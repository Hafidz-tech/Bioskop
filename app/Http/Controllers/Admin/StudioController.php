<?php

namespace App\Http\Controllers\Admin;

use App\Models\Studio;
use App\Models\Kursi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudioController extends Controller
{
    // Metode untuk menampilkan data studio
    public function index(Request $request)
    {
        $search = $request->search;
        $perPage = $request->per_page ?? 5;

        $query = Studio::with('kursis');

        if (!empty($search)) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $studios = $query->paginate($perPage)->appends(request()->query());

        return view('admin.studio.index', compact('studios'));
    }

    // Metode untuk menyimpan studio baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Studio::create($request->all());

        return redirect()->route('admin.studio.index')->with('success', 'Studio berhasil ditambahkan!');
    }

    // Metode untuk mengupdate studio
    public function update(Request $request, Studio $studio)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $studio->update($request->all());

        return redirect()->route('admin.studio.index')->with('success', 'Studio berhasil diperbarui!');
    }

    // Metode untuk menghapus studio
    public function destroy(Studio $studio)
    {
        if ($studio->kursis()->count() > 0) {
            return redirect()->route('admin.studio.index')->with('error', 'Studio tidak dapat dihapus karena masih memiliki kursi yang tertaut.');
        }

        $studio->delete();

        return redirect()->route('admin.studio.index')->with('success', 'Studio berhasil dihapus!');
    }

    // Metode untuk menangani pemesanan kursi
    public function bookSeats(Request $request, Studio $studio)
    {
        $request->validate([
            'seats' => 'array|max:5',
            'seats.*' => 'exists:kursis,id',
        ]);

        $selectedSeats = Kursi::whereIn('id', $request->seats)->get();

        foreach ($selectedSeats as $kursi) {
            if ($kursi->status == 'dipesan') {
                return back()->withErrors(['seats' => "Kursi nomor {$kursi->nomor_kursi} sudah dipesan."]);
            }
            $kursi->status = 'dipesan';
            $kursi->save();
        }

        return redirect()->route('admin.studio.index')->with('success', 'Kursi berhasil dipesan!');
    }
}
