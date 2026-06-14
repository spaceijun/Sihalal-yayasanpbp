<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyBenefit;
use App\Models\CompanyHistory;
use App\Models\CompanyProfile;
use App\Models\CompanyStatistic;
use App\Models\CompanyTeam;
use App\Models\PageSection;
use App\Models\ArticleCategory;
use App\Models\SocialMedia;
use App\Models\Testimonial;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CompanyProfileController extends Controller
{
    use HasRoutePrefix;

    /**
     * Display a listing of company profile pages
     */
    public function index()
    {
        $pages = CompanyProfile::with('sections')->get();
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

    /**
     * Show the form for editing a company profile page
     */
    public function edit(string $page)
    {
        $profile = CompanyProfile::findByPage($page);
        $routePrefix = $this->routePrefix();

        if (!$profile) {
            return redirect()->route($routePrefix . '.company-profile.index')
                ->with('error', 'Halaman tidak ditemukan');
        }

        $sections = $profile->sections;
        $availableSections = $this->getAvailableSections($page);

        return view('superadmin.company-profile.edit', compact('profile', 'sections', 'availableSections', 'routePrefix'));
    }

    /**
     * Update company profile page settings
     */
    public function update(Request $request, string $page)
    {
        $profile = CompanyProfile::findByPage($page);

        if (!$profile) {
            return redirect()->back()->with('error', 'Halaman tidak ditemukan');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $profile->update($validated);

        return redirect()->back()->with('success', 'Pengaturan halaman berhasil disimpan');
    }

    /**
     * Store a new section
     */
    public function storeSection(Request $request, string $page)
    {
        $profile = CompanyProfile::findByPage($page);

        if (!$profile) {
            return redirect()->back()->with('error', 'Halaman tidak ditemukan');
        }

        $validated = $request->validate([
            'section_key' => [
                'required',
                'string',
                Rule::unique('page_sections')->where(function ($query) use ($profile) {
                    return $query->where('company_profile_id', $profile->id);
                }),
            ],
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'link' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'extra_data' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('company-profile', 'public');
            $validated['image'] = $path;
        }

        $validated['company_profile_id'] = $profile->id;
        $validated['is_active'] = $validated['is_active'] ?? true;

        if (isset($validated['extra_data'])) {
            $validated['extra_data'] = json_encode($validated['extra_data']);
        }

        PageSection::create($validated);

        return redirect()->back()->with('success', 'Section berhasil ditambahkan');
    }

    /**
     * Update a section
     */
    public function updateSection(Request $request, string $page, int $sectionId)
    {
        $profile = CompanyProfile::findByPage($page);
        $section = PageSection::where('id', $sectionId)
            ->where('company_profile_id', $profile?->id)
            ->first();

        if (!$section) {
            return redirect()->back()->with('error', 'Section tidak ditemukan');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'link' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'extra_data' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($section->image) {
                \Storage::disk('public')->delete($section->image);
            }
            $path = $request->file('image')->store('company-profile', 'public');
            $validated['image'] = $path;
        }

        $validated['is_active'] = $validated['is_active'] ?? $section->is_active;

        if (isset($validated['extra_data'])) {
            $validated['extra_data'] = json_encode($validated['extra_data']);
        }

        $section->update($validated);

        return redirect()->back()->with('success', 'Section berhasil diperbarui');
    }

    /**
     * Delete a section
     */
    public function destroySection(string $page, int $sectionId)
    {
        $profile = CompanyProfile::findByPage($page);
        $section = PageSection::where('id', $sectionId)
            ->where('company_profile_id', $profile?->id)
            ->first();

        if (!$section) {
            return redirect()->back()->with('error', 'Section tidak ditemukan');
        }

        // Delete image
        if ($section->image) {
            \Storage::disk('public')->delete($section->image);
        }

        $section->delete();

        return redirect()->back()->with('success', 'Section berhasil dihapus');
    }

    /**
     * Toggle section active status
     */
    public function toggleSection(string $page, int $sectionId)
    {
        $profile = CompanyProfile::findByPage($page);
        $section = PageSection::where('id', $sectionId)
            ->where('company_profile_id', $profile?->id)
            ->first();

        if (!$section) {
            return response()->json(['success' => false, 'message' => 'Section tidak ditemukan'], 404);
        }

        $section->update(['is_active' => !$section->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $section->is_active,
            'message' => $section->is_active ? 'Section diaktifkan' : 'Section dinonaktifkan',
        ]);
    }

    /**
     * Get available sections for a page
     */
    private function getAvailableSections(string $page): array
    {
        $allSections = [
            'home' => [
                'hero' => 'Hero Section',
                'stats' => 'Statistik',
                'features' => 'Fitur Layanan',
                'process' => 'Alur Proses',
                'testimonials' => 'Testimoni',
                'cta' => 'Call to Action',
                'faq' => 'FAQ',
            ],
            'about' => [
                'intro' => 'Intro',
                'mission' => 'Misi & Visi',
                'team' => 'Tim Kami',
                'history' => 'Sejarah',
                'values' => 'Nilai-nilai',
            ],
            'contact' => [
                'info' => 'Informasi Kontak',
                'map' => 'Peta Lokasi',
                'form' => 'Form Kontak',
            ],
        ];

        return $allSections[$page] ?? [];
    }
}
