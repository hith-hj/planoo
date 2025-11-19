<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\EventStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

final class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $end_date = $this->start_date;
        if ($this->event_duration > 1) {
            $end_date = Carbon::createFromDate($this->start_date)
                ->addDays($this->event_duration)
                ->toDateString();
        }

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
            'end_date' => $end_date,
            'rate' => $this->rate,
            'status' => EventStatus::from($this->status)->name,
            ...$this->exrtas(),
        ];
    }

    private function exrtas(): array
    {
        $isOwner = Auth::id() === $this->user_id;

        return [
            'customers' => $this->when($isOwner, CustomerResource::collection($this->whenLoaded('customers'))),
            'days' => DayResource::collection($this->whenLoaded('days')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'location' => LocationResource::make($this->whenLoaded('location')),
            'medias' => MediaResource::collection($this->whenLoaded('medias')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
