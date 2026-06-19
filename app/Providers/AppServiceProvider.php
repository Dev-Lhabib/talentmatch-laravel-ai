<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Application;
use App\Models\Offre;
use App\Policies\OffrePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::model('candidature', Application::class);

        Gate::policy(Offre::class, OffrePolicy::class);
    }
}
