<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\CompanyBenefit;
use App\Models\CompanyHistory;
use App\Models\CompanyProfile;
use App\Models\CompanyStatistic;
use App\Models\CompanyTeam;
use App\Models\ContactMessage;
use App\Models\RecruitmentPost;
use App\Models\SocialMedia;
use App\Models\Testimonial;
use Illuminate\Support\Collection;

class CompanyProfileService
{
    /**
     * Get home page data
     */
    public function getHomeData(): array
    {
        $profile = CompanyProfile::findByPage('home');

        $stats = CompanyStatistic::getStats();
        $benefits = CompanyBenefit::getBenefits();
        $testimonials = Testimonial::getTestimonials();
        $socialMedia = SocialMedia::getActive();

        // Get featured articles
        $featuredArticles = Article::published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        // Fallback to latest if no featured
        if ($featuredArticles->isEmpty()) {
            $featuredArticles = Article::published()
                ->orderBy('published_at', 'desc')
                ->take(4)
                ->get();
        }

        // Get active recruitment posts
        $recruitmentPosts = RecruitmentPost::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return [
            'profile' => $profile,
            'stats' => $stats,
            'benefits' => $benefits,
            'testimonials' => $testimonials,
            'socialMedia' => $socialMedia,
            'featuredArticles' => $featuredArticles,
            'recruitmentPosts' => $recruitmentPosts,
        ];
    }

    /**
     * Get about page data
     */
    public function getAboutData(): array
    {
        $profile = CompanyProfile::findByPage('about');
        $timeline = CompanyHistory::getTimeline();
        $teams = CompanyTeam::getTeam();
        $stats = CompanyStatistic::getStats();
        $benefits = CompanyBenefit::getBenefits();
        $testimonials = Testimonial::getTestimonials();
        $socialMedia = SocialMedia::getActive();

        return [
            'profile' => $profile,
            'timeline' => $timeline,
            'teams' => $teams,
            'stats' => $stats,
            'benefits' => $benefits,
            'testimonials' => $testimonials,
            'socialMedia' => $socialMedia,
        ];
    }

    /**
     * Get articles page data
     */
    public function getArticlesData(array $filters = []): array
    {
        $query = Article::published();

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('published_at', 'desc')
            ->paginate(6);

        $categories = ArticleCategory::getAllWithCount();

        return [
            'articles' => $articles,
            'categories' => $categories,
        ];
    }

    /**
     * Get single article data
     */
    public function getArticleData(string $slug): ?array
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$article) {
            return null;
        }

        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ];
    }

    /**
     * Get recruitment page data
     */
    public function getRecruitmentData(array $filters = []): array
    {
        $query = RecruitmentPost::where('is_active', true);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_loker', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate(6);

        return [
            'posts' => $posts,
        ];
    }

    /**
     * Get single recruitment post data
     */
    public function getRecruitmentPostData(string $slug): ?array
    {
        $post = RecruitmentPost::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$post) {
            return null;
        }

        return [
            'post' => $post,
        ];
    }

    /**
     * Get contact page data
     */
    public function getContactData(): array
    {
        $profile = CompanyProfile::findByPage('contact');
        $socialMedia = SocialMedia::getActive();

        return [
            'profile' => $profile,
            'socialMedia' => $socialMedia,
        ];
    }

    /**
     * Submit contact message
     */
    public function submitContact(array $data): ContactMessage
    {
        return ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'status' => 'pending',
        ]);
    }

    /**
     * Get social media links
     */
    public function getSocialMedia(): Collection
    {
        return SocialMedia::getActive();
    }
}
