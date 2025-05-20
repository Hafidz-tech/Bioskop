<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_id', 'user_id', 'jumlah_tiket', 'total_harga', 'status'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // Relasi ke Pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Relasi ke Kursi
    public function kursi()
    {
    return $this->belongsToMany(Kursi::class, 'kursi_pemesanan', 'pemesanan_id', 'kursi_id');
    }

}
