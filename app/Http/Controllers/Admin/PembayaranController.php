<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::paginate(10);
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function markPaid(Pembayaran $pembayaran)
    {
        $pembayaran->status = 'paid';
        $pembayaran->save();

        return redirect()->back()->with('success', 'Status pembayaran diubah menjadi paid.');
    }

}
