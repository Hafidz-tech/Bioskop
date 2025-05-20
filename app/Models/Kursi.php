<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kursi extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id', 'nomor_kursi'
    ];

    // Relasi ke Studio
    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
    
    // Relasi ke Pemesanan
    public function pemesanans()
    {
        return $this->belongsToMany(Pemesanan::class, 'kursi_pemesanan')->withTimestamps();
    }

    // Relasi ke Jadwal
    public function jadwals()
    {
        return $this->belongsToMany(Jadwal::class, 'kursi_jadwal')->withTimestamps();
    }
}
