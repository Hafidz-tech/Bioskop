@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-white border-bottom-0">
                <h3 class="mb-0 text-center">Kelola Pemesanan Tiket</h3>
            </div>

            <div class="card-body">
                {{-- Flash Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Filter --}}
                <div class="row mb-3">
                    <div class="col-md-3 mb-2"></div>
                    <div class="col-md-9">
                        <form method="GET" action="{{ route('admin.pemesanan.index') }}">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <select name="per_page" onchange="this.form.submit()" class="form-select">
                                        @foreach ([5, 10, 15, 20] as $option)
                                            <option value="{{ $option }}"
                                                {{ request('per_page') == $option ? 'selected' : '' }}>
                                                {{ $option }} / halaman
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <select name="status" onchange="this.form.submit()" class="form-select">
                                        <option value="">Semua Status</option>
                                        @foreach (['waiting', 'pending', 'paid'] as $statusOption)
                                            <option value="{{ $statusOption }}"
                                                {{ request('status') == $statusOption ? 'selected' : '' }}>
                                                {{ ucfirst($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Cari nama / film">
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
                                    <th>Nama</th>
                                    <th>Film</th>
                                    <th>Studio</th>
                                    <th>Tiket</th>
                                    <th>Total Harga</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($pemesanans as $pemesanan)
                                    @php
                                        $status = $pemesanan->pembayaran->status ?? 'waiting';
                                        $bukti = $pemesanan->pembayaran->bukti_pembayaran ?? null;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration + ($pemesanans->currentPage() - 1) * $pemesanans->perPage() }}
                                        </td>
                                        <td>{{ $pemesanan->user->name }}</td>
                                        <td>{{ $pemesanan->jadwal->film->judul }}</td>
                                        <td>{{ $pemesanan->jadwal->studio->nama }}</td>
                                        <td>{{ $pemesanan->jumlah_tiket }}</td>
                                        <td>Rp{{ number_format((int) $pemesanan->total_harga) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-{{ $bukti ? 'primary' : 'secondary' }}"
                                                data-bs-toggle="modal" data-bs-target="#buktiModal{{ $pemesanan->id }}">
                                                {{ $bukti ? 'Lihat Bukti' : 'Belum Diupload' }}
                                            </button>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td>{{ $pemesanan->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            @if ($status === 'waiting' && $bukti)
                                                <form
                                                    action="{{ route('admin.pemesanan.updateStatus', [$pemesanan->id, 'paid']) }}"
                                                    method="POST" class="d-inline form-confirm"
                                                    data-nama="{{ $pemesanan->user->name }}  {{ $pemesanan->jadwal->film->judul }}"
                                                    data-aksi="mengonfirmasi">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-success me-1">Konfirmasi</button>
                                                </form>
                                                <form
                                                    action="{{ route('admin.pemesanan.updateStatus', [$pemesanan->id, 'pending']) }}"
                                                    method="POST" class="d-inline form-confirm"
                                                    data-nama="{{ $pemesanan->user->name }}  {{ $pemesanan->jadwal->film->judul }}"
                                                    data-aksi="menolak">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                                </form>
                                            @else
                                                <small class="text-muted">Tidak ada aksi</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Modal --}}
                    @foreach ($pemesanans as $pemesanan)
                        <div class="modal fade" id="buktiModal{{ $pemesanan->id }}" tabindex="-1"
                            aria-labelledby="buktiModalLabel{{ $pemesanan->id }}" aria-hidden="true">
                            <div class="modal-dialog {{ $bukti ? 'modal-lg' : '' }} modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="buktiModalLabel{{ $pemesanan->id }}">Bukti Pembayaran
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p><strong>Nama:</strong> {{ $pemesanan->user->name }}</p>
                                        <p><strong>Film:</strong> {{ $pemesanan->jadwal->film->judul }}</p>
                                        @if ($pemesanan->pembayaran && $pemesanan->pembayaran->bukti_pembayaran)
                                            <img src="{{ asset('storage/' . $pemesanan->pembayaran->bukti_pembayaran) }}"
                                                class="img-fluid rounded shadow" style="max-height: 400px;">
                                        @else
                                            <button class="btn btn-outline-secondary">Belum Diupload</button>
                                        @endif

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-confirm').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const nama = this.dataset.nama;
                    const aksi = this.dataset.aksi ?? 'mengubah status';
                    Swal.fire({
                        title: 'Yakin?',
                        text: `Apakah Anda yakin ingin ${aksi} pembayaran untuk "${nama}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
