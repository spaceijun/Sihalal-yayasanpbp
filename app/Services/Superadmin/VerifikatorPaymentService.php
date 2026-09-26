<?php

namespace App\Services\Superadmin;

use App\Models\VerifikatorPayment;
use Illuminate\Support\Facades\DB;

class VerifikatorPaymentService
{
    public function store(array $data): VerifikatorPayment
    {
        return DB::transaction(function () use ($data) {
            return VerifikatorPayment::create($data);
        });
    }

    public function update(VerifikatorPayment $verifikatorPayment, array $data): VerifikatorPayment
    {
        return DB::transaction(function () use ($verifikatorPayment, $data) {
            $verifikatorPayment->update($data);

            return $verifikatorPayment->fresh();
        });
    }

    public function delete(VerifikatorPayment $verifikatorPayment): void
    {
        DB::transaction(function () use ($verifikatorPayment) {
            $verifikatorPayment->delete();
        });
    }
}
