<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search  = $request->get('search');
        $status  = $request->get('status'); // 'pending' atau 'confirmade'

        $query = Pemesanan::with([
                'user',
                'jadwal.film',
                'jadwal.studio',
                'kursi',
                'pembayaran'
            ])
            ->latest();

        // Filter pencarian nama user atau judul film
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q2) => 
                        $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('jadwal.film', fn($q2) => 
                        $q2->where('judul', 'like', "%{$search}%"));
            });
        }

        // Filter berdasarkan status pembayaran
        if ($status) {
            $query->whereHas('pembayaran', fn($q) => 
                $q->where('status', $status)
            );
        }

        $pemesanans = $query->paginate($perPage);

        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    /**
     * Konfirmasi pembayaran: ubah status di tabel pembayaran menjadi 'paid'
     */
   public function updateStatus($id, $status)
{
    $pembayaran = Pembayaran::where('pemesanan_id', $id)->firstOrFail();

    if (!in_array($status, ['pending', 'paid'])) {
        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    $pembayaran->update(['status' => $status]);

    return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui menjadi ' . ucfirst($status));
}


}
