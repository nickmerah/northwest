<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Repositories\AccountRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ApplicantRepository;
use App\Repositories\SchoolSettingsRepository;
use App\Repositories\AccountRepositoryInterface;
use App\Repositories\ApplicantRepositoryInterface;
use App\Repositories\SchoolSettingsRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(SchoolSettingsRepositoryInterface::class, SchoolSettingsRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(ApplicantRepositoryInterface::class, ApplicantRepository::class);
    }


    public function boot(): void
    {
        View::share('SCHOOLNAME', 'Delta State Polytechnic, Ogwashi-Uku');
    }
}
