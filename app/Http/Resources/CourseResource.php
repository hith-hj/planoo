<?php

declare(strict_types=1);

namespace App\Http\Resources;

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
            'session_duration' => $this->session_duration,
            'course_duration' => $this->course_duration,
            'capacity' => $this->capacity,
            'cancellation_fee' => $this->cancellation_fee,
            'rate' => $this->rate,
            ...$this->exrtas(),
        ];
    }

    private function exrtas(): array
    {
        $isOwner = Auth::id() === $this->user_id;
        $isCustomer = Auth::user() instanceof Customer;

        return [
            'customers' => $this->when(
                $isOwner,
                CustomerResource::collection($this->whenLoaded('customers'))
            ),
            'is_favorite' => (bool) $this->when(
                ! $isOwner && $isCustomer,
                count($this->whenLoaded('isFavorite', default: []))
            ),
            'details' => $this->when($isOwner, $this->whenLoaded('pivot')),
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
