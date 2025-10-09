<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    // Tampilkan semua genre dengan fitur pencarian dan jumlah per halaman
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 5); // Default 5 jika tidak ada input

        $genres = Genre::when($search, function ($query, $search) {
                        return $query->where('nama', 'like', '%' . $search . '%');
                    })
                    ->latest()
                    ->paginate($perPage)
                    ->appends($request->all()); // agar search dan per_page tetap saat pindah halaman

        return view('admin.genre.index', compact('genres', 'search'));
    }

    // Simpan genre baru
    public function store(Request $request)
    {
        if (empty($request->nama)) {
            return back()->withErrors(['nama' => 'Nama genre tidak boleh kosong.'])->withInput();
        }

        $request->validate([
            'nama' => 'string|max:255|unique:genres,nama',
        ]);

        Genre::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.genre.index')->with('success', 'Genre berhasil ditambahkan.');
    }

    // Tampilkan form edit genre
    public function edit($id)
    {
        $genre = Genre::findOrFail($id);
        return view('admin.genre.edit', compact('genre'));
    }

    // Update genre
    public function update(Request $request, $id)
    {
        $genre = Genre::findOrFail($id);

        if (empty($request->nama)) {
            return back()->withErrors(['nama' => 'Nama genre tidak boleh kosong.'])->withInput();
        }

        $request->validate([
            'nama' => 'string|max:255|unique:genres,nama,' . $genre->id,
        ]);

        $genre->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.genre.index')->with('success', 'Genre berhasil diperbarui.');
    }

    // Hapus genre
    public function destroy($id)
    {
        $genre = Genre::findOrFail($id);

        // Cek apakah genre ini memiliki film yang tertaut
        if ($genre->films()->count() > 0) {
            return redirect()->route('admin.genre.index')   
                ->with('error', 'Genre tidak dapat dihapus karena masih tertaut oleh film.');
        }

        $genre->delete();

        return redirect()->route('admin.genre.index')->with('success', 'Genre berhasil dihapus.');
    }
}
