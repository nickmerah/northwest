<?php

namespace App\Observers;

use App\Helpers\AccountHelper;
use App\Models\Admissions\AppProfile;
use Illuminate\Support\Facades\Cache;

class AppProfileObserver
{
    /**
     * Handle the AppProfile "updated" event.
     */
    public function updated(AppProfile $appProfile): void
    {
        $appProfile =  $appProfile->fresh();
        $accountHelper = app(AccountHelper::class);
        $data = $accountHelper->cacheApplicantData($appProfile->std_logid);
        Cache::put("applicant:{$appProfile->std_logid}", $data, now()->addHour());
    }
}
