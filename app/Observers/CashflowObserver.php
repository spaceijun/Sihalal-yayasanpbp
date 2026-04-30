<?php

namespace App\Observers;

use App\Models\CashflowsKoordinator;
use App\Helpers\NotificationHelper;

class CashflowObserver
{
    public function created(CashflowsKoordinator $cashflow): void
    {
        $user = $cashflow->dataLapangan?->user;
        if (!$user) return;

        NotificationHelper::kirimPemasukan(
            $user,
            (float) $cashflow->nominal
        );
    }
}
