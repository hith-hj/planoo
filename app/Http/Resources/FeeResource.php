<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\FeeTypes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FeeResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'subject_type' => class_basename($this->subject_type),
            'amount' => $this->amount,
            'delay_fee' => $this->delay_fee,
            'type' => FeeTypes::from($this->type)->name,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
        ];
    }
}
