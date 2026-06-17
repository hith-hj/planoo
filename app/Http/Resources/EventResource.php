<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\EventStatus;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

final class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $endDate = $this->event_duration > 1
            ? Carbon::parse($this->start_date)->addDays($this->event_duration)->toDateString()
            : $this->start_date;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->whenLoaded('category'),
            'is_active' => $this->is_active,
            'is_full' => $this->is_full,
            'capacity' => $this->capacity,
            'admission_fee' => $this->admission_fee,
            'withdrawal_fee' => $this->withdrawal_fee,
            'event_duration' => $this->event_duration,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rate' => $this->rate,
            'status' => EventStatus::from($this->status)->name,
            ...$this->extras(),
        ];
    }

    private function extras(): array
    {
        $user = Auth::user();
        $isOwner = $user?->id === $this->user_id;
        $isCustomer = $user instanceof Customer;

        $showOwnerData = $isOwner && $this->relationLoaded('customers');
        $showCustomerData = ! $isOwner && $isCustomer;

        return [
            'attendees' => $this->when(
                $showOwnerData,
                fn () => $this->customers->count()
            ),
            'customers' => $this->when(
                $showOwnerData,
                fn () => $this->customers->map(fn ($customer) => [
                    'name' => $customer->name,
                    'profile_image' => optional(
                        $customer->mediaByName('profile_image'),
                        fn ($media) => MediaResource::make($media)
                    ),
                    'attended_at' => $customer->pivot?->created_at,
                ])
            ),
            'is_favorite' => $this->when(
                $showCustomerData && $this->relationLoaded('isFavorite'),
                fn () => $this->isFavorite->isNotEmpty()
            ),
            'is_attending' => $this->when(
                $showCustomerData && $this->relationLoaded('isAttending'),
                fn () => $this->isAttending->isNotEmpty()
            ),
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
