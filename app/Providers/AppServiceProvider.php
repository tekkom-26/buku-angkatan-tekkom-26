<?php

namespace App\Providers;

use App\Services\AppConfigService;
use App\Services\StudentDirectoryService;
use App\Services\WebContentService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(AppConfigService::class);
        $this->app->scoped(WebContentService::class);
        $this->app->scoped(StudentDirectoryService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('staging', 'production') || env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view): void {
            try {
                $cohortName = app(AppConfigService::class)->cohortName();
            } catch (Throwable) {
                $cohortName = config('content.cohort_name', 'TEKNIK KOMPUTER 2026');
            }

            $view->with('cohortName', $cohortName);
        });
    }
}
