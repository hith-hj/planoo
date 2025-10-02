<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'is_outdoor' => $this->is_outdoor,
            'is_main' => $this->is_main,
            'is_active' => $this->is_active,
            'in_public' => $this->in_public,
            'is_multiPerson' => $this->is_multiPerson,
            'name' => $this->name,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'cost' => $this->cost,
            'cancellation_cost' => $this->cancellation_cost,
            'description' => $this->description,
            'workdays' => WorkdayResource::collection($this->whenLoaded('workdays')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'location' => $this->in_public ?
                LocationResource::make($this->whenLoaded('location')) :
                CourtResource::make($this->whenLoaded('court')),
            'is_filled' => $this->isFilled,
        ];
    }
}
