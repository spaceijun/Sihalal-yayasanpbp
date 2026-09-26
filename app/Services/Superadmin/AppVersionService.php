<?php

namespace App\Services\Superadmin;

use App\Models\AppVersion;
use Illuminate\Support\Facades\DB;

class AppVersionService
{
    public function store(array $data): AppVersion
    {
        return DB::transaction(function () use ($data) {
            return AppVersion::create($data);
        });
    }

    public function update(AppVersion $appVersion, array $data): AppVersion
    {
        return DB::transaction(function () use ($appVersion, $data) {
            $appVersion->update($data);

            return $appVersion->fresh();
        });
    }

    public function delete(AppVersion $appVersion): void
    {
        DB::transaction(function () use ($appVersion) {
            $appVersion->delete();
        });
    }
}
