<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Services\Koordinator\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(): View
    {
        $koordinator = auth()->user()->koordinator;

        $stats = $this->service->getStats($koordinator);
        $enumerators = $this->service->getEnumeratorKpi($koordinator);

        return view('koordinator.dashboard', compact('koordinator', 'stats', 'enumerators'));
    }
}
