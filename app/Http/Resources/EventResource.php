<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\EventStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
            'status' => EventStatus::from($this->status)->name,
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'customers' => CustomerResource::collection($this->whenLoaded('customers')),
            'details' => $this->whenLoaded('pivot'),
        ];
    }
}
