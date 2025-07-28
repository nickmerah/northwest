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
use App\Interfaces\ResultsRepositoryInterface;
use App\Interfaces\SchoolSettingsRepositoryInterface;
use App\Repositories\ResultRepository;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $bindings = [
            SchoolSettingsRepositoryInterface::class => SchoolSettingsRepository::class,
            AccountRepositoryInterface::class => AccountRepository::class,
            ApplicantRepositoryInterface::class => ApplicantRepository::class,
            PaymentRepositoryInterface::class => PaymentRepository::class,
            ProfileRepositoryInterface::class => ProfileRepository::class,
            ResultsRepositoryInterface::class => ResultRepository::class,
        ];

        foreach ($bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }


    public function boot(): void
    {
        AppProfile::observe(AppProfileObserver::class);
        View::share('SCHOOLNAME', config('school.name'));
    }
}
