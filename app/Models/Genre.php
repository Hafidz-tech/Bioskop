<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    // Tambahkan relasi ini DI DALAM kelas Genre
    public function films()
    {
        return $this->hasMany(Film::class);
    }
}
