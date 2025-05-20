@extends('layouts.user')

@section('content')
    <h1>Dashboard Pengguna</h1> 
    <p>Selamat datang, {{ Auth::user()->name }}! Kamu login sebagai <strong>User</strong>.</p>

    <div class="row">
    @foreach ($films as $film)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <a href="{{ route('user.film.show', $film->id) }}">
                    <img src="{{ asset('storage/' . $film->poster) }}" class="card-img-top" alt="{{ $film->judul }}">
                </a>
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $film->judul }}</h5>
                </div>
            </div>
        </div>
    @endforeach
</div>


    
@endsection
