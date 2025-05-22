@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <h3 class="mb-0 text-center">Kelola Studio</h3>
        </div>

        <div class="card-body">
            {{-- Tombol Tambah dan Form Pencarian --}}
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalTambahStudio">
                        Tambah Studio
                    </button>
                </div>
                <div class="col-md-9">
                    <form method="GET" action="{{ route('admin.studio.index') }}">
                        <div class="input-group">
                            <select name="per_page" onchange="this.form.submit()" class="form-select" style="max-width: 150px;">
                                @foreach ([5, 10, 15, 20] as $option)
                                    <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>{{ $option }} / halaman</option>
                                @endforeach
                            </select>

                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Studio">
                            <button class="btn btn-outline-success" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            @if ($studios->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="align-middle">No</th>
                                <th class="align-middle">Nama Studio</th>
                                <th class="align-middle">Kapasitas</th>
                                <th class="align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($studios as $studio)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration + ($studios->currentPage() - 1) * $studios->perPage() }}</td>
                                    <td class="align-middle">{{ $studio->nama }}</td>
                                    <td class="align-middle">{{ $studio->kapasitas }} kursi</td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditStudio{{ $studio->id }}">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKursi{{ $studio->id }}">
                                            Tambah Kursi
                                        </button>
                                        <form action="{{ route('admin.studio.destroy', $studio->id) }}" method="POST" class="d-inline delete-form" data-nama="{{ $studio->nama }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        {{ $studios->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="alert alert-info">Tidak ada data studio</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tambah Studio --}}
<div class="modal fade" id="modalTambahStudio" tabindex="-1" aria-labelledby="modalTambahStudioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.studio.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahStudioLabel">Tambah Studio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Studio</label>
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

{{-- Modal Edit Studio --}}
@foreach ($studios as $studio)
<div class="modal fade" id="modalEditStudio{{ $studio->id }}" tabindex="-1" aria-labelledby="modalEditStudioLabel{{ $studio->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.studio.update', $studio->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditStudioLabel{{ $studio->id }}">Edit Studio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama{{ $studio->id }}" class="form-label">Nama Studio</label>
                    <input type="text" name="nama" id="nama{{ $studio->id }}" class="form-control" value="{{ old('nama', $studio->nama) }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Kursi di Studio --}}
<div class="modal fade" id="modalTambahKursi{{ $studio->id }}" tabindex="-1" aria-labelledby="modalTambahKursiLabel{{ $studio->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.kursi.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="studio_id" value="{{ $studio->id }}">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahKursiLabel{{ $studio->id }}">Tambah Kursi di Studio {{ $studio->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nomor_kursi" class="form-label">Nomor Kursi</label>
                    <input type="text" name="nomor_kursi" id="nomor_kursi" class="form-control" value="{{ old('nomor_kursi') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah Kursi</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- SweetAlert dan Konfirmasi Hapus --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Tampilkan SweetAlert untuk success
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    @endif

    // Tampilkan SweetAlert untuk error khusus jika ada kursi tertaut
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '{{ session('error') === "Data gagal dihapus karena masih ada kursi yang tertaut" ? "Gagal Hapus" : "Error" }}',
            text: '{{ session('error') }}',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    @endif

    // Konfirmasi hapus dengan SweetAlert
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const namaStudio = this.dataset.nama || 'studio ini';

            Swal.fire({
                title: `Yakin hapus ${namaStudio}?`,
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

});
</script>

@endsection
