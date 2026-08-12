@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Permintaan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pp.index') }}">Permintaan Pembelian</a></li>
                    <li class="breadcrumb-item active">Detail Permintaan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header bg-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Detail Permintaan Pembelian</h4>
                            <p class="card-text mb-0">
                                Berikut detail permintaan pembelian.
                            </p>
                        </div>
                        <div class="text-end">
                            <div>
                                <strong>No. Permintaan:</strong>
                                <span>{{ $data->NomorPermintaan ?? '-' }}</span>
                            </div>
                            <div>
                                <strong>Asal RS:</strong>
                                <span>{{ $data->getPerusahaan->NamaLengkap ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Departemen</strong></label>
                            <input type="text" class="form-control" value="{{ $data->getDepartemen->Nama ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Tanggal</strong></label>
                            <input type="date" class="form-control" value="{{ $data->Tanggal ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Jenis Permintaan</strong></label>
                            <input type="text" class="form-control" value="{{ $data->getJenisPermintaan->Nama ?? '' }}"
                                readonly>
                        </div>
                    </div>
                    <h5 class="mb-3"><strong>Detail Permintaan Pembelian</strong></h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="table-detail-pembelian">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20%">Nama Barang</th>
                                    <th style="width: 15%">Merk</th>
                                    <th style="width: 15%">Tipe</th>
                                    <th style="width: 13%">Jumlah</th>
                                    <th style="width: 12%">Satuan</th>
                                    <th style="width: 20%">Rencana Penempatan</th>
                                    <th style="width: 20%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $details = $data->getDetail ?? [];
                                    $totalJumlah = 0;
                                @endphp
                                @foreach ($details as $detail)
                                    @php
                                        $totalJumlah += (int) $detail->Jumlah;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $detail->getBarang->Nama ?? $detail->NamaBarang }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $detail->getBarang->getMerk->Nama ?? ($detail->getBarang->getMerk->Nama ?? '-') }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $detail->getBarang->Tipe ?? ($detail->getBarang->Tipe ?? '-') }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" value="{{ $detail->Jumlah }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $detail->getSatuan->NamaSatuan ?? $detail->getSatuan->NamaSatuan }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $detail->RencanaPenempatan }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $detail->Keterangan }}"
                                                readonly>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        <strong style="font-size: 1rem;">
                                            Total Jumlah: <span id="total-jumlah-view" style="font-size: 1rem;">
                                                {{ $totalJumlah }}
                                            </span>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan Permintaan Pembelian</strong></h5>
                            <div class="mb-2 text-center">
                                @if (!empty($approval))
                                    <div class="row justify-content-center">
                                        <div class="col text-center" style="font-weight:600;">
                                            Meminta
                                        </div>
                                        <div class="col text-center" style="font-weight:600;">
                                            Disetujui Oleh
                                        </div>
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
                                        {{-- <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center align-bottom">

                                                </td>
                                            @endforeach
                                        </tr> --}}
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="height:90px; vertical-align: top;">
                                                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                                            style="width:90px; height:90px;"><br>
                                                        <small><em>
                                                                {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                                            </em></small>
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
                                                    <small>{{ $item->Status ?? '-' }}</small>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('pp.index') }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>

                            @foreach ($approval as $item)
                                <form id="formApprove{{ $item->id }}"
                                    action="{{ route('pp.approve', $item->ApprovalToken) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <input type="hidden" name="UserId" value="{{ $item->UserId }}">
                                    <input type="hidden" name="DokumenId" value="{{ $item->DokumenId }}">
                                    <input type="hidden" name="JenisForm" value="{{ $item->JenisFormId }}">
                                    @if (auth()->id() == ($item->UserId ?? null) && $item->Status != 'Approved' && !empty($item->ApprovalToken))
                                        <button type="button" class="btn btn-primary me-2 swal-confirm-btn"
                                            data-title="Konfirmasi"
                                            data-text="Apakah Anda yakin ingin menyetujui sebagai {{ $item->getJabatan->Nama ?? $item->JenisUser }}?"
                                            data-form="formApprove{{ $item->id }}">
                                            <i class="fa fa-check"></i>
                                            Setujui
                                        </button>
                                    @endif
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('js')
        @if (Session::get('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#4BCC1F',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#4BCC1F',
                });
            </script>
        @endif
        @if (Session::get('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#d33',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#d33',
                });
            </script>
        @endif
        <script>
            document.querySelectorAll('.swal-confirm-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let formId = btn.getAttribute('data-form');
                    let form = document.getElementById(formId);
                    let title = btn.getAttribute('data-title') ?? 'Konfirmasi';
                    let text = btn.getAttribute('data-text') ?? 'Apakah Anda yakin?';
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Proses...',
                                html: '<div style="margin:20px 0"><i class="fa fa-spinner fa-spin fa-2x"></i></div>Memproses persetujuan...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                allowEscapeKey: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            form.submit();
                        }
                    });
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                if ($.fn.select2) {
                    $('.user-penilai-select').each(function() {
                        if ($(this).hasClass("select2-hidden-accessible")) {
                            $(this).select2('destroy');
                        }
                    });
                    $('.user-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih User",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                } else {
                    console.warn("Plugin select2 belum dimuat.");
                }

                let isSubmitting = false;

                function disableAllInteractions() {
                    isSubmitting = true;
                    $(document).on('contextmenu.loading', function(e) {
                        e.preventDefault();
                        return false;
                    });
                    $(document).on('keydown.loading', function(e) {
                        e.preventDefault();
                        return false;
                    });
                    $(document).on('mousewheel.loading DOMMouseScroll.loading', function(e) {
                        e.preventDefault();
                        return false;
                    });
                    $('body').css({
                        'user-select': 'none',
                        '-webkit-user-select': 'none',
                        '-moz-user-select': 'none',
                        '-ms-user-select': 'none'
                    });
                    if ($('#loading-overlay').length === 0) {
                        $('body').append(
                            '<div id="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;cursor:not-allowed;background:rgba(0,0,0,0.3);"></div>'
                        );
                    }
                }

                function enableAllInteractions() {
                    isSubmitting = false;
                    $(document).off('.loading');
                    $('body').css({
                        'user-select': '',
                        '-webkit-user-select': '',
                        '-moz-user-select': '',
                        '-ms-user-select': ''
                    });
                    $('#loading-overlay').remove();
                }

                // User: set email, jabatanid, departemenid when user changed
                $('.user-penilai-select').on('change', function() {
                    var $select = $(this);
                    var rowIndex = $select.data('row-index');
                    if (rowIndex == 0) return;
                    var selected = $select.find('option:selected');
                    var email = selected.data('email') || '';
                    var jabatanid = selected.data('jabatanid') || '';
                    var departemenid = selected.data('departemenid') || '';

                    $('input.email-penilai-input[data-row-index="' + rowIndex + '"]').val(email);
                    $('select.jabatan-penilai-select[data-row-index="' + rowIndex + '"]').val(jabatanid)
                        .trigger('change');
                    $('select.departemen-penilai-select[data-row-index="' + rowIndex + '"]').val(departemenid)
                        .trigger('change');
                });

                // On modal load, set email/jabatanid/departemenid if User selected
                $('.user-penilai-select').each(function() {
                    var $select = $(this);
                    var rowIndex = $select.data('row-index');
                    if (rowIndex == 0) return;
                    var selected = $select.find('option:selected');
                    if (selected.length && selected.val() !== '') {
                        var email = selected.data('email') || '';
                        var jabatanid = selected.data('jabatanid') || '';
                        var departemenid = selected.data('departemenid') || '';
                        $('input.email-penilai-input[data-row-index="' + rowIndex + '"]').val(email);
                        $('select.jabatan-penilai-select[data-row-index="' + rowIndex + '"]').val(jabatanid);
                        $('select.departemen-penilai-select[data-row-index="' + rowIndex + '"]').val(
                            departemenid);
                    }
                });

                // -- PATCH: loading process not shown --
                // Pastikan penamaan id formPenilai benar-benar sama dengan form pada html
                // Pastikan tidak ada duplicate id formPenilai pada halaman

                // Bro, ini bagian submit: tampilkan sweetalert loading (proses ajax) saat submit
                $('#formPenilai').on('submit', function(e) {
                    e.preventDefault();
                    if (isSubmitting) return;

                    const form = $(this);

                    Swal.fire({
                        title: 'Konfirmasi Ajukan?',
                        text: 'Apakah Anda yakin data penilai sudah benar dan ingin mengirim email?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, ajukan!',
                        cancelButtonText: 'Batal',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        // Aktifkan disable interactions
                        disableAllInteractions();

                        // Tampilkan SweetAlert loading
                        let seconds = 0;
                        let timerInterval = null;
                        Swal.fire({
                            title: 'Mengirim Email...',
                            html: `
                            <div style="margin:20px 0">
                                <i class="fa fa-envelope fa-3x" style="color:#3085d6"></i>
                            </div>
                            Proses pengiriman email sedang berlangsung.<br>
                            <strong style="color:#d33">JANGAN tutup atau refresh halaman ini!</strong><br>
                            <small>Waktu tunggu: <b id="timer-counter">0</b> detik</small>
                        `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                timerInterval = setInterval(() => {
                                    seconds++;
                                    const counterEl = document.getElementById(
                                        'timer-counter');
                                    if (counterEl) {
                                        counterEl.textContent = seconds;
                                    }
                                }, 1000);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        });

                        // Kirim AJAX request, gunakan processData & contentType hanya jika perlu (misal file upload)
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            // tambahkan headers ini jika kamu pakai csrf di meta tag
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                clearInterval(timerInterval);
                                enableAllInteractions();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Email berhasil dikirim ke penilai.',
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                clearInterval(timerInterval);
                                enableAllInteractions();

                                let errorMsg = 'Terjadi kesalahan saat mengirim email.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errorMsg,
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    });
                });

                // Re-initialize select2 after modal open
                $('#modalPenilai').on('shown.bs.modal', function() {
                    $('.user-penilai-select').each(function() {
                        if (!$(this).hasClass("select2-hidden-accessible")) {
                            $(this).select2({
                                dropdownParent: $('#modalPenilai'),
                                width: '100%',
                                placeholder: "Pilih User",
                                allowClear: true,
                                minimumResultsForSearch: 0
                            });
                        }
                    });
                });


                // -- PATCH: Tampilkan loading global untuk semua form dengan class .with-global-loading
                $('form.with-global-loading').on('submit', function() {
                    Swal.fire({
                        title: 'Proses...',
                        html: '<div style="margin:20px 0"><i class="fa fa-spinner fa-spin fa-2x"></i></div>Memproses data...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        allowEscapeKey: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            });
        </script>
    @endpush
