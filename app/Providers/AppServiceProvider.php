<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Candidate;
use App\Models\Offer;
use App\Models\Offre;
use App\Policies\CandidatePolicy;
use App\Policies\OfferPolicy;
use App\Policies\OffrePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
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
        Route::model('candidature', Candidate::class);

        Gate::policy(Offre::class, OffrePolicy::class);
        Gate::policy(Offer::class, OfferPolicy::class);
        Gate::policy(Candidate::class, CandidatePolicy::class);
    }
}
