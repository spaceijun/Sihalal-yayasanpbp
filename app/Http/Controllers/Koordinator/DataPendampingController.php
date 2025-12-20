<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataPendampingController extends Controller
{
    public function index()
    {
        $enumerators = Enumerator::where('koordinator_id', Auth::user()->koordinator->id)->latest()->paginate(10);
        return view('koordinator.data-pendamping.index', compact('enumerators'));
    }
}
