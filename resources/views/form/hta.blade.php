@extends('layouts.app')

@section('content')
    <div class="col-xxl-12 col-xl-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Analisa HTA - Kriteria Penilaian
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-header p-2 bg-light">
                                    <strong>Barang {{ $i }}</strong>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="mb-3"><i class="feather-box me-1"></i> Informasi Barang</h6>
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th style="width:120px;">Nama Barang</th>
                                            <td>: <span class="fw-semibold">
                                                    @if ($i == 1)
                                                        Infusion Pump XYZ
                                                    @elseif($i == 2)
                                                        Infusion Pump ABC
                                                    @else
                                                        Infusion Pump DEF
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Merk</th>
                                            <td>: <span class="fw-semibold">
                                                    @if ($i == 1)
                                                        Meditech 1000
                                                    @elseif($i == 2)
                                                        HealthPro 500
                                                    @else
                                                        Lifetech X
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Vendor</th>
                                            <td>: <span class="fw-semibold">
                                                    @if ($i == 1)
                                                        PT Alpha Medika
                                                    @elseif($i == 2)
                                                        PT Beta Medis
                                                    @else
                                                        PT Gamma Health
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <ul class="nav nav-tabs tab-style-1 nav-justified mb-3 d-sm-flex d-block" id="htaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="harga-tab" data-bs-toggle="tab" data-bs-target="#harga"
                            type="button" role="tab" aria-controls="harga" aria-selected="true">
                            <i class="feather-dollar-sign me-1 align-middle"></i>Harga
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="garansi-tab" data-bs-toggle="tab" data-bs-target="#garansi"
                            type="button" role="tab" aria-controls="garansi" aria-selected="false">
                            <i class="feather-shield me-1 align-middle"></i>Garansi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="spesifikasi-tab" data-bs-toggle="tab" data-bs-target="#spesifikasi"
                            type="button" role="tab" aria-controls="spesifikasi" aria-selected="false">
                            <i class="feather-file-text me-1 align-middle"></i>Spesifikasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="populasi-tab" data-bs-toggle="tab" data-bs-target="#populasi"
                            type="button" role="tab" aria-controls="populasi" aria-selected="false">
                            <i class="feather-users me-1 align-middle"></i>Populasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="keamanan-pasien-tab" data-bs-toggle="tab"
                            data-bs-target="#keamanan-pasien" type="button" role="tab" aria-controls="keamanan-pasien"
                            aria-selected="false">
                            <i class="feather-user-check me-1 align-middle"></i>Keamanan Pasien
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="keamanan-petugas-tab" data-bs-toggle="tab"
                            data-bs-target="#keamanan-petugas" type="button" role="tab"
                            aria-controls="keamanan-petugas" aria-selected="false">
                            <i class="feather-user-shield me-1 align-middle"></i>Keamanan Petugas
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mudah-digunakan-tab" data-bs-toggle="tab"
                            data-bs-target="#mudah-digunakan" type="button" role="tab" aria-controls="mudah-digunakan"
                            aria-selected="false">
                            <i class="feather-thumbs-up me-1 align-middle"></i>Mudah Digunakan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="insiden-tab" data-bs-toggle="tab" data-bs-target="#insiden"
                            type="button" role="tab" aria-controls="insiden" aria-selected="false">
                            <i class="feather-alert-triangle me-1 align-middle"></i>Laporan Insiden Re/Under Call
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rekomendasi-tab" data-bs-toggle="tab" data-bs-target="#rekomendasi"
                            type="button" role="tab" aria-controls="rekomendasi" aria-selected="false">
                            <i class="feather-star me-1 align-middle"></i>Rekomendasi Klinis
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="perawatan-tab" data-bs-toggle="tab" data-bs-target="#perawatan"
                            type="button" role="tab" aria-controls="perawatan" aria-selected="false">
                            <i class="feather-settings me-1 align-middle"></i>Perawatan Pasca Beli
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="htaTabContent">
                    <div class="tab-pane fade show active text-muted" id="harga" role="tabpanel"
                        aria-labelledby="harga-tab" tabindex="0">
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-sm align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width:70%;">Perbandingan</th>
                                        <th style="width:10%;">Vendor 1</th>
                                        <th style="width:10%;">Vendor 2</th>
                                        <th style="width:10%;">Vendor 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="parameter_1" value="HNA+PPN"
                                                class="form-control form-control-sm" placeholder="Parameter" readonly>
                                        </td>
                                        <td>
                                            <input type="text" min="0" step="any" name="HNA_PPN_vendor1"
                                                id="HNA_PPN_vendor1"
                                                class="form-control form-control-sm auto-calc rupiah-input">
                                        </td>
                                        <td>
                                            <input type="text" min="0" step="any" name="HNA_PPN_vendor2"
                                                id="HNA_PPN_vendor2"
                                                class="form-control form-control-sm auto-calc rupiah-input">
                                        </td>
                                        <td>
                                            <input type="text" min="0" step="any" name="HNA_PPN_vendor3"
                                                id="HNA_PPN_vendor3"
                                                class="form-control form-control-sm auto-calc rupiah-input">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="parameter_2"
                                                value="DISC% (Special disc Awal Bros)"
                                                class="form-control form-control-sm" placeholder="Parameter" readonly>
                                        </td>
                                        <td>
                                            <input type="number" min="0" max="100" step="any"
                                                name="Disc_vendor1" id="Disc_vendor1"
                                                class="form-control form-control-sm auto-calc">
                                        </td>
                                        <td>
                                            <input type="number" min="0" max="100" step="any"
                                                name="Disc_vendor2" id="Disc_vendor2"
                                                class="form-control form-control-sm auto-calc">
                                        </td>
                                        <td>
                                            <input type="number" min="0" max="100" step="any"
                                                name="Disc_vendor3" id="Disc_vendor3"
                                                class="form-control form-control-sm auto-calc">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="parameter_3" value="GRAND TOTAL + PPN"
                                                class="form-control form-control-sm" placeholder="Parameter" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="GrandTotal_vendor1" id="GrandTotal_vendor1"
                                                class="form-control form-control-sm rupiah-output" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="GrandTotal_vendor2" id="GrandTotal_vendor2"
                                                class="form-control form-control-sm rupiah-output" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="GrandTotal_vendor3" id="GrandTotal_vendor3"
                                                class="form-control form-control-sm rupiah-output" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="parameter_4" value="DP"
                                                class="form-control form-control-sm" placeholder="Parameter" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="DP_vendor1"
                                                class="form-control form-control-sm rupiah-input">
                                        </td>
                                        <td>
                                            <input type="text" name="DP_vendor2"
                                                class="form-control form-control-sm rupiah-input">
                                        </td>
                                        <td>
                                            <input type="text" name="DP_vendor3"
                                                class="form-control form-control-sm rupiah-input">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tabel Skema Pembayaran Bertahap memanjang ke samping untuk 3 Vendor -->
                        <div class="table-responsive mb-5">
                            <table class="table table-hover table-sm align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width:35%;">Tahap Pembayaran</th>
                                        <th style="width:20%;">Vendor 1</th>
                                        <th style="width:20%;">Vendor 2</th>
                                        <th style="width:20%;">Vendor 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <tr>
                                            <td>
                                                <input type="text" name="tahap_parameter_{{ $i }}"
                                                    value="TAHAP {{ $i }}" class="form-control form-control-sm"
                                                    placeholder="Tahap Pembayaran">
                                            </td>
                                            <td>
                                                <input type="text" name="Tahap{{ $i }}_vendor1"
                                                    class="form-control form-control-sm rupiah-input">
                                            </td>
                                            <td>
                                                <input type="text" name="Tahap{{ $i }}_vendor2"
                                                    class="form-control form-control-sm rupiah-input">
                                            </td>
                                            <td>
                                                <input type="text" name="Tahap{{ $i }}_vendor3"
                                                    class="form-control form-control-sm rupiah-input">
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <script>
                            // Fungsi format Rupiah secara otomatis saat mengetik
                            function toRupiah(angka) {
                                angka = typeof angka === "number" ? angka : parseFloat(angka.toString().replace(/\D/g, ''));
                                if (isNaN(angka)) return '';
                                let reverse = angka.toString().split('').reverse().join('');
                                let ribuan = reverse.match(/\d{1,3}/g);
                                let hasil = ribuan.join('.').split('').reverse().join('');
                                return 'Rp' + hasil;
                            }

                            function parseRupiah(str) {
                                if (!str) return 0;
                                let sanitized = str.replace(/[^0-9,]/g, '').replace(',', '.');
                                let val = parseFloat(sanitized);
                                if (isNaN(val)) return 0;
                                return val;
                            }

                            function formatRupiahLive(input) {
                                // Ambil posisi kursor sebelum perubahan
                                let selectionStart = input.selectionStart;
                                let beforeLength = input.value.length;

                                // Format value
                                let unformatted = input.value.replace(/[^0-9]/g, '');
                                if (unformatted === '') {
                                    input.value = '';
                                    return;
                                }
                                let number = parseInt(unformatted, 10);
                                input.value = toRupiah(number);

                                // Atur ulang posisi kursor ke posisi logis setelah reformat
                                let afterLength = input.value.length;
                                input.setSelectionRange(
                                    selectionStart + (afterLength - beforeLength),
                                    selectionStart + (afterLength - beforeLength)
                                );
                            }

                            function calcGT(vendor) {
                                // Ambil elemen terkait vendor
                                const hnaInput = document.getElementById('HNA_PPN_vendor' + vendor);
                                const discInput = document.getElementById('Disc_vendor' + vendor);
                                const gtInput = document.getElementById('GrandTotal_vendor' + vendor);

                                let hna = parseRupiah(hnaInput ? hnaInput.value : 0);
                                let disc = parseFloat(discInput ? discInput.value : 0);

                                if (isNaN(hna)) hna = 0;
                                if (isNaN(disc)) disc = 0;

                                // Grand Total + PPN = HNA + PPN dikurangi diskon persen (jika ada disc)
                                // Rumus: GT = HNA - (HNA * DISC / 100)
                                let gt = hna;
                                if (disc > 0) {
                                    gt = hna - (hna * disc / 100);
                                }
                                gtInput.value = gt > 0 ? toRupiah(gt.toFixed(0)) : '';
                            }

                            function setupCalcListeners() {
                                for (let i = 1; i <= 3; i++) {
                                    const hna = document.getElementById('HNA_PPN_vendor' + i);
                                    const disc = document.getElementById('Disc_vendor' + i);

                                    if (hna) {
                                        // Format on input
                                        hna.addEventListener('input', function() {
                                            formatRupiahLive(hna);
                                            calcGT(i);
                                        });
                                    }
                                    if (disc) {
                                        disc.addEventListener('input', function() {
                                            calcGT(i);
                                        });
                                    }
                                }
                            }

                            function formatRupiahFields() {
                                document.querySelectorAll('.rupiah-input').forEach(function(input) {
                                    // Format saat diketik
                                    input.addEventListener('input', function() {
                                        formatRupiahLive(input);
                                    });
                                    // On blur, pastikan tetap di-format
                                    input.addEventListener('blur', function() {
                                        formatRupiahLive(input);
                                    });
                                    // On focus, bisa strip prefix 'Rp' jika ingin bebas edit angka, atau biarkan sbb
                                });
                            }

                            document.addEventListener('DOMContentLoaded', function() {
                                setupCalcListeners();
                                formatRupiahFields();
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade text-muted" id="garansi" role="tabpanel" aria-labelledby="garansi-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Garansi.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="spesifikasi" role="tabpanel" aria-labelledby="spesifikasi-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Spesifikasi.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="populasi" role="tabpanel" aria-labelledby="populasi-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Populasi.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="keamanan-pasien" role="tabpanel" aria-labelledby="keamanan-pasien-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Keamanan Pasien.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="keamanan-petugas" role="tabpanel"
            aria-labelledby="keamanan-petugas-tab" tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Keamanan Petugas.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="mudah-digunakan" role="tabpanel" aria-labelledby="mudah-digunakan-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Kemudahan Digunakan.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="insiden" role="tabpanel" aria-labelledby="insiden-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait laporan insiden recall atau under call.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="rekomendasi" role="tabpanel" aria-labelledby="rekomendasi-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait penilaian Rekomendasi Klinis.</li>
            </ul>
        </div>
        <div class="tab-pane fade text-muted" id="perawatan" role="tabpanel" aria-labelledby="perawatan-tab"
            tabindex="0">
            <ul class="ps-3 mb-0">
                <li>Isi terkait Perawatan Pasca Beli.</li>
            </ul>
        </div>
    </div>
    <!-- Scroll to Top Button-->
    <button type="button" class="btn btn-primary btn-lg rounded-circle" id="scrollToTopBtn"
        style="position: fixed; bottom: 30px; right: 30px; display: none; z-index: 9999;">
        <i class="fa fa-arrow-up"></i>
    </button>

    @push('js')
        <script>
            window.onscroll = function() {
                const scrollBtn = document.getElementById('scrollToTopBtn');
                if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                    scrollBtn.style.display = "block";
                } else {
                    scrollBtn.style.display = "none";
                }
            };
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('scrollToTopBtn').onclick = function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        </script>
    @endpush
@endsection
