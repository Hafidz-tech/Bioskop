<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    // Relasi ke Pemesanan
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function ratings()
{
    return $this->hasMany(Rating::class);
}

}
