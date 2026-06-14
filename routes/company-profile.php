<?php

use App\Http\Controllers\Publik\HomeController;
use App\Http\Controllers\Superadmin\ArticleController;
use App\Http\Controllers\Superadmin\CompanyManagementController;
use App\Http\Controllers\Superadmin\CompanyProfileController;
use App\Http\Controllers\Superadmin\ContactMessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Company Profile Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('')->name('publik.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/tentang-kami', [HomeController::class, 'about'])->name('about');
    Route::get('/artikel', [HomeController::class, 'articles'])->name('articles.index');
    Route::get('/artikel/{slug}', [HomeController::class, 'showArticle'])->name('articles.show');
    Route::get('/lowongan-kerja', [HomeController::class, 'recruitment'])->name('recruitment.index');
    Route::get('/lowongan-kerja/{slug}', [HomeController::class, 'showRecruitment'])->name('recruitment.show');
    Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');
    Route::post('/kontak', [HomeController::class, 'submitContact'])->name('contact.submit');
});

/*
|--------------------------------------------------------------------------
| Company Profile Routes (Superadmin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'role:superadmin|admin_umum')->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
        Route::get('company-profile/{page}/edit', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
        Route::put('company-profile/{page}', [CompanyProfileController::class, 'update'])->name('company-profile.update');
        Route::post('company-profile/{page}/sections', [CompanyProfileController::class, 'storeSection'])->name('company-profile.sections.store');
        Route::put('company-profile/{page}/sections/{sectionId}', [CompanyProfileController::class, 'updateSection'])->name('company-profile.sections.update');
        Route::delete('company-profile/{page}/sections/{sectionId}', [CompanyProfileController::class, 'destroySection'])->name('company-profile.sections.destroy');
        Route::post('company-profile/{page}/sections/{sectionId}/toggle', [CompanyProfileController::class, 'toggleSection'])->name('company-profile.sections.toggle');

        Route::get('company-content', [CompanyManagementController::class, 'index'])->name('company-content.index');
        Route::post('company-content/statistics', [CompanyManagementController::class, 'storeStatistic'])->name('company-content.statistics.store');
        Route::put('company-content/statistics/{id}', [CompanyManagementController::class, 'updateStatistic'])->name('company-content.statistics.update');
        Route::delete('company-content/statistics/{id}', [CompanyManagementController::class, 'destroyStatistic'])->name('company-content.statistics.destroy');
        Route::post('company-content/statistics/{id}/toggle', [CompanyManagementController::class, 'toggleStatistic'])->name('company-content.statistics.toggle');
        Route::post('company-content/benefits', [CompanyManagementController::class, 'storeBenefit'])->name('company-content.benefits.store');
        Route::put('company-content/benefits/{id}', [CompanyManagementController::class, 'updateBenefit'])->name('company-content.benefits.update');
        Route::delete('company-content/benefits/{id}', [CompanyManagementController::class, 'destroyBenefit'])->name('company-content.benefits.destroy');
        Route::post('company-content/benefits/{id}/toggle', [CompanyManagementController::class, 'toggleBenefit'])->name('company-content.benefits.toggle');
        Route::post('company-content/testimonials', [CompanyManagementController::class, 'storeTestimonial'])->name('company-content.testimonials.store');
        Route::put('company-content/testimonials/{id}', [CompanyManagementController::class, 'updateTestimonial'])->name('company-content.testimonials.update');
        Route::delete('company-content/testimonials/{id}', [CompanyManagementController::class, 'destroyTestimonial'])->name('company-content.testimonials.destroy');
        Route::post('company-content/testimonials/{id}/toggle', [CompanyManagementController::class, 'toggleTestimonial'])->name('company-content.testimonials.toggle');
        Route::post('company-content/teams', [CompanyManagementController::class, 'storeTeam'])->name('company-content.teams.store');
        Route::put('company-content/teams/{id}', [CompanyManagementController::class, 'updateTeam'])->name('company-content.teams.update');
        Route::delete('company-content/teams/{id}', [CompanyManagementController::class, 'destroyTeam'])->name('company-content.teams.destroy');
        Route::post('company-content/teams/{id}/toggle', [CompanyManagementController::class, 'toggleTeam'])->name('company-content.teams.toggle');
        Route::post('company-content/histories', [CompanyManagementController::class, 'storeHistory'])->name('company-content.histories.store');
        Route::put('company-content/histories/{id}', [CompanyManagementController::class, 'updateHistory'])->name('company-content.histories.update');
        Route::delete('company-content/histories/{id}', [CompanyManagementController::class, 'destroyHistory'])->name('company-content.histories.destroy');
        Route::post('company-content/categories', [CompanyManagementController::class, 'storeCategory'])->name('company-content.categories.store');
        Route::put('company-content/categories/{id}', [CompanyManagementController::class, 'updateCategory'])->name('company-content.categories.update');
        Route::delete('company-content/categories/{id}', [CompanyManagementController::class, 'destroyCategory'])->name('company-content.categories.destroy');
        Route::post('company-content/social-media', [CompanyManagementController::class, 'storeSocialMedia'])->name('company-content.social-media.store');
        Route::put('company-content/social-media/{id}', [CompanyManagementController::class, 'updateSocialMedia'])->name('company-content.social-media.update');
        Route::delete('company-content/social-media/{id}', [CompanyManagementController::class, 'destroySocialMedia'])->name('company-content.social-media.destroy');
        Route::post('company-content/social-media/{id}/toggle', [CompanyManagementController::class, 'toggleSocialMedia'])->name('company-content.social-media.toggle');

        Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');

        Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{id}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::patch('contact-messages/{id}/status', [ContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');
        Route::delete('contact-messages/{id}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
        Route::post('contact-messages/mark-all-read', [ContactMessageController::class, 'markAllRead'])->name('contact-messages.mark-all-read');
    });

/*
|--------------------------------------------------------------------------
| Company Profile Routes (Admin Umum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'role:admin_umum')->prefix('admin-umum')
    ->name('admin-umum.')
    ->group(function () {
        Route::get('company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
        Route::get('company-profile/{page}/edit', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
        Route::put('company-profile/{page}', [CompanyProfileController::class, 'update'])->name('company-profile.update');
        Route::post('company-profile/{page}/sections', [CompanyProfileController::class, 'storeSection'])->name('company-profile.sections.store');
        Route::put('company-profile/{page}/sections/{sectionId}', [CompanyProfileController::class, 'updateSection'])->name('company-profile.sections.update');
        Route::delete('company-profile/{page}/sections/{sectionId}', [CompanyProfileController::class, 'destroySection'])->name('company-profile.sections.destroy');
        Route::post('company-profile/{page}/sections/{sectionId}/toggle', [CompanyProfileController::class, 'toggleSection'])->name('company-profile.sections.toggle');

        Route::get('company-content', [CompanyManagementController::class, 'index'])->name('company-content.index');
        Route::post('company-content/statistics', [CompanyManagementController::class, 'storeStatistic'])->name('company-content.statistics.store');
        Route::put('company-content/statistics/{id}', [CompanyManagementController::class, 'updateStatistic'])->name('company-content.statistics.update');
        Route::delete('company-content/statistics/{id}', [CompanyManagementController::class, 'destroyStatistic'])->name('company-content.statistics.destroy');
        Route::post('company-content/statistics/{id}/toggle', [CompanyManagementController::class, 'toggleStatistic'])->name('company-content.statistics.toggle');
        Route::post('company-content/benefits', [CompanyManagementController::class, 'storeBenefit'])->name('company-content.benefits.store');
        Route::put('company-content/benefits/{id}', [CompanyManagementController::class, 'updateBenefit'])->name('company-content.benefits.update');
        Route::delete('company-content/benefits/{id}', [CompanyManagementController::class, 'destroyBenefit'])->name('company-content.benefits.destroy');
        Route::post('company-content/benefits/{id}/toggle', [CompanyManagementController::class, 'toggleBenefit'])->name('company-content.benefits.toggle');
        Route::post('company-content/testimonials', [CompanyManagementController::class, 'storeTestimonial'])->name('company-content.testimonials.store');
        Route::put('company-content/testimonials/{id}', [CompanyManagementController::class, 'updateTestimonial'])->name('company-content.testimonials.update');
        Route::delete('company-content/testimonials/{id}', [CompanyManagementController::class, 'destroyTestimonial'])->name('company-content.testimonials.destroy');
        Route::post('company-content/testimonials/{id}/toggle', [CompanyManagementController::class, 'toggleTestimonial'])->name('company-content.testimonials.toggle');
        Route::post('company-content/teams', [CompanyManagementController::class, 'storeTeam'])->name('company-content.teams.store');
        Route::put('company-content/teams/{id}', [CompanyManagementController::class, 'updateTeam'])->name('company-content.teams.update');
        Route::delete('company-content/teams/{id}', [CompanyManagementController::class, 'destroyTeam'])->name('company-content.teams.destroy');
        Route::post('company-content/teams/{id}/toggle', [CompanyManagementController::class, 'toggleTeam'])->name('company-content.teams.toggle');
        Route::post('company-content/histories', [CompanyManagementController::class, 'storeHistory'])->name('company-content.histories.store');
        Route::put('company-content/histories/{id}', [CompanyManagementController::class, 'updateHistory'])->name('company-content.histories.update');
        Route::delete('company-content/histories/{id}', [CompanyManagementController::class, 'destroyHistory'])->name('company-content.histories.destroy');
        Route::post('company-content/categories', [CompanyManagementController::class, 'storeCategory'])->name('company-content.categories.store');
        Route::put('company-content/categories/{id}', [CompanyManagementController::class, 'updateCategory'])->name('company-content.categories.update');
        Route::delete('company-content/categories/{id}', [CompanyManagementController::class, 'destroyCategory'])->name('company-content.categories.destroy');
        Route::post('company-content/social-media', [CompanyManagementController::class, 'storeSocialMedia'])->name('company-content.social-media.store');
        Route::put('company-content/social-media/{id}', [CompanyManagementController::class, 'updateSocialMedia'])->name('company-content.social-media.update');
        Route::delete('company-content/social-media/{id}', [CompanyManagementController::class, 'destroySocialMedia'])->name('company-content.social-media.destroy');
        Route::post('company-content/social-media/{id}/toggle', [CompanyManagementController::class, 'toggleSocialMedia'])->name('company-content.social-media.toggle');

        Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');

        Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{id}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::patch('contact-messages/{id}/status', [ContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');
        Route::delete('contact-messages/{id}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
        Route::post('contact-messages/mark-all-read', [ContactMessageController::class, 'markAllRead'])->name('contact-messages.mark-all-read');
    });
