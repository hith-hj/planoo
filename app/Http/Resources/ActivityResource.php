<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->whenLoaded('category'),
            'is_active' => $this->is_active,
            'price' => $this->price,
            'session_duration' => $this->session_duration,
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
        ];
    }
}
