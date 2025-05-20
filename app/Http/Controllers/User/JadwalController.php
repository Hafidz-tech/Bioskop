<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kursi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $tanggalTerpilih = $request->query('tanggal') ?? Carbon::today()->toDateString();

        // Generate list tanggal selama 7 hari ke depan
        $tanggalList = collect();
        for ($i = 0; $i < 7; $i++) {
            $tanggalList->push(Carbon::today()->addDays($i)->toDateString());
        }

        // Ambil jadwal film berdasarkan tanggal yang dipilih, grouping berdasarkan film
        $jadwals = Jadwal::with(['film', 'studio'])
            ->whereDate('tanggal', $tanggalTerpilih)
            ->orderBy('jam')
            ->get()
            ->groupBy('film_id');

        // Hitung sisa kursi untuk setiap jadwal
        foreach ($jadwals as $jadwalList) {
            foreach ($jadwalList as $jadwal) {
                $totalKursi = $jadwal->studio->kapasitas;

                // Hitung kursi yang sudah dipesan dengan status pembayaran confirmed
                $kursiTerpakai = DB::table('kursi_pemesanan')
                    ->join('pemesanans as p', 'kursi_pemesanan.pemesanan_id', '=', 'p.id')
                    ->join('pembayarans as pay', 'p.id', '=', 'pay.pemesanan_id')
                    ->where('p.jadwal_id', $jadwal->id)
                    ->where('pay.status', 'confirmed')
                    ->count();

                $jadwal->sisa_kursi = $totalKursi - $kursiTerpakai;
            }
        }

        return view('user.jadwal.index', compact('jadwals', 'tanggalList', 'tanggalTerpilih'));
    }

    public function show($id)
    {
        $jadwal = Jadwal::with(['film', 'studio'])->findOrFail($id);

        // Ambil kursi yang sudah dipesan untuk jadwal ini dengan status pembayaran confirmed
        $kursiTerpesanIds = DB::table('kursi_pemesanan')
            ->join('pemesanans as p', 'kursi_pemesanan.pemesanan_id', '=', 'p.id')
            ->join('pembayarans as pay', 'p.id', '=', 'pay.pemesanan_id')
            ->where('p.jadwal_id', $id)
            ->where('pay.status', 'confirmed')
            ->pluck('kursi_pemesanan.kursi_id') // Gunakan alias tabel agar aman
            ->toArray();

        // Ambil seluruh kursi di studio tersebut lalu tandai yang sudah dipesan
        $kursis = Kursi::where('studio_id', $jadwal->studio_id)
            ->get()
            ->sortBy(function ($kursi) {
                preg_match('/\d+/', $kursi->nomor_kursi, $matches);
                return isset($matches[0]) ? (int) $matches[0] : 0;
            })
            ->map(function ($kursi) use ($kursiTerpesanIds) {
                $kursi->sudah_dipesan = in_array($kursi->id, $kursiTerpesanIds);
                return $kursi;
            });

        return view('user.jadwal.show', compact('jadwal', 'kursis'));
    }
}
