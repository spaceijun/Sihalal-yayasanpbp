<?php
namespace App\Observers;

use App\Models\Cashflow;
use App\Helpers\NotificationHelper;

class CashflowObserver
{
    public function created(Cashflow $cashflow): void
    {
        // Ambil user pemilik data cashflow
        $user = $cashflow->dataLapangan?->user;
        if (!$user) return;

        NotificationHelper::kirimPemasukan(
            $user,
            (double) $cashflow->nominal
        );
    }
}
