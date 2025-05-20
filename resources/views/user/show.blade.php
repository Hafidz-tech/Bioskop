@extends('layouts.user')

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Poster Film --}}
        <div class="col-md-4 text-center mb-4">
            <img src="{{ asset('storage/poster/' . $film->poster) }}" 
                 alt="{{ $film->judul }}" 
                 class="img-fluid rounded shadow" 
                 style="max-height: 400px; object-fit: cover;">
        </div>

        {{-- Detail Film --}}
        <div class="col-md-8">
            <h1 class="mb-3">{{ $film->judul }}</h1>

            <p><strong>Durasi:</strong> {{ $film->durasi }} menit</p>

            <p>
                <strong>Genre:</strong> 
                @if($film->genre)
                    {{ $film->genre->nama }}
                @else
                    -
                @endif
            </p>

            <h4>Sinopsis</h4>
            <p>{{ $film->sinopsis ?? 'Sinopsis belum tersedia.' }}</p>

            <hr>

            <h4>Jadwal Tayang</h4>
            @if ($film->jadwal->count())
                <ul class="list-group">
                    @foreach ($film->jadwal as $jadwal)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</strong> 
                                pukul {{ \Carbon\Carbon::parse($jadwal->jam)->format('H:i') }}
                            </div>
                            <span class="badge bg-primary rounded-pill">Studio {{ $jadwal->studio->nama }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Jadwal tayang belum tersedia.</p>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('user.jadwal.index') }}" class="btn btn-secondary">Kembali ke Daftar Film</a>
    </div>
</div>
@endsection
