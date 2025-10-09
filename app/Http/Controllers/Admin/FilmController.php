<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FilmController extends Controller
{
    // Menampilkan daftar film
    public function index(Request $request)
    {
        $query = Film::with('genre', 'ratings');

        // Pencarian
        if ($request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 5);
        $films = $query->paginate($perPage)->withQueryString();
        $genres = Genre::all();

        return view('admin.film.index', compact('films', 'genres'));
    }

    // Menampilkan form tambah film
    public function create()
    {
        $genres = Genre::all();
        return view('admin.film.create', compact('genres'));
    }

    // Menyimpan data film
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'durasi' => 'required|integer|min:1',
            'genre_id' => 'required|exists:genres,id',
            'poster' => 'nullable|image|max:2048',
            // ganti rule numeric jadi integer
            'harga' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.film.create')->withErrors($validator)->withInput();
        }

        $film = new Film();
        $film->judul = $request->judul;
        $film->sinopsis = $request->sinopsis;
        $film->durasi = $request->durasi;
        $film->genre_id = $request->genre_id;
        // cast ke integer kalau perlu
        $film->harga = (int) $request->harga;

        if ($request->hasFile('poster')) {
            $film->poster = $request->file('poster')->store('posters', 'public');
        }

        $film->save();

        return redirect()->route('admin.film.index')->with('success', 'Film berhasil ditambahkan');
    }

    // Menampilkan form edit
    public function edit(Film $film)
    {
        return redirect()->route('admin.film.index');
    }

    // Proses update
    public function update(Request $request, Film $film)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'durasi' => 'required|integer|min:1',
            'genre_id' => 'required|exists:genres,id',
            'poster' => 'nullable|image|max:2048',
            // ganti rule numeric jadi integer
            'harga' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.film.edit', $film->id)->withErrors($validator)->withInput();
        }

        $film->judul = $request->judul;
        $film->sinopsis = $request->sinopsis;
        $film->durasi = $request->durasi;
        $film->genre_id = $request->genre_id;
        // cast ke integer
        $film->harga = (int) $request->harga;

        if ($request->hasFile('poster')) {
            if ($film->poster && Storage::disk('public')->exists($film->poster)) {
                Storage::disk('public')->delete($film->poster);
            }
            $film->poster = $request->file('poster')->store('posters', 'public');
        }

        $film->save();

        return redirect()->route('admin.film.index')->with('success', 'Film berhasil diperbarui');
    }

    // Hapus film
    public function destroy(Film $film)
    {
        if ($film->poster && Storage::disk('public')->exists($film->poster)) {
            Storage::disk('public')->delete($film->poster);
        }

        $film->delete();

        return redirect()->route('admin.film.index')->with('success', 'Film berhasil dihapus');
    }
}
