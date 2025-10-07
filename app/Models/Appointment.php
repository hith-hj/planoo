<?php

namespace App\Models;

use App\Observers\AppointmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory;

    public function holder(): MorphTo
    {
        return $this->morphTo(__METHOD__, 'appointable_type', 'appointable_id');
    }

    public function scopeOwner(Builder $query, string $owner_class, ?int $owner_id)
    {
        return $query->where([['appointable_type', $owner_class], ['appointable_id', $owner_id],]);
    }

    // public function customer():BelongsTo
    // {
    //     return $this->belongsTo(Customer::class);
    // }

}
