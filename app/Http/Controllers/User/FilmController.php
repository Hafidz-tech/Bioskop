<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Film;

class FilmController extends Controller
{
    public function show($id)
    {
        $film = Film::findOrFail($id);
        return view('user.show', compact('film'));
    }
}
