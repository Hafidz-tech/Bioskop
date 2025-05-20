<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Menampilkan laporan transaksi
    public function index()
    {
        $laporan = Pemesanan::with('user', 'jadwal')->get();
        return view('admin.laporan.index', compact('laporan'));
    }
}
