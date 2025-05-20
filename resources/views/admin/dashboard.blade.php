@extends('layouts.admin')

@section('content')
    <h1 class="mb-4 text-center">Dashboard Admin</h1>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-4">
        <div class="col">
            <a href="{{ route('admin.film.index') }}" class="card-hover card text-white bg-primary shadow-sm text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title">Total Film</h5>
                    <p class="card-text display-6">{{ $totalFilm }}</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.studio.index') }}" class="card-hover card text-white bg-success shadow-sm text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title">Total Studio</h5>
                    <p class="card-text display-6">{{ $totalStudio }}</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.jadwal.index') }}" class="card-hover card text-white bg-warning shadow-sm text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title">Total Jadwal</h5>
                    <p class="card-text display-6">{{ $totalJadwal }}</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.pemesanan.index') }}" class="card-hover card text-white bg-danger shadow-sm text-decoration-none">
                <div class="card-body">
                    <h5 class="card-title">Pemesanan</h5>
                    <p class="card-text display-6">{{ $totalPemesanan }}</p>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease-in-out;
            display: block;
        }

        .card-hover:hover {
            transform: scale(1.05);
            box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.3);
            z-index: 2;
        }

        .card-hover:active {
            transform: scale(0.98);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection
