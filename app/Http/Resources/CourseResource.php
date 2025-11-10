<?php

declare(strict_types=1);

namespace App\Http\Resources;

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
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            ...$this->exrtas(),
        ];
    }

    private function exrtas(): array
    {
        $isOwner = Auth::id() === $this->user_id;

        return [
            'customers' => $this->when($isOwner, CustomerResource::collection($this->whenLoaded('customers'))),
            'details' => $this->when($isOwner, $this->whenLoaded('pivot')),
        ];
    }
}
