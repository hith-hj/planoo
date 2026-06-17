<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\CourseStatus;
use App\Models\Customer;
use App\Models\User;
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
            'court' => $this->whenLoaded('court'),
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
        $isPartner = $user instanceof User;

        $showPartnerData = $isPartner && $isOwner;
        $showCustomerData = ! $isPartner && $isCustomer;

        $attending = $showCustomerData && $this->relationLoaded('isAttending')
            ? $this->isAttending->first()
            : null;

        return [
            'attendees' => $this->when(
                $showPartnerData && $this->relationLoaded('customers'),
                fn () => $this->customers->count()
            ),
            'customers' => $this->when(
                $showPartnerData && $this->relationLoaded('customers'),
                fn () => $this->customers->map(fn ($customer) => [
                    'name' => $customer->name,
                    'profile_image' => optional(
                        $customer->mediaByName('profile_image'),
                        fn ($media) => MediaResource::make($media)
                    ),
                    'remaining_sessions' => $customer->pivot->remaining_sessions,
                    'is_complete' => $customer->pivot->is_complete,
                    'attended_at' => $customer->pivot->created_at,
                ])
            ),
            'customer' => $this->when(
                (bool) $attending,
                fn () => [
                    'remaining_sessions' => $attending->pivot->remaining_sessions,
                    'is_complete' => $attending->pivot->is_complete,
                ]
            ),
            'is_favorite' => $this->when(
                $showCustomerData && $this->relationLoaded('isFavorite'),
                fn () => $this->isFavorite->isNotEmpty()
            ),
            'is_attending' => $this->when(
                $showCustomerData && $this->relationLoaded('isAttending'),
                fn () => (bool) $attending
            ),
            'details' => $this->when($showPartnerData && $this->relationLoaded('pivot'), fn () => $this->pivot),
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
