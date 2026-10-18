<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Jobs\DeleteMediaFile;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class CustomerServices
{
    public function get(mixed $id): Customer
    {
        Required($id, 'customer id');
        $customer = Customer::find($id);
        NotFound($customer, 'customer');

        return $customer->load(['medias']);
    }

    public function createIfNotExists(array $data): Customer
    {
        $data = checkAndCastData($data, ['phone' => 'string']);
        $customer = Customer::where([
            ['phone', $data['phone']],
            ['country_code', $data['country_code']],
        ])->first();
        if ($customer) {
            return $customer;
        }
        $data['name'] = $this->userName($data);
        $data['password'] = 'Pass@'.$data['phone'];
        $data['firebase_token'] = 'not-set';

        return $this->create($data);
    }

    public function find(mixed $id): Customer
    {
        Required($id, 'customer id');
        $customer = Customer::find($id);
        NotFound($customer, 'customer');

        return $customer;
    }

    public function create(array $data): Customer
    {
        $data = checkAndCastData($data, [
            'phone' => 'string',
            'name' => 'string',
            'password' => 'string',
            'firebase_token' => 'string',
        ]);

        /** @var Customer $customer */
        $customer = Customer::create([
            'name' => $this->userName($data),
            'country_code' => $data['country_code'],
            'phone' => formatPhone($data['phone']),
            'password' => bcrypt($data['password']),
            'status' => AccountStatus::fresh->value,
            'firebase_token' => $data['firebase_token'],
            'is_active' => true,
            'is_notifiable' => true,
        ]);

        $customer->fresh()->verify(by: $data['by'] ?? 'fcm');

        return $customer;
    }

    public function getCustomer(array $data)
    {
        $customer = null;
        if (isset($data['customer_id']) || isset($data['customer_phone'], $data['country_code'])) {
            if (isset($data['customer_phone'], $data['country_code'])) {
                $customer = $this->createIfNotExists([
                    'country_code' => $data['country_code'],
                    'phone' => $data['customer_phone'],
                ]);
            }
            if (isset($data['customer_id'])) {
                $customer = $this->find($data['customer_id']);
            }
        }
        NotFound($customer, 'customer');

        return $customer;
    }

    public function update(Customer $customer, array $data): bool
    {
        Required($data, 'data');

        return $customer->update($data);
    }

    public function delete(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer): bool {
            $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            $courses = Course::query()->whereIn('id', $customer->courses()->select('courses.id'))
                ->orderBy('id')->lockForUpdate()->get();
            $events = Event::query()->whereIn('id', $customer->events()->select('events.id'))
                ->orderBy('id')->lockForUpdate()->get();
            foreach ($customer->medias()->get() as $media) {
                $cleanup = str_starts_with($media->url, 'uploads/') ? new DeleteMediaFile($media->url) : null;
                // The cleanup job removes the source before synchronizing its deletion.
                $media->deleteQuietly();
                if ($cleanup) {
                    DB::afterCommit(fn () => dispatch($cleanup));
                }
            }
            $customer->location()->delete();
            $customer->courses()->detach();
            $customer->events()->detach();
            foreach ($courses->concat($events) as $group) {
                $group->update(['is_full' => $group->customers()->count() >= $group->capacity]);
            }
            $customer->appointments()->delete();
            $customer->notifications()->delete();
            $customer->codes()->delete();
            $customer->favorites()->delete();
            $customer->reviews()->delete();

            return $customer->delete();
        }, 3);
    }

    public function uploadProfileImage(Customer $customer, array $data)
    {
        $oldMedia = $customer->mediaByName('profile_image');
        if ($oldMedia !== null) {
            $this->deleteProfileImage($oldMedia);
        }

        return $customer->uploadMedia('image', 'profile_image', $data['profile_image']);
    }

    public function deleteProfileImage(Media $media)
    {
        NotFound($media, 'media');
        Storage::disk('public')->delete($media->url);

        return $media->delete();
    }

    public function toggleNotification(Customer $customer)
    {
        return $customer->update(['is_notifiable' => ! $customer->is_notifiable]);
    }

    private function userName(array $data): string
    {
        if (isset($data['name'])) {
            return 'usr_'.$data['name'];
        }

        if (isset($data['phone'])) {
            return 'usr_ph_'.mb_substr($data['phone'], -5);
        }

        return 'usr_rd_'.random_int(1000000, 9000000);
    }
}
