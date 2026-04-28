<?php

namespace App\Http\Controllers;

use App\Models\ResepMakanan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function resepMakanan()
    {
        $reseps = ResepMakanan::all();
        return view('publik.resep-makanan', compact('reseps'));
    }
}
