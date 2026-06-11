<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CourtResource extends JsonResource
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
            ...$this->extras(),
        ];
    }

    private function extras(): array
    {
        return [
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'events' => EventResource::collection($this->whenLoaded('events')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
        ];
    }
}
