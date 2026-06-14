<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyBenefit;
use App\Models\CompanyHistory;
use App\Models\CompanyStatistic;
use App\Models\CompanyTeam;
use App\Models\Testimonial;
use App\Models\SocialMedia;
use App\Models\ArticleCategory;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyManagementController extends Controller
{
    use HasRoutePrefix;

    /**
     * Display company profile management page
     */
    public function index()
    {
        $pages = \App\Models\CompanyProfile::with('sections')->get();
        $stats = CompanyStatistic::orderBy('sort_order')->get();
        $benefits = CompanyBenefit::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();
        $teams = CompanyTeam::orderBy('sort_order')->get();
        $histories = CompanyHistory::orderBy('year', 'desc')->get();
        $categories = ArticleCategory::orderBy('name')->get();
        $socialMedia = SocialMedia::orderBy('sort_order')->get();
        $routePrefix = $this->routePrefix();

        return view('superadmin.company-profile.index', compact(
            'pages', 'stats', 'benefits', 'testimonials', 'teams', 'histories', 'categories', 'socialMedia', 'routePrefix'
        ));
    }

    // ==================== STATISTICS ====================

    public function storeStatistic(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'value' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $validated['sort_order'] = CompanyStatistic::max('sort_order') + 1;
        $validated['is_active'] = true;

        CompanyStatistic::create($validated);

        return back()->with('success', 'Statistik berhasil ditambahkan.');
    }

    public function updateStatistic(Request $request, $id)
    {
        $stat = CompanyStatistic::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'value' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $stat->update($validated);

        return back()->with('success', 'Statistik berhasil diperbarui.');
    }

    public function destroyStatistic($id)
    {
        CompanyStatistic::findOrFail($id)->delete();

        return back()->with('success', 'Statistik berhasil dihapus.');
    }

    // ==================== BENEFITS ====================

    public function storeBenefit(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['sort_order'] = CompanyBenefit::max('sort_order') + 1;
        $validated['is_active'] = true;

        CompanyBenefit::create($validated);

        return back()->with('success', 'Keunggulan berhasil ditambahkan.');
    }

    public function updateBenefit(Request $request, $id)
    {
        $benefit = CompanyBenefit::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $benefit->update($validated);

        return back()->with('success', 'Keunggulan berhasil diperbarui.');
    }

    public function destroyBenefit($id)
    {
        CompanyBenefit::findOrFail($id)->delete();

        return back()->with('success', 'Keunggulan berhasil dihapus.');
    }

    // ==================== TESTIMONIALS ====================

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'testimonial' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $validated['sort_order'] = Testimonial::max('sort_order') + 1;
        $validated['is_active'] = true;
        $validated['rating'] = $validated['rating'] ?? 5;

        Testimonial::create($validated);

        return back()->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function updateTestimonial(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'testimonial' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return back()->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroyTestimonial($id)
    {
        Testimonial::findOrFail($id)->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }

    // ==================== TEAMS ====================

    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'description' => 'nullable|string',
            'linkedin' => 'nullable|url',
            'twitter' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('teams', 'public');
        }

        $validated['sort_order'] = CompanyTeam::max('sort_order') + 1;
        $validated['is_active'] = true;

        CompanyTeam::create($validated);

        return back()->with('success', 'Tim berhasil ditambahkan.');
    }

    public function updateTeam(Request $request, $id)
    {
        $team = CompanyTeam::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'description' => 'nullable|string',
            'linkedin' => 'nullable|url',
            'twitter' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('teams', 'public');
        }

        $team->update($validated);

        return back()->with('success', 'Tim berhasil diperbarui.');
    }

    public function destroyTeam($id)
    {
        CompanyTeam::findOrFail($id)->delete();

        return back()->with('success', 'Tim berhasil dihapus.');
    }

    // ==================== HISTORIES ====================

    public function storeHistory(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'description' => 'nullable|string',
        ]);

        $validated['sort_order'] = CompanyHistory::max('sort_order') + 1;

        CompanyHistory::create($validated);

        return back()->with('success', 'Riwayat berhasil ditambahkan.');
    }

    public function updateHistory(Request $request, $id)
    {
        $history = CompanyHistory::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'description' => 'nullable|string',
        ]);

        $history->update($validated);

        return back()->with('success', 'Riwayat berhasil diperbarui.');
    }

    public function destroyHistory($id)
    {
        CompanyHistory::findOrFail($id)->delete();

        return back()->with('success', 'Riwayat berhasil dihapus.');
    }

    // ==================== CATEGORIES ====================

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        ArticleCategory::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = ArticleCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        ArticleCategory::findOrFail($id)->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // ==================== SOCIAL MEDIA ====================

    public function storeSocialMedia(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $validated['sort_order'] = SocialMedia::max('sort_order') + 1;
        $validated['is_active'] = true;

        SocialMedia::create($validated);

        return back()->with('success', 'Social media berhasil ditambahkan.');
    }

    public function updateSocialMedia(Request $request, $id)
    {
        $social = SocialMedia::findOrFail($id);

        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $social->update($validated);

        return back()->with('success', 'Social media berhasil diperbarui.');
    }

    public function destroySocialMedia($id)
    {
        SocialMedia::findOrFail($id)->delete();

        return back()->with('success', 'Social media berhasil dihapus.');
    }

    // ==================== TOGGLE STATUS ====================

    public function toggleStatistic($id)
    {
        $stat = CompanyStatistic::findOrFail($id);
        $stat->update(['is_active' => !$stat->is_active]);

        return back()->with('success', 'Status statistik berhasil diubah.');
    }

    public function toggleBenefit($id)
    {
        $benefit = CompanyBenefit::findOrFail($id);
        $benefit->update(['is_active' => !$benefit->is_active]);

        return back()->with('success', 'Status keunggulan berhasil diubah.');
    }

    public function toggleTestimonial($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return back()->with('success', 'Status testimoni berhasil diubah.');
    }

    public function toggleTeam($id)
    {
        $team = CompanyTeam::findOrFail($id);
        $team->update(['is_active' => !$team->is_active]);

        return back()->with('success', 'Status tim berhasil diubah.');
    }

    public function toggleSocialMedia($id)
    {
        $social = SocialMedia::findOrFail($id);
        $social->update(['is_active' => !$social->is_active]);

        return back()->with('success', 'Status social media berhasil diubah.');
    }
}
