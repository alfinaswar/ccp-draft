@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Detail Penilaian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Hta / Gpa</a></li>
                    <li class="breadcrumb-item active">Detail Penilaian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Penilaian HTA / GPA
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">Informasi Barang</h5>
                        <table class="table align-middle" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:180px;">Nama Barang</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Tipe ?? '-' }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>File Penilaian yang Diupload</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @forelse ($data->getVendor as $vIdx => $Vendor)
                                        <div class="col-md-6 mb-3">
                                            <div class="card" style="border:1px solid #d7d7d7;">
                                                <div class="card-body">
                                                    <h6 class="mb-2">{{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}</h6>
                                                    @if (!empty($Vendor->getHtaGpa->File))
                                                        <ul>
                                                            <li>
                                                                <a href="{{ asset('storage/upload/gpa/' . $Vendor->getHtaGpa->File) }}"
                                                                    target="_blank">
                                                                    {{ $Vendor->getHtaGpa->File }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">Tidak ada file diupload.</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <span class="text-muted">Tidak ada vendor ditemukan.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan HTA / GPA</strong></h5>
                            <div class="mb-2 text-center">
                                @if (!empty($approval))
                                    <div class="row justify-content-center">
                                        @foreach ($approval as $item)
                                            <div class="col text-center" style="font-weight:600;">
                                                {{ $item->NamaJabatan ?? '-' }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless" style="max-width:100%; margin: 0 auto;">
                                    <colgroup>
                                        @if (!empty($approval))
                                            @foreach ($approval as $item)
                                                <col style="width: {{ 100 / count($approval) }}%;">
                                            @endforeach
                                        @endif
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="height:80px; vertical-align: top;">
                                                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                                            style="width:80px; height:80px;"><br>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="padding-bottom:0;">
                                                    <hr
                                                        style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center align-top">
                                                    <span style="font-weight:600;">
                                                        {{ $item->Nama ?? '-' }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $item->Status ?? '-' }}</small><br>
                                                    <small><em>
                                                            {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                                        </em></small>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            @foreach ($approval as $item)
                                @if (auth()->id() == ($item->UserId ?? null) && $item->Status != 'Approved' && !empty($item->ApprovalToken))
                                    <button type="button" class="btn btn-approve me-2"
                                        style="background-color: #28a745; color: #fff; border-color: #28a745;"
                                        data-bs-toggle="modal" data-bs-target="#modalJustifikasi"
                                        data-approval-token="{{ $item->ApprovalToken }}"
                                        data-approval-route="{{ route('htagpa.submitJustifikasi', $item->ApprovalToken) }}"
                                        data-jabatan="{{ $item->NamaJabatan ?? ($item->getJabatan->Nama ?? $item->JenisUser) }}"
                                        data-nama="{{ $item->Nama ?? 'Penilai' }}">
                                        <i class="fa fa-check"></i>
                                        Setujui
                                    </button>
                                @endif
                            @endforeach
                        </div>
                        @if (isset($approval) && count($approval) > 0)
                            <div class="mt-4">
                                <dl>
                                    @php $nomor = 1; @endphp
                                    @foreach ($approval as $item)
                                        @if (!empty($item->Justifikasi))
                                            <dt>
                                                <strong>{{ $nomor++ }}.
                                                    Justifikasi{{ $item->Nama ? ' oleh ' . $item->Nama : '' }}:</strong>
                                            </dt>
                                            <dd class="text-muted" style="margin-bottom:10px;">
                                                {{ $item->Justifikasi }}
                                            </dd>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                    </div>
                    @if (isset($approval) && count($approval) > 0)
                        <div class="mt-4">
                            <dl>
                                @php $nomor = 1; @endphp
                                @foreach ($approval as $item)
                                    @if (!empty($item->Justifikasi))
                                        <dt>
                                            <strong>{{ $nomor++ }}.
                                                Justifikasi{{ $item->Nama ? ' oleh ' . $item->Nama : '' }}:</strong>
                                        </dt>
                                        <dd class="text-muted" style="margin-bottom:10px;">
                                            {{ $item->Justifikasi }}
                                        </dd>
                                    @endif
                                @endforeach
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalJustifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-check-circle me-2"></i>Konfirmasi Persetujuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formApprove" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="justifikasi" class="form-label fw-bold">
                                Justifikasi Pembelian Alat <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('justifikasi') is-invalid @enderror" id="justifikasi" name="justifikasi"
                                rows="4" placeholder="Contoh: Spesifikasi alat sesuai kebutuhan, harga kompetitif, vendor terpercaya, dll."
                                required></textarea>
                            @error('justifikasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Justifikasi akan tercatat dalam history persetujuan.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fa fa-paper-plane me-1"></i>Kirim Persetujuan
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @if (Session::get('error'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#d33',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#d33',
                });
            }, 500);
        </script>
    @endif
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#28a745',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#28a745',
                });
            }, 500);
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiApprove(idx, penilaiKe) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: 'Apakah Anda yakin ingin menyetujui HTA/GPA ini sebagai Penilai ' + penilaiKe + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + idx).submit();
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalJustifikasi = document.getElementById('modalJustifikasi');
            const formApprove = document.getElementById('formApprove');
            const inputJustifikasi = document.getElementById('justifikasi');
            const modalJabatan = document.getElementById('modalJabatan');

            // Saat modal dibuka, isi data dinamis dari tombol yang diklik
            modalJustifikasi.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                // Ambil data dari data-attributes
                const token = button.getAttribute('data-approval-token');
                const route = button.getAttribute('data-approval-route');
                const jabatan = button.getAttribute('data-jabatan');

                // Set action form & tampilkan info jabatan
                formApprove.action = route;
                modalJabatan.textContent = jabatan;

                // Reset form & error saat modal dibuka
                inputJustifikasi.value = '';
                inputJustifikasi.classList.remove('is-invalid');
                const existingFeedback = inputJustifikasi.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            });

            // Validasi client-side sebelum submit
            formApprove.addEventListener('submit', function(e) {
                const justifikasi = inputJustifikasi.value.trim();

                if (!justifikasi) {
                    e.preventDefault();
                    inputJustifikasi.classList.add('is-invalid');

                    // Tambahkan pesan error jika belum ada
                    if (!inputJustifikasi.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.textContent = 'Justifikasi wajib diisi sebelum menyetujui';
                        inputJustifikasi.parentNode.appendChild(errorDiv);
                    }
                    inputJustifikasi.focus();
                    return false;
                }
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Memproses...';

            });

            // Reset tombol saat modal ditutup
            modalJustifikasi.addEventListener('hidden.bs.modal', function() {
                const submitBtn = formApprove.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Kirim Persetujuan';
            });
        });
    </script>
@endpush
