<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $pembayarans = Pembayaran::paginate(10);
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function uploadBukti(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|max:2048', // misal: file gambar max 2MB
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/bukti_pembayaran', $filename);

            $pembayaran->bukti_pembayaran = $filename;
            $pembayaran->status = 'pending'; // atau sesuai logika
            $pembayaran->save();

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload.');
        }

        return redirect()->back()->with('error', 'Upload bukti pembayaran gagal.');
    }

    public function markPaid(Pembayaran $pembayaran)
    {
        $pembayaran->status = 'paid';
        $pembayaran->save();

        return redirect()->back()->with('success', 'Status pembayaran diubah menjadi paid.');
    }
}
