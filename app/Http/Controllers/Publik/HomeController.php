<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Services\CompanyProfileService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected CompanyProfileService $service;

    public function __construct(CompanyProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the home page
     */
    public function index()
    {
        $data = $this->service->getHomeData();

        return view('publik.company-profile.home', $data);
    }

    /**
     * Display the about page
     */
    public function about()
    {
        $data = $this->service->getAboutData();

        return view('publik.company-profile.about', $data);
    }

    /**
     * Display the articles listing page
     */
    public function articles(Request $request)
    {
        $data = $this->service->getArticlesData([
            'category' => $request->get('category'),
            'search' => $request->get('search'),
        ]);

        return view('publik.company-profile.articles.index', $data);
    }

    /**
     * Display a single article
     */
    public function showArticle(string $slug)
    {
        $data = $this->service->getArticleData($slug);

        if (!$data) {
            abort(404);
        }

        return view('publik.company-profile.articles.show', $data);
    }

    /**
     * Display the recruitment page
     */
    public function recruitment(Request $request)
    {
        $data = $this->service->getRecruitmentData([
            'search' => $request->get('search'),
        ]);

        return view('publik.company-profile.recruitment.index', $data);
    }

    /**
     * Display a single recruitment post
     */
    public function showRecruitment(string $slug)
    {
        $data = $this->service->getRecruitmentPostData($slug);

        if (!$data) {
            abort(404);
        }

        return view('publik.company-profile.recruitment.show', $data);
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        $data = $this->service->getContactData();

        return view('publik.company-profile.contact', $data);
    }

    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $this->service->submitContact($validated);

        return redirect()->back()
            ->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}
