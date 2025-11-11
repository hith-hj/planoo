<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CustomerResource extends JsonResource
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
            // 'phone' => $this->phone,
            'status' => $this->status,
            'is_verified' => $this->verified_at,
            'is_notifiable' => $this->is_notifiable,
            'is_active' => $this->is_active,
            'profile_image' => MediaResource::make($this->mediaByName('profile_image')),
        ];
    }
}
