@extends('layouts.user')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
            <h3 class="mb-3 text-center">
                Pilih Kursi – <span class="text-primary">{{ $jadwal->film->judul }}</span>
            </h3>
            
            <div class="mb-4 text-center text-muted">
                <strong>1. Tanggal &amp; Jam:</strong> {{ $jadwal->tanggal }} – {{ $jadwal->jam_mulai }}<br>
                <strong>2. Nomor Studio:</strong> Studio {{ $jadwal->studio->nama }}<br>
                <strong>3. Harga per Kursi:</strong>
                <span class="fw-semibold">Rp {{ number_format($jadwal->film->harga,2,',','.') }}</span>
            </div>

            @if ($errors->has('kursi_sudah_dipesan'))
            <div class="alert alert-danger">
                Beberapa kursi sudah dibayar oleh orang lain. Silakan pilih kursi lain.
            </div>
            @endif

            @if ($errors->any() && !$errors->has('kursi_sudah_dipesan'))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="form-pemesanan" method="POST"
                  action="{{ route('user.pemesanan.store', $pemesananId ?? '') }}"
                  enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                <input type="hidden" name="jumlah_tiket" id="jumlah_tiket" value="0">
                <input type="hidden" name="total_harga" id="total_harga" value="0">
                <div id="kursi_inputs"></div>

                @php
                    $grouped = $kursis->groupBy(fn($k) => substr($k->nomor_kursi, 0, 1));
                    $harga = $jadwal->film->harga;
                @endphp
                <div class="mb-4">
                    <label class="form-label fw-semibold">Pilih Kursi:</label>
                    <div class="border rounded-4 bg-light p-3">
                        @foreach($grouped as $row => $ks)
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:40px;font-weight:bold">{{ $row }}</div>
                            <div class="d-flex flex-wrap" style="gap:10px">
                                @foreach($ks as $kursi)
                                <div>
                                    <input type="checkbox"
                                           class="btn-check"
                                           id="k{{ $kursi->id }}"
                                           data-id="{{ $kursi->id }}"
                                           autocomplete="off"
                                           {{ $kursi->sudah_dipesan ? 'disabled' : '' }}>
                                    <label for="k{{ $kursi->id }}"
                                           class="btn btn-sm {{ $kursi->sudah_dipesan ? 'btn-primary disabled' : 'btn-outline-primary' }}"
                                           style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:8px">
                                        {{ $kursi->nomor_kursi }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Legenda Kursi --}}
                <div class="mt-3 px-3">
                    <div class="d-flex justify-content-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <span class="btn btn-sm btn-outline-primary p-0" style="width: 28px; height: 28px; border-radius: 6px;"></span>
                            <span>Tersedia</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <span class="btn btn-sm btn-primary disabled p-0" 
                                  style="width: 28px; height: 28px; opacity: 0.7; border-radius: 6px;"></span>
                            <span>Sudah Dipesan</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <span class="btn btn-sm btn-primary p-0" style="width: 28px; height: 28px; border-radius: 6px;"></span>
                            <span>Dipilih</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('user.jadwal.index') }}"
                       class="btn btn-outline-secondary rounded-3 px-4">&larr; Kembali</a>
                    <button type="button" id="btn-pesan" class="btn btn-primary rounded-3 px-4">
                        Pesan &amp; Bayar
                    </button>
                </div>

                <!-- Modal Pembayaran -->
                <div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                      <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title" id="modalBayarLabel">Konfirmasi Pemesanan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <p><strong>Jumlah Kursi:</strong> <span id="mJumlah">0</span></p>
                          <p><strong>Total Harga:</strong> <span id="mTotal">Rp 0</span></p>
                        </div>
                        <div class="mb-3">
                          <label for="buktiPembayaran" class="form-label fw-semibold">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                          <input class="form-control" type="file" id="buktiPembayaran" name="bukti_pembayaran" accept="image/*">
                          <div id="buktiError" class="text-danger mt-1" style="display:none;">Bukti pembayaran wajib diunggah.</div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btn-submit-final" class="btn btn-success rounded-3">Kirim &amp; Bayar</button>
                      </div>
                    </div>
                  </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const harga = {{ $harga }};
  const btn = document.getElementById('btn-pesan');
  const modal = new bootstrap.Modal(document.getElementById('modalBayar'));
  const jumlahEl = document.getElementById('mJumlah');
  const totalEl = document.getElementById('mTotal');
  const form = document.getElementById('form-pemesanan');
  const buktiInput = document.getElementById('buktiPembayaran');
  const buktiError = document.getElementById('buktiError');
  const submitFinal = document.getElementById('btn-submit-final');

  // Toggle warna label saat kursi dicentang
  document.querySelectorAll('input.btn-check').forEach(cb => {
    cb.addEventListener('change', () => {
      const label = document.querySelector(`label[for="${cb.id}"]`);
      if (cb.checked) {
        label.classList.remove('btn-outline-primary');
        label.classList.add('btn-primary');
      } else {
        label.classList.add('btn-outline-primary');
        label.classList.remove('btn-primary');
      }
    });
  });

  btn.addEventListener('click', () => {
    const checked = Array.from(document.querySelectorAll('input.btn-check'))
      .filter(i => i.checked && !i.disabled)
      .map(i => +i.dataset.id);

    if (!checked.length) {
      return alert('Pilih minimal 1 kursi.');
    }

    const total = checked.length * harga;
    jumlahEl.textContent = checked.length;
    totalEl.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);

    document.getElementById('jumlah_tiket').value = checked.length;
    document.getElementById('total_harga').value = total;

    const kursiContainer = document.getElementById('kursi_inputs');
    kursiContainer.innerHTML = '';
    checked.forEach(id => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'kursi_ids[]';
      input.value = id;
      kursiContainer.appendChild(input);
    });

    buktiInput.value = ''; // Reset file input
    buktiError.style.display = 'none';
    modal.show();
  });

  submitFinal.addEventListener('click', () => {
    if (!buktiInput.files.length) {
      buktiError.style.display = 'block';
      return;
    }

    form.submit();
  });
});
</script>

@endsection
