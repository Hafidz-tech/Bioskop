<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Film;

class DashboardController extends Controller
{
    public function index()
    {
        $films = Film::all();

        return view('user.dashboard', [
            'films' => $films,
        ]);
    }
}
