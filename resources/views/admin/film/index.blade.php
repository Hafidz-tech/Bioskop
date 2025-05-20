@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <h3 class="mb-0 text-center">Daftar Film</h3>
        </div>

        <div class="card-body">
            {{-- Tombol Tambah + Form Pencarian --}}
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalTambahFilm">
                        Tambah Film
                    </button>
                </div>
                <div class="col-md-9">
                    <form method="GET" action="{{ route('admin.film.index') }}">
                        <div class="input-group">
                            <select name="per_page" onchange="this.form.submit()" class="form-select" style="max-width: 150px;">
                                @foreach ([5, 10, 15, 20] as $option)
                                    <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>
                                        {{ $option }} / halaman
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Judul Film">
                            <button class="btn btn-outline-success" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Film --}}
            @if ($films->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Durasi</th>
                                <th>Genre</th>
                                <th>Rating</th>
                                <th>Harga</th>
                                <th>Poster</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($films as $film)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration + ($films->currentPage() - 1) * $films->perPage() }}</td>
                                    <td class="align-middle">{{ $film->judul }}</td>
                                    <td class="align-middle">{{ $film->durasi }} menit</td>
                                    <td class="align-middle">{{ $film->genre->nama ?? '-' }}</td>
                                    <td class="align-middle">
                                        @php
                                            $rating = $film->ratings->avg('nilai');
                                        @endphp
                                        {{ $rating ? number_format($rating, 1) . ' / 5' : 'Belum ada rating' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ 'Rp ' . number_format($film->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle">
                                        @if ($film->poster)
                                            <img src="{{ asset('storage/' . $film->poster) }}" width="60">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditFilm{{ $film->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.film.destroy', $film->id) }}" method="POST" class="d-inline delete-film-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-title="{{ $film->judul }}">Hapus</button>
                                        </form>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="modalEditFilm{{ $film->id }}" tabindex="-1" aria-labelledby="modalEditFilmLabel{{ $film->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <form action="{{ route('admin.film.update', $film->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalEditFilmLabel{{ $film->id }}">Edit Film</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{-- Judul --}}
                                                        <div class="mb-3">
                                                            <label for="judul{{ $film->id }}" class="form-label">Judul</label>
                                                            <input type="text" name="judul" id="judul{{ $film->id }}" class="form-control" value="{{ old('judul', $film->judul) }}">
                                                            @error('judul')
                                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        {{-- Sinopsis --}}
                                                        <div class="mb-3">
                                                            <label for="sinopsis{{ $film->id }}" class="form-label">Sinopsis</label>
                                                            <textarea name="sinopsis" id="sinopsis{{ $film->id }}" class="form-control" rows="4">{{ old('sinopsis', $film->sinopsis) }}</textarea>
                                                            @error('sinopsis')
                                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        {{-- Durasi --}}
                                                        <div class="mb-3">
                                                            <label for="durasi{{ $film->id }}" class="form-label">Durasi (menit)</label>
                                                            <input type="number" name="durasi" id="durasi{{ $film->id }}" class="form-control" value="{{ old('durasi', $film->durasi) }}">
                                                            @error('durasi')
                                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        {{-- Genre --}}
                                                        <div class="mb-3">
                                                            <label for="genre_id{{ $film->id }}" class="form-label">Genre</label>
                                                            <select name="genre_id" id="genre_id{{ $film->id }}" class="form-select">
                                                                <option value="">-- Pilih Genre --</option>
                                                                @foreach ($genres as $genre)
                                                                    <option value="{{ $genre->id }}" {{ old('genre_id', $film->genre_id) == $genre->id ? 'selected' : '' }}>
                                                                        {{ $genre->nama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('genre_id')
                                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        {{-- Harga --}}
                                                        <div class="mb-3">
                                                            <label for="harga{{ $film->id }}" class="form-label">Harga (Rp)</label>
                                                            <input type="number" name="harga" id="harga{{ $film->id }}" class="form-control" value="{{ old('harga', $film->harga) }}" step="1" min="0">
                                                            @error('harga')
                                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        {{-- Poster --}}
                                                        <div class="mb-3">
                                                            <label for="poster{{ $film->id }}" class="form-label">Ganti Poster (Opsional)</label>
                                                            <input type="file" name="poster" id="poster{{ $film->id }}" class="form-control" accept="image/*">
                                                            @if ($film->poster)
                                                                <div class="mt-2">
                                                                    <img src="{{ asset('storage/' . $film->poster) }}" width="100" alt="Poster Lama">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        {{ $films->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="alert alert-info">Tidak ada data film</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tambah Film --}}
<div class="modal fade" id="modalTambahFilm" tabindex="-1" aria-labelledby="modalTambahFilmLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.film.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahFilmLabel">Tambah Film</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                {{-- Judul --}}
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul') }}">
                    @error('judul')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Sinopsis --}}
                <div class="mb-3">
                    <label for="sinopsis" class="form-label">Sinopsis</label>
                    <textarea name="sinopsis" id="sinopsis" class="form-control" rows="4">{{ old('sinopsis') }}</textarea>
                    @error('sinopsis')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Durasi --}}
                <div class="mb-3">
                    <label for="durasi" class="form-label">Durasi (menit)</label>
                    <input type="number" name="durasi" id="durasi" class="form-control" value="{{ old('durasi') }}">
                    @error('durasi')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Genre --}}
                <div class="mb-3">
                    <label for="genre_id" class="form-label">Genre</label>
                    <select name="genre_id" id="genre_id" class="form-select">
                        <option value="">-- Pilih Genre --</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->nama }}</option>
                        @endforeach
                    </select>
                    @error('genre_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Harga --}}
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" id="harga" class="form-control" value="{{ old('harga') }}" step="1" min="0">
                    @error('harga')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Poster --}}
                <div class="mb-3">
                    <label for="poster" class="form-label">Poster</label>
                    <input type="file" name="poster" id="poster" class="form-control" accept="image/*">
                    @error('poster')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- SweetAlert2 Hapus Confirmation --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const filmTitle = this.getAttribute('data-title') || 'film ini';

            Swal.fire({
                title: `Yakin mau hapus Film ini?`,
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else if (result.isDismissed) {
                    Swal.fire('Dibatalkan', 'Film tidak dihapus.', 'info');
                }
            });
        });
    });
});
</script>
@endsection
