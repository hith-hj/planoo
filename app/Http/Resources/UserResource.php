<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'account_type' => $this->account_type,
            'description' => $this->description,
            'is_verified' => $this->verified_at,
            'is_notifiable' => $this->is_notifiable,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
