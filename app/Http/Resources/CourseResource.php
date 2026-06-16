<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\CourseStatus;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

final class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->whenLoaded('category'),
            'is_active' => $this->is_active,
            'price' => $this->price,
            'is_full' => $this->is_full,
            'course_duration' => $this->course_duration,
            'capacity' => $this->capacity,
            'cancellation_fee' => $this->cancellation_fee,
            'start_date' => $this->start_date,
            'status' => CourseStatus::from($this->status)->name,
            'rate' => $this->rate,
            ...$this->extras(),
        ];
    }

    private function extras(): array
    {
        $user = Auth::user();
        $isOwner = $user?->id === $this->user_id;
        $isCustomer = $user instanceof Customer;

        return [
            'attendees' => $this->when(
                $isOwner && $this->relationLoaded('customers'),
                fn () => $this->customers->count()
            ),
            'customers' => $this->when(
                $isOwner && $this->relationLoaded('customers'),
                fn () => $this->customers->map(function ($customer) {
                    return [
                        'name' => $customer->name,
                        'profile_image' => optional($customer->mediaByName('profile_image'), function ($media) {
                            return MediaResource::make($media);
                        }),
                        'remaining_sessions' => $customer->pivot->remaining_sessions,
                        'is_complete' => $customer->pivot->is_complete,
                        'attended_at' => $customer->pivot->created_at,
                    ];
                })
            ),
            'customer' => $this->when(
                $isCustomer && $this->relationLoaded('isAttending') && count($this->isAttending) > 0,
                function () {
                    return [
                        'remaining_sessions' => $this->pivot->remaining_sessions,
                        'is_complete' => $this->pivot->is_complete,
                    ];
                }
            ),
            'is_favorite' => $this->when(
                ! $isOwner && $isCustomer && $this->relationLoaded('isFavorite'),
                fn () => (bool) count($this->isFavorite)
            ),
            'is_attending' => $this->when(
                ! $isOwner && $isCustomer && $this->relationLoaded('isAttending'),
                fn () => (bool) count($this->isAttending)
            ),
            'details' => $this->when($isOwner && $this->relationLoaded('pivot'), fn () => $this->pivot),
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
