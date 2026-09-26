<?php

namespace App\Http\Controllers\AdminUmum;

use App\Http\Controllers\Controller;
use App\Services\AdminUmum\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    /**
     * Display admin umum dashboard.
     */
    public function index(): View
    {
        return view('admin-umum.home.index', $this->dashboardService->getDashboardData());
    }
}
