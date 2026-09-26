<?php

namespace App\Services\Superadmin;

use App\Models\CompanyProfile;
use App\Models\PageSection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyProfileService
{
    /**
     * Update page settings (title/meta) for a company profile page.
     */
    public function updatePage(CompanyProfile $profile, array $data): CompanyProfile
    {
        return DB::transaction(function () use ($profile, $data) {
            $profile->update($data);

            return $profile->fresh();
        });
    }

    /**
     * Store a new section under the given company profile page.
     */
    public function storeSection(CompanyProfile $profile, array $data, ?UploadedFile $image = null): PageSection
    {
        return DB::transaction(function () use ($profile, $data, $image) {
            if ($image) {
                $data['image'] = $image->store('company-profile', 'public');
            }

            $data['company_profile_id'] = $profile->id;
            $data['is_active'] = $data['is_active'] ?? true;

            if (isset($data['extra_data'])) {
                $data['extra_data'] = json_encode($data['extra_data']);
            }

            return PageSection::create($data);
        });
    }

    /**
     * Update an existing section.
     */
    public function updateSection(PageSection $section, array $data, ?UploadedFile $image = null): PageSection
    {
        return DB::transaction(function () use ($section, $data, $image) {
            if ($image) {
                if ($section->image) {
                    Storage::disk('public')->delete($section->image);
                }

                $data['image'] = $image->store('company-profile', 'public');
            }

            $data['is_active'] = $data['is_active'] ?? $section->is_active;

            if (isset($data['extra_data'])) {
                $data['extra_data'] = json_encode($data['extra_data']);
            }

            $section->update($data);

            return $section->fresh();
        });
    }

    /**
     * Delete a section (and its image, if any).
     */
    public function deleteSection(PageSection $section): void
    {
        DB::transaction(function () use ($section) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }

            $section->delete();
        });
    }

    /**
     * Toggle a section's active status.
     */
    public function toggleSection(PageSection $section): PageSection
    {
        return DB::transaction(function () use ($section) {
            $section->update(['is_active' => ! $section->is_active]);

            return $section->fresh();
        });
    }
}
