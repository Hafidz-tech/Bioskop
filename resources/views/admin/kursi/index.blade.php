@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <h3 class="mb-0 text-center">Kelola Kursi</h3>
        </div>

        <div class="card-body">
            {{-- Pesan Error Validation --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tombol Tambah dan Pencarian --}}
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalTambahKursi">
                        Tambah Kursi
                    </button>
                </div>
                <div class="col-md-9">
                    <form method="GET" action="{{ route('admin.kursi.index') }}">
                        <div class="input-group">
                            <select name="per_page" onchange="this.form.submit()" class="form-select" style="max-width: 130px;">
                                @foreach ([5, 10, 15, 20] as $option)
                                    <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>{{ $option }} / halaman</option>
                                @endforeach
                            </select>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Studio atau Kursi">
                            <button class="btn btn-outline-success" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            @if ($kursis->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Studio</th>
                                <th>Nomor Kursi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kursis as $kursi)
                                <tr>
                                    <td>{{ $loop->iteration + ($kursis->currentPage() - 1) * $kursis->perPage() }}</td>
                                    <td>{{ $kursi->studio->nama }}</td>
                                    <td>{{ $kursi->nomor_kursi }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditKursi{{ $kursi->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.kursi.destroy', $kursi->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                        </form>

                                        {{-- Modal Edit --}}
                                        <div class="modal fade" id="modalEditKursi{{ $kursi->id }}" tabindex="-1" aria-labelledby="modalEditKursiLabel{{ $kursi->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.kursi.update', $kursi->id) }}" method="POST" class="modal-content">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalEditKursiLabel{{ $kursi->id }}">Edit Kursi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="studio_id{{ $kursi->id }}" class="form-label">Studio</label>
                                                            <select name="studio_id" id="studio_id{{ $kursi->id }}" class="form-select">
                                                                @foreach ($studios as $studio)
                                                                    <option value="{{ $studio->id }}" {{ $kursi->studio_id == $studio->id ? 'selected' : '' }}>{{ $studio->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="nomor_kursi{{ $kursi->id }}" class="form-label">Nomor Kursi</label>
                                                            <input type="text" name="nomor_kursi" id="nomor_kursi{{ $kursi->id }}" class="form-control" value="{{ old('nomor_kursi', $kursi->nomor_kursi) }}">
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
                        {{ $kursis->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">Tidak ada data kursi</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tambah Kursi --}}
<div class="modal fade" id="modalTambahKursi" tabindex="-1" aria-labelledby="modalTambahKursiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.kursi.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahKursiLabel">Tambah Kursi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="studio_id" class="form-label">Studio</label>
                    <select name="studio_id" id="studio_id" class="form-select">
                        <option value="">-- Pilih Studio --</option>
                        @foreach ($studios as $studio)
                            <option value="{{ $studio->id }}" {{ old('studio_id') == $studio->id ? 'selected' : '' }}>{{ $studio->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nomor_kursi" class="form-label">Nomor Kursi</label>
                    <input type="text" name="nomor_kursi" id="nomor_kursi" class="form-control" value="{{ old('nomor_kursi') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- SweetAlert2 Success dan Error --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        @endif
    });
</script>

{{-- SweetAlert Hapus --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const form = this.closest('form');

            Swal.fire({
                title: 'Yakin hapus kursi ini?',
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
                    Swal.fire('Dibatalkan', 'Kursi tidak dihapus.', 'info');
                }
            });
        });
    });
});
</script>

<style>
    .modal-body .form-label {
        text-align: left;
        display: block;
    }
</style>
@endsection
