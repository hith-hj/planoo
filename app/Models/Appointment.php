<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Observers\AppointmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy([AppointmentObserver::class])]
final class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'appointable_type',
        'appointable_id',
        'customer_id',
        'date',
        'time',
        'end_at',
        'session_duration',
        'price',
        'status',
        'notes',
        'canceled_by',
    ];

    public function holder(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'appointable_type', 'appointable_id');
    }

    public function scopeOwner(Builder $query, string $owner_class, ?int $owner_id)
    {
        return $query->where([['appointable_type', $owner_class], ['appointable_id', $owner_id]]);
    }

    public function scopeConflict(Builder $query, string $date, string $startTime, string $endTime): void
    {
        $query->where([
            ['status', AppointmentStatus::accepted->value],
            ['date',   $date],
            ['time',   '<', $endTime],
            ['end_at', '>', $startTime],
        ]);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
