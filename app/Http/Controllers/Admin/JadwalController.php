<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $perPage = $request->per_page ?? 10;

        $jadwals = Jadwal::with('film', 'studio')
                        ->when($search, function ($query) use ($search) {
                            return $query->whereHas('film', function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                    });
                        })
                                ->paginate($perPage)
                         ->withQueryString(); // ← Penting agar pagination membawa parameter pencarian

        $films = Film::all();
        $studios = Studio::all();

        return view('admin.jadwal.index', compact('jadwals', 'films', 'studios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam' => 'required|date_format:H:i',
        ]);

        $inputStart = Carbon::parse($request->tanggal . ' ' . $request->jam);

        $existingJadwals = Jadwal::where('studio_id', $request->studio_id)
                                ->where('tanggal', $request->tanggal)
                                ->get();

        foreach ($existingJadwals as $jadwal) {
            $jadwalTime = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam);
            $diffInMinutes = abs($inputStart->diffInMinutes($jadwalTime));

            if ($diffInMinutes < 120) {
                return redirect()->back()
                    ->with('error', 'Jadwal gagal ditambahkan. Studio ini sudah memiliki jadwal yang terlalu dekat (kurang dari 2 jam).')
                    ->withInput();
            }
        }

        Jadwal::create($request->only(['film_id', 'studio_id', 'tanggal', 'jam']));

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $films = Film::all();
        $studios = Studio::all();

        return view('admin.jadwal.edit', compact('jadwal', 'films', 'studios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam' => 'required|date_format:H:i',
        ]);

        $inputStart = Carbon::parse($request->tanggal . ' ' . $request->jam);

        $existingJadwals = Jadwal::where('studio_id', $request->studio_id)
                                 ->where('tanggal', $request->tanggal)
                                 ->where('id', '!=', $id)
                                 ->get();

        foreach ($existingJadwals as $jadwal) {
            $jadwalTime = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam);
            $diffInMinutes = abs($inputStart->diffInMinutes($jadwalTime));

            if ($diffInMinutes < 120) {
                return redirect()->back()
                    ->with('error', 'Jadwal gagal diperbarui. Studio ini sudah memiliki jadwal yang terlalu dekat (kurang dari 2 jam).')
                    ->withInput();
            }
        }

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->only(['film_id', 'studio_id', 'tanggal', 'jam']));

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
