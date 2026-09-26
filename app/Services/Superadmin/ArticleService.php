<?php

namespace App\Services\Superadmin;

use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleService
{
    public function store(array $data, ?UploadedFile $image = null): Article
    {
        return DB::transaction(function () use ($data, $image) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            if ($image) {
                $data['image'] = $image->store('articles', 'public');
            }

            $data['is_published'] = $data['is_published'] ?? false;
            $data['is_featured'] = $data['is_featured'] ?? false;

            if ($data['is_published'] && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            return Article::create($data);
        });
    }

    public function update(Article $article, array $data, ?UploadedFile $image = null): Article
    {
        return DB::transaction(function () use ($article, $data, $image) {
            if (empty($data['slug']) || $data['slug'] !== $article->slug) {
                $data['slug'] = Str::slug($data['title']);

                $counter = 1;
                $baseSlug = $data['slug'];
                while (Article::where('slug', $data['slug'])->where('id', '!=', $article->id)->exists()) {
                    $data['slug'] = $baseSlug.'-'.$counter++;
                }
            }

            if ($image) {
                if ($article->image) {
                    Storage::disk('public')->delete($article->image);
                }

                $data['image'] = $image->store('articles', 'public');
            }

            $data['is_published'] = $data['is_published'] ?? false;
            $data['is_featured'] = $data['is_featured'] ?? false;

            if ($data['is_published'] && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $article->update($data);

            return $article->fresh();
        });
    }

    public function delete(Article $article): void
    {
        DB::transaction(function () use ($article) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }

            $article->delete();
        });
    }
}
