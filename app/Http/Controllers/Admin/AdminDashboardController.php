<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Studio;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use App\Models\Pembayaran;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalFilm' => Film::count(),
            'totalStudio' => Studio::count(),
            'totalJadwal' => Jadwal::count(),
            'totalPemesanan' => Pemesanan::count(),
            'totalPembayaran' => Pembayaran::where('status', 'paid')->count(),
        ]);
    }
}
