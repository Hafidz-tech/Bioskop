<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursi;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KursiController extends Controller
{
    // Menampilkan daftar kursi dengan pagination, pencarian, dan filter status
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search');
        $status = $request->get('status');

        $kursis = Kursi::with('studio')
            ->when($search, function ($query, $search) {
                return $query->whereHas('studio', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                })->orWhere('nomor_kursi', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate($perPage)
            ->appends($request->only(['per_page', 'search', 'status'])); // menjaga query string saat pindah halaman

        $studios = Studio::all();

        return view('admin.kursi.index', compact('kursis', 'studios'));
    }

    // Menampilkan form tambah kursi
    public function create()
    {
        $studios = Studio::all();
        return view('admin.kursi.create', compact('studios'));
    }

    // Menyimpan data kursi baru
    public function store(Request $request)
    {
        $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'nomor_kursi' => [
                'required',
                'string',
                'max:10',
                Rule::unique('kursis')->where(function ($query) use ($request) {
                    return $query->where('studio_id', $request->studio_id);
                }),
            ],
            'status' => 'required|in:available,booked',
        ]);

        Kursi::create([
            'studio_id' => $request->studio_id,
            'nomor_kursi' => $request->nomor_kursi,
            'status' => $request->status,
        ]);

        $this->updateStudioKapasitas($request->studio_id);

        return redirect()->route('admin.kursi.index')
            ->with('success', 'Kursi berhasil ditambahkan dan kapasitas studio diperbarui.');
    }

    // Menampilkan form edit kursi
    public function edit(Kursi $kursi)
    {
        $studios = Studio::all();
        return view('admin.kursi.edit', compact('kursi', 'studios'));
    }

    // Memperbarui data kursi
    public function update(Request $request, Kursi $kursi)
    {
        $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'nomor_kursi' => [
                'required',
                'string',
                'max:10',
                Rule::unique('kursis')->where(function ($query) use ($request) {
                    return $query->where('studio_id', $request->studio_id);
                })->ignore($kursi->id),
            ],
            'status' => 'required|in:available,booked',
        ]);

        $kursi->update([
            'studio_id' => $request->studio_id,
            'nomor_kursi' => $request->nomor_kursi,
            'status' => $request->status,
        ]);

        $this->updateStudioKapasitas($request->studio_id);

        return redirect()->route('admin.kursi.index')
            ->with('success', 'Kursi berhasil diperbarui.');
    }


   // Menghapus kursi
public function destroy(Kursi $kursi)
{
    // Cek apakah status kursi adalah 'booked'
    if ($kursi->status === 'booked') {
        return redirect()->route('admin.kursi.index')
            ->with('error', 'Kursi tidak dapat dihapus karena statusnya dipinjam (booked).');
    }

    $studioId = $kursi->studio_id;

    // Menghapus kursi jika status bukan 'booked'
    $kursi->delete();

    $this->updateStudioKapasitas($studioId);

    return redirect()->route('admin.kursi.index')
        ->with('success', 'Kursi berhasil dihapus!');
}

    // Mengupdate kapasitas studio berdasarkan jumlah kursi
    private function updateStudioKapasitas($studioId)
    {
        $jumlahKursi = Kursi::where('studio_id', $studioId)->count();
        Studio::where('id', $studioId)->update(['kapasitas' => $jumlahKursi]);
    }
}
