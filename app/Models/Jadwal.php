<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id', 'studio_id', 'tanggal', 'jam'
    ];

    // Relasi ke Film
    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    // Relasi ke Studio
    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    // Relasi ke Pemesanan
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    // Relasi ke Kursi
    public function kursis()
    {
        return $this->hasMany(Kursi::class);
    }
}
