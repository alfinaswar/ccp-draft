@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Lembar Disposisi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lembar-disposisi.index') }}">Lembar Disposisi</a></li>
                    <li class="breadcrumb-item active">Tambah Lembar Disposisi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Lembar Disposisi</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('lembar-disposisi.store') }}" method="POST" id="form-lembar-disposisi">
                        @csrf
                        <input type="hidden" name="IdPengajuan" value="{{ $idPengajuan }}">
                        <input type="hidden" name="PengajuanItemId" value="{{ $idPengajuanItem }}">

                        {{-- Contoh input Nama Barang, Harga, Vendor, Tujuan --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="NamaBarang" class="form-label"><strong>NAMA BARANG / JASA YANG AKAN
                                            DIBELI</strong></label>
                                    <select name="NamaBarang" id="NamaBarang"
                                        class="form-select select2 @error('NamaBarang') is-invalid @enderror"
                                        style="width:100%;">
                                        <option value="{{ $data->getBarang->id ?? '' }}" selected>
                                            {{ $data->getBarang->Nama ?? '-' }}</option>
                                    </select>
                                    @error('NamaBarang')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="Harga" class="form-label"><strong>HARGA</strong></label>
                                    <input type="text" name="Harga" id="Harga"
                                        class="form-control @error('Harga') is-invalid @enderror"
                                        value="{{ old('Harga', $data->getRekomendasi->getRekomedasiDetail[0]->HargaNego ?? 0) }}"
                                        placeholder="contoh: 1.000.000" readonly>
                                    <small class="form-text text-muted">Harga yang telah dinegosiasi</small>
                                    @error('Harga')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="RencanaVendor" class="form-label"><strong>RENCANA VENDOR</strong></label>
                                    <select name="RencanaVendor" id="RencanaVendor"
                                        class="form-select select2 @error('RencanaVendor') is-invalid @enderror"
                                        style="width:100%;">
                                        @if (isset($data->getRekomendasi->getRekomedasiDetail[0]->getNamaVendor))
                                            <option
                                                value="{{ $data->getRekomendasi->getRekomedasiDetail[0]->getNamaVendor->id }}"
                                                selected>
                                                {{ $data->getRekomendasi->getRekomedasiDetail[0]->getNamaVendor->Nama }}
                                            </option>
                                        @elseif(old('RencanaVendor'))
                                            <option value="{{ old('RencanaVendor') }}" selected>{{ old('RencanaVendor') }}
                                            </option>
                                        @else
                                            <option value="" selected>Pilih vendor</option>
                                        @endif
                                    </select>
                                    @error('RencanaVendor')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="TujuanPenempatan" class="form-label"><strong>TUJUAN
                                            PENGGUNAAN/RUANGAN</strong></label>
                                    <input type="text" name="TujuanPenempatan" id="TujuanPenempatan"
                                        class="form-control @error('TujuanPenempatan') is-invalid @enderror"
                                        value="{{ old('TujuanPenempatan', $data->getPengajuanPembelian->getPermintaan->getDetail[0]->RencanaPenempatan ?? 'Tidak Diisi Dipermintaan') }}"
                                        placeholder="Masukkan tujuan penggunaan atau ruangan">
                                    @error('TujuanPenempatan')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block"><strong>FORM PERMINTAAN DARI USER</strong></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="FormPermintaan"
                                                id="FormPermintaan_ada" value="Y"
                                                {{ old('FormPermintaan', 'N') == 'Y' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="FormPermintaan_ada">Ya, Ada</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="FormPermintaan"
                                                id="FormPermintaan_tidak" value="N"
                                                {{ old('FormPermintaan', 'N') == 'N' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="FormPermintaan_tidak">Tidak ada</label>
                                        </div>
                                    </div>
                                    @error('FormPermintaan')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Approver --}}
                        {{-- <div class="mb-4">
                            <div class="alert alert-info mb-3" role="alert">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>Informasi:</strong> Harap masukkan <b>email</b> dengan benar karena notifikasi akan
                                dikirim.
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle" style="width:100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:5%;">No</th>
                                            <th style="width:25%;">Nama</th>
                                            <th style="width:25%;">Jabatan</th>
                                            <th style="width:25%;">Departemen</th>
                                            <th style="width:20%;">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($ttd->getApproval) && $ttd->getApproval->count() > 0)
                                            @foreach ($ttd->getApproval as $i => $approval)
                                                <tr>
                                                    <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                    <td>
                                                        <select name="IdUser[]" id="IdUser_{{ $i + 1 }}"
                                                            class="form-control select2 iduser-select"
                                                            data-row="{{ $i + 1 }}" data-placeholder="Pilih Nama">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach ($user as $u)
                                                                <option value="{{ $u->id }}"
                                                                    data-email="{{ $u->email ?? '' }}"
                                                                    data-jabatan="{{ $u->jabatan ?? '' }}"
                                                                    data-departemen="{{ $u->departemen ?? '' }}"
                                                                    {{ old('IdUser.' . $i, $approval->UserId) == $u->id ? 'selected' : '' }}>
                                                                    {{ $u->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('IdUser.' . $i)
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select name="Jabatan[]" id="Jabatan_{{ $i + 1 }}"
                                                            class="form-control select2 jabatan-select"
                                                            data-placeholder="Pilih Jabatan">
                                                            <option value="">Pilih Jabatan</option>
                                                            @foreach ($jabatan as $j)
                                                                <option value="{{ $j->id }}"
                                                                    {{ old('Jabatan.' . $i, $approval->JabatanId) == $j->id ? 'selected' : '' }}>
                                                                    {{ $j->Nama }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('Jabatan.' . $i)
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select name="Departemen[]" id="Departemen_{{ $i + 1 }}"
                                                            class="form-control select2 departemen-select"
                                                            data-placeholder="Pilih Departemen">
                                                            <option value="">Pilih Departemen</option>
                                                            @foreach ($departemen as $d)
                                                                <option value="{{ $d->id }}"
                                                                    {{ old('Departemen.' . $i, $approval->DepartemenId) == $d->Nama ? 'selected' : '' }}>
                                                                    {{ $d->Nama }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('Departemen.' . $i)
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="email" name="Email[]"
                                                            id="Email_{{ $i + 1 }}"
                                                            class="form-control email-input"
                                                            value="{{ old('Email.' . $i, $approval->getUser->email ?? ($approval->Email ?? '')) }}"
                                                            placeholder="Masukkan Email" required>
                                                        @error('Email.' . $i)
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i>
                                Simpan</button>
                            <a href="{{ route('ajukan.show', encrypt($idPengajuan)) }}" class="btn btn-secondary"><i
                                    class="fa fa-arrow-left me-1"></i> Kembali</a>
                        </div> --}}
                    </form>
                </div>
            </div>
            @if (!empty($approval))
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Status Approval</h5>
                            {{-- @if (!empty($approval) && count($approval) > 0)
                                <a href="{{ route('lembar-disposisi.kirim-ulang-notifikasi', $dispo->id) }}"
                                    class="btn btn-primary" id="btnKonfirmasiKirimUlang">
                                    <i class="fa fa-paper-plane me-1"></i> Kirim Ulang Notifikasi
                                </a>
                            @endif --}}
                        </div>
                        <div class="card-body">
                            {{-- <span>
                                Gunakan fitur <strong>Kirim Ulang Notifikasi</strong> untuk mengirim ulang email
                                approval ke penilai yang statusnya masih <strong>Pending</strong>, tanpa membatalkan
                                status approval yang sudah ada sebelumnya.
                            </span> --}}
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Urutan</th>
                                            <th>Status</th>
                                            <th>Status Email</th>
                                            <th>TanggalApprove</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($approval && count($approval) > 0)
                                            @foreach ($approval as $key => $item)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $item->Nama }}</td>
                                                    <td>{{ $item->Email }}</td>
                                                    <td>{{ $item->Urutan }}</td>
                                                    <td>
                                                        <span
                                                            class="badge
                                            @if ($item->Status == 'Approved') bg-success
                                            @elseif($item->Status == 'Pending') bg-warning text-dark
                                            @elseif($item->Status == 'Rejected') bg-danger
                                            @else bg-secondary @endif">
                                                            {{ $item->Status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $item->StatusEmail }}</td>
                                                    <td>
                                                        {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($item->UserId == 81)
                                                            <span class="text-muted">Notifikasi Akan dikirim kan oleh ccp
                                                                setelah presentasi</span>
                                                        @else
                                                            @php
                                                                $approvalUrl = route(
                                                                    'lembar-disposisi.approve',
                                                                    $item->ApprovalToken ?? '',
                                                                );
                                                            @endphp
                                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                                onclick="navigator.clipboard.writeText('{{ $approvalUrl }}'); Swal.fire('Disalin!','Link approval telah disalin ke clipboard!','success')">
                                                                <i class="fa fa-copy"></i> Salin Link Approval
                                                            </button>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="17" class="text-center">Belum ada data approval.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            // Variable untuk tracking loading state
            let isSubmitting = false;

            // Inisialisasi select2
            $('.select2').select2({
                width: '100%'
            });

            // Fungsi reusable set user details
            function setUserDetails(row, email, jabatan, departemen) {
                $('#Email_' + row).val(email);
                $('#Jabatan_' + row).val(jabatan).trigger('change');
                $('#Departemen_' + row).val(departemen).trigger('change');
            }

            // Event change untuk IdUser
            $('.iduser-select').on('change', function() {
                let row = $(this).data('row');
                let selected = $(this).find('option:selected');
                let email = selected.data('email') || '';
                let jabatan = selected.data('jabatan') || '';
                let departemen = selected.data('departemen') || '';
                setUserDetails(row, email, jabatan, departemen);
            });

            // Trigger otomatis untuk row yang sudah ada value saat load
            $('.iduser-select').each(function() {
                if ($(this).val()) $(this).trigger('change');
            });

            // Fungsi untuk disable semua interaksi
            function disableAllInteractions() {
                isSubmitting = true;

                // Disable klik kanan
                $(document).on('contextmenu.loading', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Disable semua keyboard shortcuts
                $(document).on('keydown.loading', function(e) {
                    // Block F5 (refresh)
                    if (e.keyCode === 116) {
                        e.preventDefault();
                        return false;
                    }
                    // Block Ctrl+R (refresh)
                    if ((e.ctrlKey || e.metaKey) && e.keyCode === 82) {
                        e.preventDefault();
                        return false;
                    }
                    // Block Ctrl+W (close tab)
                    if ((e.ctrlKey || e.metaKey) && e.keyCode === 87) {
                        e.preventDefault();
                        return false;
                    }
                    // Block Ctrl+F4 (close tab)
                    if (e.ctrlKey && e.keyCode === 115) {
                        e.preventDefault();
                        return false;
                    }
                    // Block Alt+F4 (close window)
                    if (e.altKey && e.keyCode === 115) {
                        e.preventDefault();
                        return false;
                    }
                    // Block ESC
                    if (e.keyCode === 27) {
                        e.preventDefault();
                        return false;
                    }
                    // Block semua keyboard input lainnya
                    e.preventDefault();
                    return false;
                });

                // Disable mouse wheel
                $(document).on('mousewheel.loading DOMMouseScroll.loading', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Disable text selection
                $('body').css({
                    'user-select': 'none',
                    '-webkit-user-select': 'none',
                    '-moz-user-select': 'none',
                    '-ms-user-select': 'none'
                });

                // Add overlay untuk block semua klik
                if ($('#loading-overlay').length === 0) {
                    $('body').append(
                        '<div id="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;cursor:not-allowed;background:rgba(0,0,0,0.3);"></div>'
                    );
                }
            }

            // Handle form submit dengan loading
            $('#form-lembar-disposisi').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Jika sudah submitting, return
                if (isSubmitting) {
                    return false;
                }

                var form = this;

                // Validasi sederhana - cek apakah minimal ada 1 user yang dipilih
                let hasUser = false;
                $('select[name="IdUser[]"]').each(function() {
                    if ($(this).val()) {
                        hasUser = true;
                        return false; // break loop
                    }
                });

                if (!hasUser) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Mohon pilih minimal 1 user untuk disposisi.',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Konfirmasi Simpan?',
                    text: 'Apakah Anda yakin data lembar disposisi sudah benar dan akan mengirim notifikasi ke email terkait?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aktifkan semua proteksi
                        disableAllInteractions();

                        // Tampilkan loading yang tidak bisa ditutup
                        Swal.fire({
                            title: 'Menyimpan Data...',
                            html: '<div style="margin: 20px 0;"><i class="fa fa-paper-plane fa-3x" style="color: #3085d6;"></i></div>' +
                                'Mohon tunggu, sedang menyimpan data dan mengirim notifikasi email.<br>' +
                                '<strong style="color: #d33; margin-top: 15px; display: block;">JANGAN tutup atau refresh halaman ini!</strong><br>' +
                                '<small>Waktu tunggu: <b id="timer-counter">0</b> detik</small>',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            showConfirmButton: false,
                            showCancelButton: false,
                            didOpen: () => {
                                Swal.showLoading();

                                // Timer untuk menampilkan waktu tunggu
                                let seconds = 0;
                                const timerInterval = setInterval(() => {
                                    seconds++;
                                    const counterEl = document.getElementById(
                                        'timer-counter');
                                    if (counterEl) {
                                        counterEl.textContent = seconds;
                                    }
                                }, 1000);

                                // Simpan interval
                                Swal.getPopup().timerInterval = timerInterval;
                            },
                            willClose: () => {
                                if (Swal.getPopup().timerInterval) {
                                    clearInterval(Swal.getPopup().timerInterval);
                                }
                            }
                        });

                        // Submit form setelah delay kecil untuk memastikan UI update
                        setTimeout(function() {
                            form.submit();
                        }, 200);
                    }
                });

                return false;
            });
        });
    </script>
@endpush
