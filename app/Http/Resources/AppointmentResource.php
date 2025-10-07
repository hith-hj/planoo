<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'price' => $this->price,
            'session_duration' => $this->session_duration,
            'canceled_by' => $this->canceled_by,
            'notes' => $this->notes,
        ];
    }
}
