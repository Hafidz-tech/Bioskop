@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    {{-- ✅ Tampilkan pesan sukses atau error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <h3 class="mb-0 text-center">Kelola Jadwal Tayang</h3>
        </div>

        <div class="card-body">
            {{-- Tombol Tambah dan Form Pencarian --}}
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                        Tambah Jadwal
                    </button>
                </div>
                <div class="col-md-9">
                    <form method="GET" action="{{ route('admin.jadwal.index') }}">
                        <div class="input-group">
                            <select name="per_page" onchange="this.form.submit()" class="form-select" style="max-width: 150px;">
                                @foreach ([5, 10, 15, 20] as $option)
                                    <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>{{ $option }} / halaman</option>
                                @endforeach
                            </select>

                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Film atau Studio">
                            <button class="btn btn-outline-success" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            @if ($jadwals->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Film</th>
                                <th>Studio</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwals as $jadwal)
                                <tr>
                                    <td>{{ $loop->iteration + ($jadwals->currentPage() - 1) * $jadwals->perPage() }}</td>
                                    <td>{{ $jadwal->film->judul }}</td>
                                    <td>{{ $jadwal->studio->nama }}</td>
                                    <td>{{ $jadwal->tanggal }}</td>
                                    <td>{{ $jadwal->jam }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditJadwal" data-id="{{ $jadwal->id }}" data-film="{{ $jadwal->film->id }}" data-studio="{{ $jadwal->studio->id }}" data-tanggal="{{ $jadwal->tanggal }}" data-jam="{{ $jadwal->jam }}">
                                            Edit
                                        </button>

                                        <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" class="d-inline form-delete">
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

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        {{ $jadwals->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="alert alert-info">Tidak ada data jadwal</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tambah Jadwal --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal Tayang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="film_id" class="form-label">Film</label>
                    <select name="film_id" id="film_id" class="form-select" required>
                        <option value="">-- Pilih Film --</option>
                        @foreach ($films as $film)
                            <option value="{{ $film->id }}">{{ $film->judul }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="studio_id" class="form-label">Studio</label>
                    <select name="studio_id" id="studio_id" class="form-select" required>
                        <option value="">-- Pilih Studio --</option>
                        @foreach ($studios as $studio)
                            <option value="{{ $studio->id }}">{{ $studio->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="jam" class="form-label">Jam</label>
                    <input type="time" name="jam" id="jam" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Jadwal --}}
<div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-labelledby="modalEditJadwalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.jadwal.update', ':id') }}" method="POST" class="modal-content" id="formEditJadwal">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditJadwalLabel">Edit Jadwal Tayang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="edit_film_id" class="form-label">Film</label>
                    <select name="film_id" id="edit_film_id" class="form-select" required>
                        <option value="">-- Pilih Film --</option>
                        @foreach ($films as $film)
                            <option value="{{ $film->id }}">{{ $film->judul }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit_studio_id" class="form-label">Studio</label>
                    <select name="studio_id" id="edit_studio_id" class="form-select" required>
                        <option value="">-- Pilih Studio --</option>
                        @foreach ($studios as $studio)
                            <option value="{{ $studio->id }}">{{ $studio->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit_tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit_jam" class="form-label">Jam</label>
                    <input type="time" name="jam" id="edit_jam" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Bootstrap JS agar modal berfungsi --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Script untuk mengisi data modal edit --}}
<script>
    // Set min tanggal saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function () {
        const today = new Date().toISOString().split('T')[0];

        // Modal Tambah
        const inputTanggalTambah = document.getElementById('tanggal');
        if (inputTanggalTambah) {
            inputTanggalTambah.setAttribute('min', today);
        }

        // Modal Edit — kita set ulang setiap modal dibuka nanti
        const modalEditJadwal = document.getElementById('modalEditJadwal');
        modalEditJadwal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const filmId = button.getAttribute('data-film');
            const studioId = button.getAttribute('data-studio');
            const tanggal = button.getAttribute('data-tanggal');
            const jam = button.getAttribute('data-jam');

            const form = document.getElementById('formEditJadwal');
            form.action = form.action.replace(':id', id);

            document.getElementById('edit_film_id').value = filmId;
            document.getElementById('edit_studio_id').value = studioId;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_jam').value = jam;

            // Atur min tanggal saat modal edit dibuka
            const inputTanggalEdit = document.getElementById('edit_tanggal');
            if (inputTanggalEdit) {
                inputTanggalEdit.setAttribute('min', today);
            }
        });
    });
</script>

{{-- Script SweetAlert Hapus Jadwal --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const form = this.closest('form');

            Swal.fire({
                title: 'Yakin hapus jadwal ini?',
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
                    Swal.fire('Dibatalkan', 'Jadwal tidak dihapus.', 'info');
                }
            });
        });
    });
});
</script>
@endsection
