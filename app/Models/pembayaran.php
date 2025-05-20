<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemesanan_id', 'bukti_pembayaran', 'status'
    ];

    // Relasi ke Pemesanan
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
