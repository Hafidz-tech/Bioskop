@extends('layouts.user')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm rounded-4 p-4">
        <h2 class="mb-4 text-center">Jadwal Film</h2>

        {{-- Tanggal Tab --}}
        <ul class="nav nav-pills mb-4 justify-content-center">
            @foreach ($tanggalList as $tanggal)
                <li class="nav-item">
                    <a class="nav-link {{ $tanggal == $tanggalTerpilih ? 'active' : '' }}"
                        href="{{ route('user.jadwal.index', ['tanggal' => $tanggal]) }}">
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('D, d M') }}
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Jadwal Film --}}
        @forelse ($jadwals as $filmJadwals)
            @php
                $film = $filmJadwals->first()->film;
            @endphp
            <div class="card mb-4 shadow-sm rounded-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        {{ $film->judul }}
                        <small class="text-warning">({{ $film->genre->nama ?? '-' }})</small>
                    </h5>
                    <div class="mt-1">
                        <small class="text-light">
                            Harga: <strong>Rp {{ number_format($film->harga, 2, ',', '.') }}</strong>
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($filmJadwals as $jadwal)
                            @php
                                // total kursi di studio
                                $totalKursi = $jadwal->studio->kursis->count();

                                // hitung kursi terisi: hanya dari pemesanan yang sudah dibayar ('paid')
                                $terisi = $jadwal
                                    ->pemesanans()
                                    ->whereHas('pembayaran', function($q) {
                                        $q->where('status', 'paid');
                                    })
                                    ->with('kursi')
                                    ->get()
                                    ->flatMap->kursi
                                    ->count();

                                $sisaKursi = $totalKursi - $terisi;
                            @endphp
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 d-flex flex-column justify-content-between shadow-sm">
                                    <div>
                                        <p class="mb-1"><strong>Studio:</strong> {{ $jadwal->studio->nama }}</p>
                                        <p class="mb-1"><strong>Jam:</strong> {{ \Carbon\Carbon::parse($jadwal->jam)->format('H:i') }}</p>
                                        <p class="mb-1"><strong>Sisa Kursi:</strong> {{ $sisaKursi }} / {{ $totalKursi }}</p>
                                    </div>
                                    <a href="{{ route('user.jadwal.show', $jadwal->id) }}"
                                        class="btn btn-primary mt-3 w-100">
                                        Pesan Tiket
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center">
                Tidak ada jadwal film untuk tanggal ini.
            </div>
        @endforelse
    </div>
</div>

@endsection
