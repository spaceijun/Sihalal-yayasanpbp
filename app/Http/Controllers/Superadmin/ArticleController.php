<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\Superadmin\ArticleService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ArticleController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private ArticleService $service) {}

    /**
     * Display a listing of articles
     */
    public function index(): View
    {
        return view('superadmin.company-profile.articles.index', ['routePrefix' => $this->routePrefix()]);
    }

    /**
     * Return DataTables JSON for article listing.
     */
    public function data(Request $request)
    {
        $articles = Article::query()->orderBy('created_at', 'desc');

        return DataTables::of($articles)
            ->addIndexColumn()
            ->addColumn('status_badge', function ($article) {
                $published = $article->is_published;
                $featured = $article->is_featured;

                $badge = $published
                    ? '<span class="badge bg-success">Published</span>'
                    : '<span class="badge bg-warning">Draft</span>';

                if ($featured) {
                    $badge .= ' <span class="badge bg-info">Featured</span>';
                }

                return $badge;
            })
            ->addColumn('category_badge', function ($article) {
                return '<span class="badge bg-secondary">'.e(ucfirst($article->category)).'</span>';
            })
            ->addColumn('reading_time', function ($article) {
                return $article->reading_time.' min';
            })
            ->addColumn('date', function ($article) {
                return $article->published_at
                    ? $article->published_at->format('d/m/Y')
                    : '-';
            })
            ->addColumn('aksi', function ($article) {
                $editUrl = route($this->routePrefix().'.articles.edit', $article->id);

                return '<div class="adm-actions justify-content-center">
                    <a class="adm-btn primary icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteArticle(\''.$article->id.'\', \''.e(addslashes($article->title)).'\')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['status_badge', 'category_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new article
     */
    public function create(): View
    {
        return view('superadmin.company-profile.articles.create', ['routePrefix' => $this->routePrefix()]);
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        try {
            $this->service->store($validated, $request->file('image'));

            return redirect()->route($this->routePrefix().'.articles.index')
                ->with('success', 'Artikel berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat artikel: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing an article
     */
    public function edit(int $id): View
    {
        $article = Article::findOrFail($id);

        return view('superadmin.company-profile.articles.edit', [
            'article' => $article,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Update an article
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($article->id),
            ],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        try {
            $this->service->update($article, $validated, $request->file('image'));

            return redirect()->back()->with('success', 'Artikel berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui artikel: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove an article
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete(Article::findOrFail($id));

            return response()->json(['message' => 'Artikel berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
