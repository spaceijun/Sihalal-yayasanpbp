<?php

namespace App\Services\Superadmin;

use App\Models\Cashflow;
use Illuminate\Support\Facades\DB;

class CashflowService
{
    public function store(array $data): Cashflow
    {
        return DB::transaction(function () use ($data) {
            return Cashflow::create($data);
        });
    }

    public function update(Cashflow $cashflow, array $data): Cashflow
    {
        return DB::transaction(function () use ($cashflow, $data) {
            $cashflow->update($data);

            return $cashflow->fresh();
        });
    }

    public function delete(Cashflow $cashflow): void
    {
        DB::transaction(function () use ($cashflow) {
            $cashflow->delete();
        });
    }
}
