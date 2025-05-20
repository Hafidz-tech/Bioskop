<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'kapasitas', 'layout'
    ];

    // Relasi ke Jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    // Relasi ke Kursi
    public function kursis()
    {
        return $this->hasMany(Kursi::class);
    }

    public function getKapasitasAttribute()
{
    return $this->kursis()->count();
}

}
