<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Favorite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'favoritable_type',
        'favoritable_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function holder()
    {
        return $this->morphTo(__FUNCTION__, 'favoritable_type', 'favoritable_id');
    }
}
