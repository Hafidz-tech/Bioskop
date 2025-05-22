<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursi;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KursiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search');

        $kursis = Kursi::with('studio')
            ->when($search, function ($query, $search) {
                return $query->whereHas('studio', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                })->orWhere('nomor_kursi', 'like', '%' . $search . '%');
            })
            ->paginate($perPage)
            ->appends($request->only(['per_page', 'search']));

        $studios = Studio::all();

        return view('admin.kursi.index', compact('kursis', 'studios'));
    }

    public function create()
    {
        $studios = Studio::all();
        return view('admin.kursi.create', compact('studios'));
    }

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
        ]);

        Kursi::create([
            'studio_id' => $request->studio_id,
            'nomor_kursi' => $request->nomor_kursi,
        ]);

        $this->updateStudioKapasitas($request->studio_id);

        return redirect()->route('admin.kursi.index')
            ->with('success', 'Kursi berhasil ditambahkan dan kapasitas studio diperbarui.');
    }

    public function edit(Kursi $kursi)
    {
        $studios = Studio::all();
        return view('admin.kursi.edit', compact('kursi', 'studios'));
    }

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
        ]);

        $kursi->update([
            'studio_id' => $request->studio_id,
            'nomor_kursi' => $request->nomor_kursi,
        ]);

        $this->updateStudioKapasitas($request->studio_id);

        return redirect()->route('admin.kursi.index')
            ->with('success', 'Kursi berhasil diperbarui.');
    }

    public function destroy(Kursi $kursi)
    {
        $studioId = $kursi->studio_id;

        $kursi->delete();

        $this->updateStudioKapasitas($studioId);

        return redirect()->route('admin.kursi.index')
            ->with('success', 'Kursi berhasil dihapus!');
    }

    private function updateStudioKapasitas($studioId)
    {
        $jumlahKursi = Kursi::where('studio_id', $studioId)->count();
        Studio::where('id', $studioId)->update(['kapasitas' => $jumlahKursi]);
    }
}
