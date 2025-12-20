<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalDataKoordinator = Koordinator::count();
        $totalDataEnumerator = Enumerator::count();
        $totalDataLapangan = DataLapangan::count();
        return view('superadmin.home.index', compact('totalDataKoordinator', 'totalDataEnumerator', 'totalDataLapangan'));
    }
}
