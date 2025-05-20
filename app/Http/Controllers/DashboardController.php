<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function redirect()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }

    public function admin()
    {
        return view('admin.dashboard');
    }

    public function user()
    {
        return view('user.dashboard');
    }

    public function index()
{
    $films = \App\Models\Film::with('genre', 'jadwal')->get();
    return view('user.dashboard', compact('films'));
}

}
