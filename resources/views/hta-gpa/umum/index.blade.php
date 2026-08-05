@extends('layouts.app')

@section('content')
    @push('css')
        <style>
            #scrollToTopBtn,
            #scrollToBottomBtn {
                position: fixed;
                right: 30px;
                width: 48px;
                height: 48px;
                border: none;
                border-radius: 50%;
                background: #4BCC1F;
                color: white;
                font-size: 24px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                cursor: pointer;
                z-index: 999;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: opacity 0.2s;
                opacity: 0.8;
            }

            #scrollToTopBtn:hover,
            #scrollToBottomBtn:hover {
                opacity: 1;
            }

            #scrollToTopBtn {
                bottom: 90px;
                display: none;
            }

            #scrollToBottomBtn {
                bottom: 30px;
                display: none;
            }
        </style>
    @endpush
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Input Penilaian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Hta / Gpa</a></li>
                    <li class="breadcrumb-item active">Isi Penilaian</li>
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
                                </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning d-flex align-items-start" role="alert" style="min-height: 80px; background-color: #fffbe6; border-left: 6px solid #ffcc00; font-size: 1.08rem;">
                        <i class="fa fa-exclamation-triangle me-3" style="align-self: center; font-size: 2rem; color: #ff9900;"></i>
                        <div>
                            <ul class="mb-0 ps-2" style="list-style-type: disc;">
                                <li>Periksa kembali file yang akan diunggah. Pastikan semua data sudah benar sebelum mengirim.</li>
                                <li><strong>Tombol <u>Kirim</u> dan <u>Ajukan</u> hanya akan terlihat jika semua data vendor sudah terisi lengkap dan file sudah diupload.</strong></li>
                            </ul>
                        </div>
                    </div>



                    <form id="formHtaGpa" action="{{ route('htagpa.store-umum') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="vendor[0][IdVendor]" value="{{ $data->getVendor[0]->NamaVendor }}">
                        <input type="hidden" name="vendor[1][IdVendor]" value="{{ $data->getVendor[1]->NamaVendor }}">
                        <input type="hidden" name="vendor[0][IdPengajuan]" value="{{ $data->id }}">
                        <input type="hidden" name="vendor[1][IdPengajuan]" value="{{ $data->id }}">
                        <input type="hidden" name="vendor[0][PengajuanItemId]"
                            value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                        <input type="hidden" name="vendor[1][PengajuanItemId]"
                            value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                        <input type="hidden" name="vendor[0][IdBarang]"
                            value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
                        <input type="hidden" name="vendor[1][IdBarang]"
                            value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
                        <input type="hidden" name="vendor[0][IdVendorDetail]"
                            value="{{ $data->getHtaGpa->getDetailHta[0]['id'] ?? null }}">
                        <input type="hidden" name="vendor[1][IdVendorDetail]"
                            value="{{ $data->getHtaGpa->getDetailHta[1]['id'] ?? null }}">

                        <div class="mb-3">
                            <label for="file_both_vendor" class="form-label">Upload PDF atau Excel (untuk semua
                                Vendor)</label>
                            <div class="dropzone-container" id="dropzone_both_vendor"
                                style="border: 2px dashed #ccc; padding: 30px; border-radius: 8px; text-align: center; cursor: pointer; background: #f9f9f9;">
                                <i class="fa fa-cloud-upload-alt fa-2x mb-2"></i>
                                <div>Drag & Drop file PDF/Excel di sini, atau klik untuk memilih file</div>
                                <input type="file" class="form-control d-none" id="file_both_vendor"
                                    name="file_both_vendor" accept=".pdf,.xls,.xlsx">
                                <div class="dz-filename mt-2 text-primary" style="display:none;"
                                    id="dz_filename_both_vendor"></div>
                                @php
                                    // Ambil file yang sudah diupload jika sudah ada (cek ke vendor 1)
                                    $uploadedFile = null;
                                    $vendor0 = $data->getVendor[0] ?? null;
                                    if (
                                        $vendor0 &&
                                        isset($vendor0->getHtaGpa->File) &&
                                        !empty($vendor0->getHtaGpa->File)
                                    ) {
                                        if (is_array($vendor0->getHtaGpa->File)) {
                                            $uploadedFile = $vendor0->getHtaGpa->File[0] ?? null;
                                        } else {
                                            $uploadedFile = $vendor0->getHtaGpa->File;
                                        }
                                    }
                                @endphp
                                @if ($uploadedFile)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/upload/gpa/' . $uploadedFile) }}"
                                            class="btn btn-link text-success" target="_blank">
                                            <i class="fa fa-eye"></i> Preview File
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <small class="form-text text-muted">Unggah dokumen pendukung dalam format PDF atau Excel. File
                                ini akan digunakan untuk seluruh vendor terkait.</small>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var dzZone = document.getElementById('dropzone_both_vendor');
                                var dzInput = document.getElementById('file_both_vendor');
                                var dzFilename = document.getElementById('dz_filename_both_vendor');

                                dzZone.addEventListener('click', function(e) {
                                    if (e.target === dzZone || e.target.classList.contains('fa-cloud-upload-alt')) {
                                        dzInput.click();
                                    }
                                });

                                dzZone.addEventListener('dragover', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    dzZone.style.background = "#eef8ff";
                                });

                                dzZone.addEventListener('dragleave', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    dzZone.style.background = "#f9f9f9";
                                });

                                dzZone.addEventListener('drop', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    dzZone.style.background = "#f9f9f9";
                                    var files = e.dataTransfer.files;
                                    if (
                                        files.length &&
                                        (files[0].type === "application/pdf" ||
                                            files[0].name.endsWith('.xls') ||
                                            files[0].name.endsWith('.xlsx'))
                                    ) {
                                        dzInput.files = files;
                                        dzFilename.style.display = 'block';
                                        dzFilename.textContent = files[0].name;
                                    } else {
                                        dzFilename.style.display = 'block';
                                        dzFilename.textContent = 'File tidak valid. Hanya PDF atau Excel yang diizinkan.';
                                    }
                                });

                                dzInput.addEventListener('change', function(e) {
                                    if (dzInput.files.length) {
                                        dzFilename.style.display = 'block';
                                        dzFilename.textContent = dzInput.files[0].name;
                                    } else {
                                        dzFilename.style.display = 'none';
                                    }
                                });
                            });
                        </script>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" name="action" value="draft" class="btn btn-warning me-2">
                                <i class="fa fa-save me-1"></i> Simpan Sebagai Draft
                            </button>
                            <!-- Ajukan Button trigger modal -->
                            @php
                                $showAjukan = false;
                                $filledFileCount = 0;
                                foreach ($data->getHtaGpa->getDetailHta ?? [] as $detail) {
                                    if (!empty($detail->File)) {
                                        $filledFileCount++;
                                    }
                                }
                                // Tampilkan tombol Ajukan hanya jika statusnya belum Final
                                if (($data->getHtaGpa->Status ?? null) !== 'Final') {
                                    $showAjukan = true;
                                }

                            @endphp

                            @if ($showAjukan)
                                <button type="button" id="btnAjukan" class="btn btn-success me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalPenilai">
                                    <i class="fa fa-paper-plane me-1"></i> Ajukan & Kirim Email
                                </button>
                            @endif

                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            @php
                $allVendorsFilled = true;
                if (!empty($data->getHtaGpa->getDetailHta)) {
                    foreach ($data->getHtaGpa->getDetailHta as $detail) {
                        if (empty($detail->File)) {
                            $allVendorsFilled = false;
                            break;
                        }
                    }
                } else {
                    $allVendorsFilled = false;
                }
            @endphp



            <form id="formAjukanHtaGpa" action="{{ route('htagpa.ajukan') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="IdPengajuan" value="{{ $data->id }}">
                <input type="hidden" name="PengajuanItemId" value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                <input type="hidden" name="IdBarang" value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
                <input type="hidden" name="Status" value="Diajukan">
            </form>
            @include('hta-gpa.modal-kirim-email')
        </div>
    </div>
    <!-- Floating Scroll Button -->

    <button id="scrollToTopBtn" title="Scroll to Top">
        <i class="fa fa-arrow-up"></i>
    </button>
    <button id="scrollToBottomBtn" title="Scroll to Bottom">
        <i class="fa fa-arrow-down"></i>
    </button>
@endsection
@push('js')
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#4BCC1F',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#4BCC1F',
                });
            }, 500);
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topBtn = document.getElementById('scrollToTopBtn');
            const bottomBtn = document.getElementById('scrollToBottomBtn');

            function toggleButtons() {
                if (window.scrollY > 150) {
                    topBtn.style.display = 'flex';
                } else {
                    topBtn.style.display = 'none';
                }
                if (window.innerHeight + window.scrollY < document.body.offsetHeight - 150) {
                    bottomBtn.style.display = 'flex';
                } else {
                    bottomBtn.style.display = 'none';
                }
            }

            window.addEventListener('scroll', toggleButtons);
            window.addEventListener('resize', toggleButtons);

            topBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            bottomBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
            });

            // Initial check
            toggleButtons();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnKonfirmasiKirimUlang');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Kirim Ulang Notifikasi?',
                        text: 'Anda yakin ingin mengirim ulang notifikasi approval yang masih pending?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Mengirim Notifikasi...',
                                html: `<b>Mohon tunggu, proses pengiriman email sedang berlangsung.</b>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal
                                        .showLoading();
                                    setTimeout(() => {
                                        window.location.href = btn.getAttribute(
                                            'href');
                                    }, 500);
                                }
                            });
                        }
                    });
                });
            }
        });
    </script>
@endpush
