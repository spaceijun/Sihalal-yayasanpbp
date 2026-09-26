<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\CompanyBenefit;
use App\Models\CompanyHistory;
use App\Models\CompanyStatistic;
use App\Models\CompanyTeam;
use App\Models\SocialMedia;
use App\Models\Testimonial;
use App\Services\Superadmin\CompanyManagementService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CompanyManagementController extends Controller
{
    use HasRoutePrefix;

    /** Sub-resource types selectable via the ?type= query on the data() endpoint. */
    private const TYPES = ['statistics', 'benefits', 'testimonials', 'teams', 'histories', 'categories', 'social-media'];

    public function __construct(private CompanyManagementService $service) {}

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

    /**
     * Return DataTables JSON for one of the sub-resource tabs (?type=statistics|benefits|...).
     */
    public function data(Request $request): JsonResponse
    {
        $type = $request->query('type');

        if (! in_array($type, self::TYPES, true)) {
            abort(404);
        }

        return match ($type) {
            'statistics' => $this->dataStatistics(),
            'benefits' => $this->dataBenefits(),
            'testimonials' => $this->dataTestimonials(),
            'teams' => $this->dataTeams(),
            'histories' => $this->dataHistories(),
            'categories' => $this->dataCategories(),
            'social-media' => $this->dataSocialMedia(),
        };
    }

    private function dataStatistics(): JsonResponse
    {
        $query = CompanyStatistic::query()->orderBy('sort_order');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('value_fmt', fn ($row) => '<span class="adm-badge adm-badge-success">'.e($row->value).e($row->suffix).'</span>')
            ->addColumn('icon_html', fn ($row) => '<i class="'.e($row->icon).'"></i>')
            ->addColumn('color_badge', fn ($row) => '<span class="adm-badge" style="background:'.e($row->color).';color:#fff;">'.e($row->color).'</span>')
            ->addColumn('status_badge', fn ($row) => $this->toggleButtonHtml('company-content.statistics.toggle', $row->id, (bool) $row->is_active))
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editStatisticModal'.$row->id, 'company-content.statistics.destroy', $row->id, $row->title))
            ->rawColumns(['value_fmt', 'icon_html', 'color_badge', 'status_badge', 'aksi'])
            ->make(true);
    }

    private function dataBenefits(): JsonResponse
    {
        $query = CompanyBenefit::query()->orderBy('sort_order');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('icon_html', fn ($row) => '<i class="'.e($row->icon).' fs-4"></i>')
            ->addColumn('description_short', fn ($row) => Str::limit($row->description, 50))
            ->addColumn('status_badge', fn ($row) => $this->toggleButtonHtml('company-content.benefits.toggle', $row->id, (bool) $row->is_active))
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editBenefitModal'.$row->id, 'company-content.benefits.destroy', $row->id, $row->title))
            ->rawColumns(['icon_html', 'status_badge', 'aksi'])
            ->make(true);
    }

    private function dataTestimonials(): JsonResponse
    {
        $query = Testimonial::query()->orderBy('sort_order');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('testimonial_short', fn ($row) => Str::limit($row->testimonial, 60))
            ->addColumn('rating_html', function ($row) {
                $html = '';
                for ($i = 0; $i < 5; $i++) {
                    $html .= '<i class="ri-star'.($i < $row->rating ? '-fill' : '').' text-warning"></i>';
                }

                return $html;
            })
            ->addColumn('status_badge', fn ($row) => $this->toggleButtonHtml('company-content.testimonials.toggle', $row->id, (bool) $row->is_active))
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editTestimonialModal'.$row->id, 'company-content.testimonials.destroy', $row->id, $row->name))
            ->rawColumns(['rating_html', 'status_badge', 'aksi'])
            ->make(true);
    }

    private function dataTeams(): JsonResponse
    {
        $query = CompanyTeam::query()->orderBy('sort_order');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('photo_html', function ($row) {
                if ($row->photo) {
                    return '<img src="'.e(Storage::url($row->photo)).'" alt="'.e($row->name).'" class="rounded-circle" width="40" height="40">';
                }

                return '<div class="adm-avatar mx-auto" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.e(substr($row->name, 0, 1)).'</div>';
            })
            ->addColumn('status_badge', fn ($row) => $this->toggleButtonHtml('company-content.teams.toggle', $row->id, (bool) $row->is_active))
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editTeamModal'.$row->id, 'company-content.teams.destroy', $row->id, $row->name))
            ->rawColumns(['photo_html', 'status_badge', 'aksi'])
            ->make(true);
    }

    private function dataHistories(): JsonResponse
    {
        $query = CompanyHistory::query()->orderBy('year', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('year_badge', fn ($row) => '<span class="adm-badge adm-badge-info">'.e($row->year).'</span>')
            ->addColumn('description_short', fn ($row) => Str::limit($row->description, 60))
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editHistoryModal'.$row->id, 'company-content.histories.destroy', $row->id, $row->title))
            ->rawColumns(['year_badge', 'aksi'])
            ->make(true);
    }

    private function dataCategories(): JsonResponse
    {
        $query = ArticleCategory::query()->orderBy('name');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('slug_code', fn ($row) => '<code class="adm-mono">'.e($row->slug).'</code>')
            ->addColumn('icon_html', fn ($row) => '<i class="'.e($row->icon).'"></i>')
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editCategoryModal'.$row->id, 'company-content.categories.destroy', $row->id, $row->name))
            ->rawColumns(['slug_code', 'icon_html', 'aksi'])
            ->make(true);
    }

    private function dataSocialMedia(): JsonResponse
    {
        $query = SocialMedia::query()->orderBy('sort_order');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('url_link', fn ($row) => '<a href="'.e($row->url).'" target="_blank" class="text-primary">'.e(Str::limit($row->url, 30)).'</a>')
            ->addColumn('icon_html', fn ($row) => '<i class="'.e($row->icon).'" style="color:'.e($row->color).';"></i>')
            ->addColumn('status_badge', fn ($row) => $this->toggleButtonHtml('company-content.social-media.toggle', $row->id, (bool) $row->is_active))
            ->addColumn('aksi', fn ($row) => $this->aksiButtonsHtml('editSocialModal'.$row->id, 'company-content.social-media.destroy', $row->id, $row->platform))
            ->rawColumns(['url_link', 'icon_html', 'status_badge', 'aksi'])
            ->make(true);
    }

    /** Edit-trigger + AJAX-delete button pair used by every sub-resource table. */
    private function aksiButtonsHtml(string $editModalId, string $deleteRouteName, int $id, ?string $label): string
    {
        $deleteUrl = route($this->routePrefix().'.'.$deleteRouteName, $id);

        return '<div class="adm-actions">
            <button type="button" class="adm-btn primary" data-bs-toggle="modal" data-bs-target="#'.$editModalId.'" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button type="button" class="adm-btn danger" title="Hapus"
                onclick="confirmDeleteCompanyContent(\''.$deleteUrl.'\', \''.e(addslashes($label ?? '')).'\')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
        </div>';
    }

    /** AJAX status-toggle button used by every sub-resource table that has is_active. */
    private function toggleButtonHtml(string $toggleRouteName, int $id, bool $isActive): string
    {
        $url = route($this->routePrefix().'.'.$toggleRouteName, $id);
        $icon = $isActive ? 'eye-line' : 'eye-off-line';
        $cls = $isActive ? 'success' : '';

        return '<button type="button" class="adm-btn '.$cls.'" title="Toggle Status" onclick="toggleCompanyContent(\''.$url.'\', this)">
            <i class="ri-'.$icon.'"></i>
        </button>';
    }

    // ==================== STATISTICS ====================

    public function storeStatistic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'value' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $this->service->storeStatistic($validated);

            return back()->with('success', 'Statistik berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan statistik: '.$e->getMessage())->withInput();
        }
    }

    public function updateStatistic(Request $request, $id): RedirectResponse
    {
        $stat = CompanyStatistic::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'value' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $this->service->updateStatistic($stat, $validated);

            return back()->with('success', 'Statistik berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui statistik: '.$e->getMessage())->withInput();
        }
    }

    public function destroyStatistic($id): JsonResponse
    {
        try {
            $this->service->deleteStatistic(CompanyStatistic::findOrFail($id));

            return response()->json(['message' => 'Statistik berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatistic($id): JsonResponse
    {
        try {
            $stat = $this->service->toggleStatistic(CompanyStatistic::findOrFail($id));

            return response()->json(['message' => 'Status statistik berhasil diubah.', 'is_active' => $stat->is_active]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ==================== BENEFITS ====================

    public function storeBenefit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        try {
            $this->service->storeBenefit($validated);

            return back()->with('success', 'Keunggulan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan keunggulan: '.$e->getMessage())->withInput();
        }
    }

    public function updateBenefit(Request $request, $id): RedirectResponse
    {
        $benefit = CompanyBenefit::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        try {
            $this->service->updateBenefit($benefit, $validated);

            return back()->with('success', 'Keunggulan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui keunggulan: '.$e->getMessage())->withInput();
        }
    }

    public function destroyBenefit($id): JsonResponse
    {
        try {
            $this->service->deleteBenefit(CompanyBenefit::findOrFail($id));

            return response()->json(['message' => 'Keunggulan berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggleBenefit($id): JsonResponse
    {
        try {
            $benefit = $this->service->toggleBenefit(CompanyBenefit::findOrFail($id));

            return response()->json(['message' => 'Status keunggulan berhasil diubah.', 'is_active' => $benefit->is_active]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ==================== TESTIMONIALS ====================

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'testimonial' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'photo' => 'nullable|image|max:2048',
        ]);

        try {
            $this->service->storeTestimonial($validated, $request->file('photo'));

            return back()->with('success', 'Testimoni berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan testimoni: '.$e->getMessage())->withInput();
        }
    }

    public function updateTestimonial(Request $request, $id): RedirectResponse
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

        try {
            $this->service->updateTestimonial($testimonial, $validated, $request->file('photo'));

            return back()->with('success', 'Testimoni berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui testimoni: '.$e->getMessage())->withInput();
        }
    }

    public function destroyTestimonial($id): JsonResponse
    {
        try {
            $this->service->deleteTestimonial(Testimonial::findOrFail($id));

            return response()->json(['message' => 'Testimoni berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggleTestimonial($id): JsonResponse
    {
        try {
            $testimonial = $this->service->toggleTestimonial(Testimonial::findOrFail($id));

            return response()->json(['message' => 'Status testimoni berhasil diubah.', 'is_active' => $testimonial->is_active]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ==================== TEAMS ====================

    public function storeTeam(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'description' => 'nullable|string',
            'linkedin' => 'nullable|url',
            'twitter' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        try {
            $this->service->storeTeam($validated, $request->file('photo'));

            return back()->with('success', 'Tim berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan tim: '.$e->getMessage())->withInput();
        }
    }

    public function updateTeam(Request $request, $id): RedirectResponse
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

        try {
            $this->service->updateTeam($team, $validated, $request->file('photo'));

            return back()->with('success', 'Tim berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui tim: '.$e->getMessage())->withInput();
        }
    }

    public function destroyTeam($id): JsonResponse
    {
        try {
            $this->service->deleteTeam(CompanyTeam::findOrFail($id));

            return response()->json(['message' => 'Tim berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggleTeam($id): JsonResponse
    {
        try {
            $team = $this->service->toggleTeam(CompanyTeam::findOrFail($id));

            return response()->json(['message' => 'Status tim berhasil diubah.', 'is_active' => $team->is_active]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ==================== HISTORIES ====================

    public function storeHistory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'description' => 'nullable|string',
        ]);

        try {
            $this->service->storeHistory($validated);

            return back()->with('success', 'Riwayat berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan riwayat: '.$e->getMessage())->withInput();
        }
    }

    public function updateHistory(Request $request, $id): RedirectResponse
    {
        $history = CompanyHistory::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'description' => 'nullable|string',
        ]);

        try {
            $this->service->updateHistory($history, $validated);

            return back()->with('success', 'Riwayat berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui riwayat: '.$e->getMessage())->withInput();
        }
    }

    public function destroyHistory($id): JsonResponse
    {
        try {
            $this->service->deleteHistory(CompanyHistory::findOrFail($id));

            return response()->json(['message' => 'Riwayat berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ==================== CATEGORIES ====================

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            $this->service->storeCategory($validated);

            return back()->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan kategori: '.$e->getMessage())->withInput();
        }
    }

    public function updateCategory(Request $request, $id): RedirectResponse
    {
        $category = ArticleCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            $this->service->updateCategory($category, $validated);

            return back()->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui kategori: '.$e->getMessage())->withInput();
        }
    }

    public function destroyCategory($id): JsonResponse
    {
        try {
            $this->service->deleteCategory(ArticleCategory::findOrFail($id));

            return response()->json(['message' => 'Kategori berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ==================== SOCIAL MEDIA ====================

    public function storeSocialMedia(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $this->service->storeSocialMedia($validated);

            return back()->with('success', 'Social media berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan social media: '.$e->getMessage())->withInput();
        }
    }

    public function updateSocialMedia(Request $request, $id): RedirectResponse
    {
        $social = SocialMedia::findOrFail($id);

        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $this->service->updateSocialMedia($social, $validated);

            return back()->with('success', 'Social media berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui social media: '.$e->getMessage())->withInput();
        }
    }

    public function destroySocialMedia($id): JsonResponse
    {
        try {
            $this->service->deleteSocialMedia(SocialMedia::findOrFail($id));

            return response()->json(['message' => 'Social media berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggleSocialMedia($id): JsonResponse
    {
        try {
            $social = $this->service->toggleSocialMedia(SocialMedia::findOrFail($id));

            return response()->json(['message' => 'Status social media berhasil diubah.', 'is_active' => $social->is_active]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
