<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PemesananController extends Controller
{
    /**
     * Tampilkan daftar pemesanan user beserta status dan bukti pembayaran,
     * dengan filter status, pencarian, dan pagination.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Pemesanan::with([
                'jadwal.film',
                'jadwal.studio',
                'kursi',
                'pembayaran'
            ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter pencarian berdasarkan judul film
        if ($search) {
            $query->whereHas('jadwal.film', function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status pembayaran
        if ($status) {
            $query->whereHas('pembayaran', function ($q) use ($status) {
                // Jika status 'selesai' di UI, mapping ke 'paid' di db
                if ($status === 'selesai') {
                    $q->where('status', 'paid');
                } else {
                    $q->where('status', $status);
                }
            });
        }

        $pemesanans = $query->paginate($perPage)->withQueryString();

        return view('user.pemesanan.index', compact('pemesanans'));
    }

    /**
     * Simpan pemesanan baru sekaligus upload bukti pembayaran dan arahkan ke halaman daftar pemesanan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id'         => 'required|exists:jadwals,id',
            'kursi_ids'         => 'required|array|min:1',
            'kursi_ids.*'       => 'exists:kursis,id',
            'total_harga'       => 'required|numeric|min:0',
            'bukti_pembayaran'  => 'required|image|max:2048', // wajib upload bukti pembayaran
        ]);

        $userId = Auth::id();
        $kursiTerpilih = $request->kursi_ids;

        // Cek apakah kursi sudah dipesan dan dibayar orang lain
        $kursiSudahDipesan = DB::table('kursi_pemesanan')
            ->whereIn('kursi_id', $kursiTerpilih)
            ->whereIn('pemesanan_id', function ($q) use ($request) {
                $q->select('pemesanans.id')
                    ->from('pemesanans')
                    ->join('pembayarans', 'pemesanans.id', '=', 'pembayarans.pemesanan_id')
                    ->where('pemesanans.jadwal_id', $request->jadwal_id)
                    ->where('pembayarans.status', 'paid');
            })
            ->exists();

        if ($kursiSudahDipesan) {
            return back()->withErrors('Beberapa kursi sudah dibayar oleh orang lain. Silakan pilih kursi lain.');
        }

        DB::beginTransaction();

        try {
            $jumlah_tiket = count($kursiTerpilih);
            $total_harga = (int) round($request->total_harga);

            $pemesanan = Pemesanan::create([
                'jadwal_id'     => $request->jadwal_id,
                'user_id'       => $userId,
                'jumlah_tiket'  => $jumlah_tiket,
                'total_harga'   => $total_harga,
            ]);

            $pemesanan->kursi()->attach($kursiTerpilih);

            // Upload file bukti pembayaran
            $path = Storage::disk('public')->putFile('bukti', $request->file('bukti_pembayaran'));

            // Simpan data pembayaran dengan status 'waiting'
            Pembayaran::create([
                'pemesanan_id'     => $pemesanan->id,
                'bukti_pembayaran' => $path,
                'status'           => 'waiting', // status awal
            ]);

            DB::commit();

            return redirect()
                ->route('user.pemesanan.index')
                ->with('success', 'Pemesanan dan bukti pembayaran berhasil disimpan. Menunggu konfirmasi.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menyimpan pemesanan', ['error' => $e->getMessage()]);
            return back()->withErrors('Terjadi kesalahan saat menyimpan pemesanan. Silakan coba lagi.');
        }
    }
}
