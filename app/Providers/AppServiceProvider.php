<?php

namespace App\Providers;

use App\Models\Manual;
use App\Models\SitePage;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        Relation::morphMap([
            'manual' => Manual::class,
            'category' => SubCategory::class,
            'site_page' => SitePage::class,
        ]);
    }
}
