@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col">
            <h3 class="page-title">Pengajuan Pembelian</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Pengajuan Pembelian</a></li>
                <li class="breadcrumb-item active">Detail Pengajuan Pembelian</li>
            </ul>
        </div>
    </div>

    {{-- Form submit ke controller KirimApprovalDirekturGroup --}}
    <form id="formKirimEmail" action="{{ route('ajukan.kirim-approval-direktur-group', encrypt($data->id)) }}"
        method="POST">
        @csrf

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cek Tanda Tangan Direktur Group</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" id="btnCopyLinkApproval" onclick="copyApprovalLink()">
                        <i class="fa fa-link"></i> Copy Link Approval
                    </button>
                    <button type="button" class="btn btn-primary" id="btnSendNotification"
                        @if ($semuaApprove) disabled @endif>
                        <i class="fa fa-bell"></i> Kirim Notifikasi
                    </button>
                </div>



            </div>

            <div class="card-body">
                <div class="row">

                    {{-- HTA / GPA --}}
                    <div class="col-md-6 mb-1">
                        <div class="card">
                            <div class="card-header"><strong><i class="fa fa-file-signature mr-1 text-primary"></i> HTA /
                                    GPA</strong></div>
                            <div class="card-body">
                                @php $list = $approval['HtaGpa'] ?? collect(); @endphp
                                @if ($list->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($list as $item)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row align-items-md-center">
                                                <div class="mb-2 mb-md-0">
                                                    <strong>{{ $item->Nama ?? '-' }}</strong><br>
                                                    <small>Email: {{ $item->Email ?? '-' }}</small><br>
                                                    <small>
                                                        Tanggal Approve:
                                                        {{ !empty($item->TanggalApprove) ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                                    </small>
                                                </div>
                                                <div>
                                                    @php
                                                        $icon = '';
                                                        $badgeClass = '';
                                                        $txtStatus = '';
                                                        // Only allow Pending or Approve for display
                                                        if ($item->Status == 'Pending') {
                                                            $icon = '<i class="fa fa-clock"></i>';
                                                            $badgeClass = 'badge-warning';
                                                            $txtStatus = 'Menunggu Persetujuan';
                                                        } else {
                                                            // Tampilkan icon menggunakan sintaks Blade agar HTML tidak di-escape
                                                            $icon = '<i class="fa fa-check-circle"></i>';
                                                            $badgeClass = 'badge-success';
                                                            $txtStatus = 'Telah Disetujui';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} d-inline-flex align-items-center"
                                                        style="font-size:1em;">
                                                        {!! $icon !!} <span>{{ $txtStatus }}</span>
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Tidak ada data / tidak membutuhkan tanda tangan pada
                                        HTA/GPA.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- FUI --}}
                    <div class="col-md-6 mb-1">
                        <div class="card">
                            <div class="card-header"><strong><i class="fa fa-file-signature mr-1 text-primary"></i>
                                    FUI</strong></div>
                            <div class="card-body">
                                @php $list = $approval['Fui'] ?? collect(); @endphp
                                @if ($list->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($list as $item)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row align-items-md-center">
                                                <div class="mb-2 mb-md-0">
                                                    <strong>{{ $item->Nama ?? '-' }}</strong><br>
                                                    <small>Email: {{ $item->Email ?? '-' }}</small><br>
                                                    <small>
                                                        Tanggal Approve:
                                                        {{ !empty($item->TanggalApprove) ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                                    </small>
                                                </div>
                                                <div>
                                                    @php
                                                        $icon = '';
                                                        $badgeClass = '';
                                                        $txtStatus = '';
                                                        if ($item->Status == 'Pending') {
                                                            $icon = '<i class="fa fa-clock"></i>';
                                                            $badgeClass = 'badge-warning';
                                                            $txtStatus = 'Menunggu Persetujuan';
                                                        } else {
                                                            $icon = '<i class="fa fa-check-circle"></i>';
                                                            $badgeClass = 'badge-success';
                                                            $txtStatus = 'Telah Disetujui';
                                                        }
                                                    @endphp
                                                    <span
                                                        class="badge {{ $badgeClass }} d-inline-flex align-items-center"
                                                        style="font-size:1em;">
                                                        {!! $icon !!} <span>{{ $txtStatus }}</span>
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Tidak ada data / tidak membutuhkan tanda tangan pada
                                        FUI.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Disposisi --}}
                    <div class="col-md-6 mb-1">
                        <div class="card">
                            <div class="card-header"><strong><i class="fa fa-file-signature mr-1 text-primary"></i>
                                    Disposisi</strong></div>
                            <div class="card-body">
                                @php $list = $approval['Disposisi'] ?? collect(); @endphp
                                @if ($list->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($list as $item)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row align-items-md-center">
                                                <div class="mb-2 mb-md-0">
                                                    <strong>{{ $item->Nama ?? '-' }}</strong><br>
                                                    <small>Email: {{ $item->Email ?? '-' }}</small><br>
                                                    <small>
                                                        Tanggal Approve:
                                                        {{ !empty($item->TanggalApprove) ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                                    </small>
                                                </div>
                                                <div>
                                                    @php
                                                        $icon = '';
                                                        $badgeClass = '';
                                                        $txtStatus = '';
                                                        if ($item->Status == 'Pending') {
                                                            $icon = '<i class="fa fa-clock"></i>';
                                                            $badgeClass = 'badge-warning';
                                                            $txtStatus = 'Menunggu Persetujuan';
                                                        } else {
                                                            $icon = '<i class="fa fa-check-circle"></i>';
                                                            $badgeClass = 'badge-success';
                                                            $txtStatus = 'Telah Disetujui';
                                                        }
                                                    @endphp
                                                    <span
                                                        class="badge {{ $badgeClass }} d-inline-flex align-items-center"
                                                        style="font-size:1em;">
                                                        {!! $icon !!} <span>{{ $txtStatus }}</span>
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Tidak ada data / tidak membutuhkan tanda tangan pada
                                        Disposisi.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- FS --}}
                    <div class="col-md-6 mb-1">
                        <div class="card">
                            <div class="card-header"><strong><i class="fa fa-file-signature mr-1 text-primary"></i>
                                    Feasibility Study (FS)</strong></div>
                            <div class="card-body">
                                @php $list = $approval['Fs'] ?? collect(); @endphp
                                @if ($list->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($list as $item)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row align-items-md-center">
                                                <div class="mb-2 mb-md-0">
                                                    <strong>{{ $item->Nama ?? '-' }}</strong><br>
                                                    <small>Email: {{ $item->Email ?? '-' }}</small><br>
                                                    <small>
                                                        Tanggal Approve:
                                                        {{ !empty($item->TanggalApprove) ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                                    </small>
                                                </div>
                                                <div>
                                                    @php
                                                        $icon = '';
                                                        $badgeClass = '';
                                                        $txtStatus = '';
                                                        if ($item->Status == 'Pending') {
                                                            $icon = '<i class="fa fa-clock"></i>';
                                                            $badgeClass = 'badge-warning';
                                                            $txtStatus = 'Menunggu Persetujuan';
                                                        } else {
                                                            $icon = '<i class="fa fa-check-circle"></i>';
                                                            $badgeClass = 'badge-success';
                                                            $txtStatus = 'Telah Disetujui';
                                                        }
                                                    @endphp
                                                    <span
                                                        class="badge {{ $badgeClass }} d-inline-flex align-items-center"
                                                        style="font-size:1em;">
                                                        {!! $icon !!} <span>{{ $txtStatus }}</span>
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Tidak ada data / tidak membutuhkan tanda tangan pada
                                        Feasibility Study (FS).</span>
                                @endif
                            </div>
                        </div>
                    </div>


                </div>
                <div class="mb-3 d-flex justify-content-end">
                    <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>


            </div>

        </div>
    </form>
@endsection

@push('css')
    <style>
        .swal2-popup {
            font-size: 0.95rem !important;
        }

        .swal2-title {
            font-weight: 600 !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            padding: 0.5rem 1.5rem !important;
        }
    </style>
@endpush

@push('js')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSendNotification = document.getElementById('btnSendNotification');
            const form = document.getElementById('formKirimEmail');

            // ✅ Handle Klik "Kirim Notifikasi"
            if (btnSendNotification) {
                btnSendNotification.addEventListener('click', function() {
                    Swal.fire({
                        title: '<i class="fa fa-bell text-primary"></i> Kirim Notifikasi Email?',
                        html: `
                            <p class="mb-2">Email notifikasi akan dikirim ke <strong>Direktur Group</strong> untuk dokumen dengan status <span class="text-warning"><strong>Pending</strong></span>.</p>
                            <small class="text-muted">Pastikan data sudah lengkap sebelum melanjutkan.</small>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-check"></i> Ya, Kirim Email',
                        cancelButtonText: '<i class="fa fa-times"></i> Batal',
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendApprovalEmail();
                        }
                    });
                });
            }

            // ✅ Fungsi AJAX Kirim Email
            function sendApprovalEmail() {
                // 1. Tampilkan Loading SweetAlert
                Swal.fire({
                    title: 'Mengirim...',
                    html: 'Sedang memproses pengiriman email notifikasi ke Direktur Group',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // 2. Disable button untuk mencegah double click
                btnSendNotification.disabled = true;
                const originalBtnText = btnSendNotification.innerHTML;
                btnSendNotification.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';

                // 3. Siapkan data payload (CSRF Token wajib untuk Laravel)
                const formData = new FormData(form);

                // 4. AJAX Request via Fetch
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ??
                                document.querySelector('input[name="_token"]')?.value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // 5. Handle Response JSON dari Controller
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '✅ Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#0d6efd',
                                customClass: {
                                    confirmButton: 'px-4'
                                }
                            }).then(() => {
                                // ✅ Refresh halaman agar data approval terbaru muncul
                                location.reload();
                            });
                        } else {
                            // Handle error dari backend (status: 'error')
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Gagal!',
                                text: data.message || 'Terjadi kesalahan pada server.',
                                confirmButtonColor: '#0d6efd',
                                customClass: {
                                    confirmButton: 'px-4'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Handle network error / server down (500, 404, dll)
                        Swal.fire({
                            icon: 'error',
                            title: '⚠️ Koneksi Gagal',
                            text: 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
                            confirmButtonColor: '#0d6efd',
                            customClass: {
                                confirmButton: 'px-4'
                            }
                        });
                    })
                    .finally(() => {
                        // 6. Kembalikan tombol ke state awal (jika tidak reload)
                        btnSendNotification.disabled = false;
                        btnSendNotification.innerHTML = originalBtnText;
                    });
            }

            // ✅ Reset button jika user navigate back via browser cache
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    btnSendNotification.disabled = false;
                    btnSendNotification.innerHTML = '<i class="fa fa-bell"></i> Kirim Notifikasi';
                }
            });
        });
    </script>
    <script>
        function copyApprovalLink() {
            var tempInput = document.createElement("input");
            var url = "{{ route('ajukan.approval-direktur-group', encrypt($data->id)) }}";
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // for mobile devices
            try {
                document.execCommand("copy");
                Swal.fire({
                    icon: 'success',
                    title: 'Link Berhasil Disalin!',
                    html: 'Link approval berhasil disalin:<br><strong style="word-break: break-all;">' + url +
                        '</strong>',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        confirmButton: 'px-4'
                    }
                });
            } catch {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Gagal menyalin link approval.',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        confirmButton: 'px-4'
                    }
                });
            }
            document.body.removeChild(tempInput);
        }
    </script>
@endpush
