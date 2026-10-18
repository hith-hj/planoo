<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\LabelIconObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy(LabelIconObserver::class)]
final class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'icon',
    ];

    protected static function booted(): void
    {
        self::saved(function (): void {
            cache()->forget('labels.tags');
        });
        self::deleted(function (): void {
            cache()->forget('labels.tags');
        });
    }

    public function activities(): MorphToMany
    {
        return $this->morphedByMany(Activity::class, 'taggable');
    }

    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'taggable');
    }

    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, 'taggable');
    }
}
