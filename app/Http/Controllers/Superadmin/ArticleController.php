<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ArticleController extends Controller
{
    use HasRoutePrefix;
    /**
     * Display a listing of articles
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
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
                    return '<span class="badge bg-secondary">' . ucfirst($article->category) . '</span>';
                })
                ->addColumn('reading_time', function ($article) {
                    return $article->reading_time . ' min';
                })
                ->addColumn('date', function ($article) {
                    return $article->published_at
                        ? $article->published_at->format('d/m/Y')
                        : '-';
                })
                ->addColumn('aksi', function ($article) {
                    $editUrl = route($this->routePrefix() . '.articles.edit', $article->id);
                    $deleteUrl = route($this->routePrefix() . '.articles.destroy', $article->id);

                    return '<div class="adm-actions justify-content-center">
                        <a class="adm-btn primary icon-only" href="' . $editUrl . '" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form action="' . $deleteUrl . '" method="POST" class="d-inline form-delete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['status_badge', 'category_badge', 'aksi'])
                ->make(true);
        }

        return view('superadmin.company-profile.articles.index', ['routePrefix' => $this->routePrefix()]);
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        return view('superadmin.company-profile.articles.create', ['routePrefix' => $this->routePrefix()]);
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
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

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $validated['image'] = $path;
        }

        $validated['is_published'] = $validated['is_published'] ?? false;
        $validated['is_featured'] = $validated['is_featured'] ?? false;

        // Set published_at if publishing
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Article::create($validated);

        return redirect()->route($this->routePrefix() . '.articles.index')
            ->with('success', 'Artikel berhasil dibuat');
    }

    /**
     * Show the form for editing an article
     */
    public function edit(int $id)
    {
        $article = Article::findOrFail($id);
        return view('superadmin.company-profile.articles.edit', [
            'article' => $article,
            'routePrefix' => $this->routePrefix()
        ]);
    }

    /**
     * Update an article
     */
    public function update(Request $request, int $id)
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

        // Generate slug if title changed
        if (empty($validated['slug']) || $validated['slug'] !== $article->slug) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure unique slug
            $counter = 1;
            $baseSlug = $validated['slug'];
            while (Article::where('slug', $validated['slug'])->where('id', '!=', $article->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter++;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($article->image) {
                \Storage::disk('public')->delete($article->image);
            }
            $path = $request->file('image')->store('articles', 'public');
            $validated['image'] = $path;
        }

        $validated['is_published'] = $validated['is_published'] ?? false;
        $validated['is_featured'] = $validated['is_featured'] ?? false;

        // Set published_at if publishing
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->back()->with('success', 'Artikel berhasil diperbarui');
    }

    /**
     * Remove an article
     */
    public function destroy(int $id)
    {
        $article = Article::findOrFail($id);

        // Delete image
        if ($article->image) {
            \Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route($this->routePrefix() . '.articles.index')
            ->with('success', 'Artikel berhasil dihapus');
    }
}
