@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <h3 class="mb-0 text-center">Kelola Genre Film</h3>
        </div>

        <div class="card-body">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            {{-- Validasi Error --}}
            @if ($errors->has('nama'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first('nama') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            {{-- Tombol Tambah & Pencarian --}}
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalTambahGenre">
                        Tambah Genre
                    </button>
                </div>
                <div class="col-md-9">
                    <form method="GET" action="{{ route('admin.genre.index') }}">
                        <div class="input-group">
                            <select name="per_page" onchange="this.form.submit()" class="form-select" style="max-width: 150px;">
                                @foreach ([5, 10, 15, 20] as $option)
                                    <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>
                                        {{ $option }} / halaman
                                    </option>
                                @endforeach
                            </select>

                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Genre">
                            <button class="btn btn-outline-success" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            @if ($genres->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama Genre</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($genres as $genre)
                                <tr>
                                    <td>{{ $loop->iteration + ($genres->currentPage() - 1) * $genres->perPage() }}</td>
                                    <td>{{ $genre->nama }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditGenre{{ $genre->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.genre.destroy', $genre->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Modal Edit Genre --}}
                @foreach ($genres as $genre)
                    <div class="modal fade" id="modalEditGenre{{ $genre->id }}" tabindex="-1" aria-labelledby="modalEditGenreLabel{{ $genre->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.genre.update', $genre->id) }}" method="POST" class="modal-content">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditGenreLabel{{ $genre->id }}">Edit Genre</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nama{{ $genre->id }}" class="form-label">Nama Genre</label>
                                        <input type="text" name="nama" id="nama{{ $genre->id }}" class="form-control" value="{{ old('nama', $genre->nama) }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        {{ $genres->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            @else
                <div class="alert alert-info">Tidak ada data genre</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tambah Genre --}}
<div class="modal fade" id="modalTambahGenre" tabindex="-1" aria-labelledby="modalTambahGenreLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.genre.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahGenreLabel">Tambah Genre Film</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Genre</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Bootstrap JS agar modal berfungsi --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Script SweetAlert Hapus --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const form = this.closest('form');

            Swal.fire({
                title: 'Yakin hapus genre ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else if (result.isDismissed) {
                    Swal.fire('Dibatalkan', 'Genre tidak dihapus.', 'info');
                }
            });
        });
    });
});
</script>
@endsection
