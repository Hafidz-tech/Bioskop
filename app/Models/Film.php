<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Jadwal;
    

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul', 'sinopsis', 'durasi', 'genre', 'poster', 'rating','harga'
    ];

    // Relasi ke Jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function genre()
{
    return $this->belongsTo(Genre::class);
}

public function averageRating()
{
    return $this->ratings()->avg('nilai');
}


}