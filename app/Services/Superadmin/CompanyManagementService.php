<?php

namespace App\Services\Superadmin;

use App\Models\ArticleCategory;
use App\Models\CompanyBenefit;
use App\Models\CompanyHistory;
use App\Models\CompanyStatistic;
use App\Models\CompanyTeam;
use App\Models\SocialMedia;
use App\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyManagementService
{
    // ==================== STATISTICS ====================

    public function storeStatistic(array $data): CompanyStatistic
    {
        return DB::transaction(function () use ($data) {
            $data['sort_order'] = CompanyStatistic::max('sort_order') + 1;
            $data['is_active'] = true;

            return CompanyStatistic::create($data);
        });
    }

    public function updateStatistic(CompanyStatistic $statistic, array $data): CompanyStatistic
    {
        return DB::transaction(function () use ($statistic, $data) {
            $statistic->update($data);

            return $statistic->fresh();
        });
    }

    public function deleteStatistic(CompanyStatistic $statistic): void
    {
        DB::transaction(function () use ($statistic) {
            $statistic->delete();
        });
    }

    public function toggleStatistic(CompanyStatistic $statistic): CompanyStatistic
    {
        return DB::transaction(function () use ($statistic) {
            $statistic->update(['is_active' => ! $statistic->is_active]);

            return $statistic->fresh();
        });
    }

    // ==================== BENEFITS ====================

    public function storeBenefit(array $data): CompanyBenefit
    {
        return DB::transaction(function () use ($data) {
            $data['sort_order'] = CompanyBenefit::max('sort_order') + 1;
            $data['is_active'] = true;

            return CompanyBenefit::create($data);
        });
    }

    public function updateBenefit(CompanyBenefit $benefit, array $data): CompanyBenefit
    {
        return DB::transaction(function () use ($benefit, $data) {
            $benefit->update($data);

            return $benefit->fresh();
        });
    }

    public function deleteBenefit(CompanyBenefit $benefit): void
    {
        DB::transaction(function () use ($benefit) {
            $benefit->delete();
        });
    }

    public function toggleBenefit(CompanyBenefit $benefit): CompanyBenefit
    {
        return DB::transaction(function () use ($benefit) {
            $benefit->update(['is_active' => ! $benefit->is_active]);

            return $benefit->fresh();
        });
    }

    // ==================== TESTIMONIALS ====================

    public function storeTestimonial(array $data, ?UploadedFile $photo = null): Testimonial
    {
        return DB::transaction(function () use ($data, $photo) {
            if ($photo) {
                $data['photo'] = $photo->store('testimonials', 'public');
            }

            $data['sort_order'] = Testimonial::max('sort_order') + 1;
            $data['is_active'] = true;
            $data['rating'] = $data['rating'] ?? 5;

            return Testimonial::create($data);
        });
    }

    public function updateTestimonial(Testimonial $testimonial, array $data, ?UploadedFile $photo = null): Testimonial
    {
        return DB::transaction(function () use ($testimonial, $data, $photo) {
            if ($photo) {
                $data['photo'] = $photo->store('testimonials', 'public');
            }

            $testimonial->update($data);

            return $testimonial->fresh();
        });
    }

    public function deleteTestimonial(Testimonial $testimonial): void
    {
        DB::transaction(function () use ($testimonial) {
            $testimonial->delete();
        });
    }

    public function toggleTestimonial(Testimonial $testimonial): Testimonial
    {
        return DB::transaction(function () use ($testimonial) {
            $testimonial->update(['is_active' => ! $testimonial->is_active]);

            return $testimonial->fresh();
        });
    }

    // ==================== TEAMS ====================

    public function storeTeam(array $data, ?UploadedFile $photo = null): CompanyTeam
    {
        return DB::transaction(function () use ($data, $photo) {
            if ($photo) {
                $data['photo'] = $photo->store('teams', 'public');
            }

            $data['sort_order'] = CompanyTeam::max('sort_order') + 1;
            $data['is_active'] = true;

            return CompanyTeam::create($data);
        });
    }

    public function updateTeam(CompanyTeam $team, array $data, ?UploadedFile $photo = null): CompanyTeam
    {
        return DB::transaction(function () use ($team, $data, $photo) {
            if ($photo) {
                $data['photo'] = $photo->store('teams', 'public');
            }

            $team->update($data);

            return $team->fresh();
        });
    }

    public function deleteTeam(CompanyTeam $team): void
    {
        DB::transaction(function () use ($team) {
            $team->delete();
        });
    }

    public function toggleTeam(CompanyTeam $team): CompanyTeam
    {
        return DB::transaction(function () use ($team) {
            $team->update(['is_active' => ! $team->is_active]);

            return $team->fresh();
        });
    }

    // ==================== HISTORIES ====================

    public function storeHistory(array $data): CompanyHistory
    {
        return DB::transaction(function () use ($data) {
            $data['sort_order'] = CompanyHistory::max('sort_order') + 1;

            return CompanyHistory::create($data);
        });
    }

    public function updateHistory(CompanyHistory $history, array $data): CompanyHistory
    {
        return DB::transaction(function () use ($history, $data) {
            $history->update($data);

            return $history->fresh();
        });
    }

    public function deleteHistory(CompanyHistory $history): void
    {
        DB::transaction(function () use ($history) {
            $history->delete();
        });
    }

    // ==================== CATEGORIES ====================

    public function storeCategory(array $data): ArticleCategory
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['name']);

            return ArticleCategory::create($data);
        });
    }

    public function updateCategory(ArticleCategory $category, array $data): ArticleCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);

            return $category->fresh();
        });
    }

    public function deleteCategory(ArticleCategory $category): void
    {
        DB::transaction(function () use ($category) {
            $category->delete();
        });
    }

    // ==================== SOCIAL MEDIA ====================

    public function storeSocialMedia(array $data): SocialMedia
    {
        return DB::transaction(function () use ($data) {
            $data['sort_order'] = SocialMedia::max('sort_order') + 1;
            $data['is_active'] = true;

            return SocialMedia::create($data);
        });
    }

    public function updateSocialMedia(SocialMedia $socialMedia, array $data): SocialMedia
    {
        return DB::transaction(function () use ($socialMedia, $data) {
            $socialMedia->update($data);

            return $socialMedia->fresh();
        });
    }

    public function deleteSocialMedia(SocialMedia $socialMedia): void
    {
        DB::transaction(function () use ($socialMedia) {
            $socialMedia->delete();
        });
    }

    public function toggleSocialMedia(SocialMedia $socialMedia): SocialMedia
    {
        return DB::transaction(function () use ($socialMedia) {
            $socialMedia->update(['is_active' => ! $socialMedia->is_active]);

            return $socialMedia->fresh();
        });
    }
}
