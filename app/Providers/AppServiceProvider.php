<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Model::preventLazyLoading();
        Model::preventSilentlyDiscardingAttributes(! app()->environment('production'));
        if (app()->environment('production')) {
            Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation): void {
                Log::warning(sprintf(
                    'Lazy loading violation: %s::%s',
                    class_basename($model),
                    $relation
                ));
            });
        }

        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(250)->by('user:'.$request->user()->id)
                : Limit::perMinute(60)->by('ip:'.$request->ip());
        });

        FilamentTimezone::set('Asia/Damascus');
    }
}
