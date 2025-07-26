<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\Admissions\AppProfile;
use App\Observers\AppProfileObserver;
use App\Repositories\AccountRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProfileRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ApplicantRepository;
use App\Interfaces\AccountRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\ProfileRepositoryInterface;
use App\Repositories\SchoolSettingsRepository;
use App\Interfaces\ApplicantRepositoryInterface;
use App\Interfaces\SchoolSettingsRepositoryInterface;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(SchoolSettingsRepositoryInterface::class, SchoolSettingsRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(ApplicantRepositoryInterface::class, ApplicantRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
    }


    public function boot(): void
    {
        AppProfile::observe(AppProfileObserver::class);
        View::share('SCHOOLNAME', 'NorthWest School of Nursing, Katsina');
    }
}
