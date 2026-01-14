<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\AppointmentStatus;
use App\Enums\SectionsTypes;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

final class AppointmentResource extends JsonResource
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
            'status' => AppointmentStatus::from($this->status)->name,
            'price' => $this->price,
            'session_duration' => $this->session_duration,
            'canceled_by' => $this->canceled_by,
            'notes' => $this->notes,
            'holder' => $this->holder(),
            'customer' => $this->customer(),
        ];
    }

    private function holder()
    {
        if (! $this->holder) {
            return [
                'type' => $this->appointable_type,
                'id' => $this->appointable_id,
            ];
        }

        $this->holder->load(['category', 'medias']);

        return [
            'type' => class_basename($this->appointable_type),
            'id' => $this->appointable_id,
            'name' => $this->holder->name,
            'category' => $this->holder->category,
            'rate' => $this->holder->rate,
            'description' => $this->holder->description,
            'image' => $this->holder->medias[0],
            ...$this->getHolderDetails(class_basename($this->appointable_type)),
        ];
    }

    private function customer()
    {
        //if appointment belong to customer show customer info
        if(Auth::user() instanceof Customer && $this->customer_id == Auth::id() ){
            return CustomerResource::make($this->whenLoaded('customer'));
        }
        //if appointment belong to partner show customer info
        if(Auth::user() instanceof User ){
            if ($this->holder && $this->holder->user_id == Auth::id()) {
                return CustomerResource::make($this->whenLoaded('customer'));
            }
        }
        return null;
    }

    private function getHolderDetails(string $type)
    {
        return match (mb_strtolower($type)) {
            SectionsTypes::activity->name => $this->activity(),
            SectionsTypes::course->name => $this->course(),
            SectionsTypes::event->name => $this->event(),
            default => [],
        };
    }

    private function activity()
    {
        return [
            'price' => $this->holder->price,
        ];
    }

    private function course()
    {
        return [
            'price' => $this->holder->price,
        ];
    }

    private function event()
    {
        return [
            'price' => $this->holder->admission_fee,
        ];
    }
}
