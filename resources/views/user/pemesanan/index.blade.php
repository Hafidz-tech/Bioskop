@extends('layouts.user')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <h3 class="mb-0 text-center">Riwayat Pemesanan Tiket</h3>
        </div>

        <div class="card-body">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filter & Search --}}
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 mb-2">
                    {{-- Bisa ditambahkan filter jika perlu --}}
                </div>
                <div class="col-md-9">
                    <form method="GET" action="{{ route('user.pemesanan.index') }}">
                        <div class="row g-2">
                            <div class="col-auto">
                                <select name="per_page" onchange="this.form.submit()" class="form-select">
                                    @foreach ([5, 10, 15, 20] as $option)
                                        <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>
                                            {{ $option }} / halaman
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-auto">
                                <select name="status" onchange="this.form.submit()" class="form-select">
                                    <option value="">Semua Status</option>
                                    @foreach (['pending', 'waiting', 'paid'] as $statusOption)
                                        <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>
                                            {{ ucfirst($statusOption) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari film">
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-outline-success" type="submit">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            @if ($pemesanans->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Judul Film</th>
                                <th>Studio</th>
                                <th>Jumlah Tiket</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($pemesanans as $pemesanan)
                                <tr>
                                    <td>{{ $loop->iteration + ($pemesanans->currentPage() - 1) * $pemesanans->perPage() }}</td>
                                    <td>{{ $pemesanan->jadwal->film->judul }}</td>
                                    <td>{{ $pemesanan->jadwal->studio->nama }}</td>
                                    <td>{{ $pemesanan->jumlah_tiket }}</td>
                                    <td>Rp{{ number_format((int) $pemesanan->total_harga) }}</td>
                                    <td>
                                        @php
                                            $pembayaran = $pemesanan->pembayaran;
                                            $statusPembayaran = $pembayaran ? $pembayaran->status : 'waiting';
                                        @endphp
                                        <span class="badge
                                            @if($statusPembayaran === 'paid')
                                                bg-success
                                            @elseif($statusPembayaran === 'waiting')
                                                bg-warning
                                            @elseif($statusPembayaran === 'pending')
                                                bg-info
                                            @else
                                                bg-secondary
                                            @endif
                                        ">
                                            {{ ucfirst($statusPembayaran) }}
                                        </span>
                                    </td>
                                    <td>{{ $pemesanan->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if (!$pembayaran)
                                            {{-- Form Upload Bukti Pembayaran --}}
                                            <form action="{{ route('user.pemesanan.payment', $pemesanan->id) }}" method="POST" enctype="multipart/form-data" class="d-inline-block">
                                                @csrf
                                                <input type="file" name="bukti_pembayaran" required>
                                                <button type="submit" class="btn btn-warning btn-sm mt-1">Upload Bukti</button>
                                            </form>
                                        @else
                                            <small>Bukti pembayaran sudah diupload</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    {{ $pemesanans->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @else   
                <div class="alert alert-info">Tidak ada data pemesanan.</div>
            @endif
        </div>
    </div>
</div>
@endsection
