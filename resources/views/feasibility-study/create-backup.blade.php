@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Input Data Feasibility Study</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fs.index') }}">Feasibility Study</a></li>
                    <li class="breadcrumb-item active">Input Data</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Input Data Feasibility Study</h4>
                </div>
                <form id="formFeasibilityStudy" action="{{ route('fs.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="idPengajuan" value="{{ $idPengajuan }}">
                    <input type="hidden" name="idPengajuanItem" value="{{ $idPengajuanItem }}">

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="NamaBarang" class="form-label fw-bold">Nama Barang</label>
                                <select class="form-select select2 @error('NamaBarang') is-invalid @enderror"
                                    name="NamaBarang" id="NamaBarang">
                                    <option value="">-- Pilih Nama Barang --</option>
                                    @if (isset($barang))
                                        <option value="{{ $barang->id }}"
                                            {{ old('NamaBarang', $barang->Nama ?? '') == $barang->Nama ? 'selected' : '' }}>
                                            {{ $barang->Nama }}
                                        </option>
                                    @endif
                                </select>
                                @error('NamaBarang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="NilaiInvestasi" class="form-label fw-bold">Nilai Investasi</label>
                                <input type="text"
                                    class="form-control rupiah @error('NilaiInvestasi') is-invalid @enderror"
                                    name="NilaiInvestasi" id="NilaiInvestasi"
                                    value="{{ old('NilaiInvestasi', isset($data->getFui->Total) ? number_format($data->getFui->Total, 0, ',', '.') : '0') }}"
                                    placeholder="Masukkan nilai investasi"
                                    oninput="this.value = formatRupiah(this.value); calculateDependentFields();">

                                @error('NilaiInvestasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="Spesifikasi" class="form-label fw-bold">Spesifikasi</label>
                                <textarea class="ckeditor @error('Spesifikasi') is-invalid @enderror" id="ckeditor" name="Spesifikasi" id="Spesifikasi"
                                    rows="10" placeholder="Masukkan spesifikasi">{{ old('Spesifikasi', $data->getRekomendasi->getRekomedasiDetail[0]) }}</textarea>
                                @error('Spesifikasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Biaya Tetap</h6>
                                    <div class="mb-3">
                                        <label for="BungaTetap" class="form-label fw-bold">Bunga Tetap</label>
                                        <input type="text"
                                            class="form-control rupiah @error('BungaTetap') is-invalid @enderror"
                                            name="BungaTetap" id="BungaTetap" value="{{ old('BungaTetap') }}"
                                            placeholder="Bunga Tetap akan dihitung otomatis" readonly>
                                        @error('BungaTetap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Penyusutan" class="form-label fw-bold">Penyusutan</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Penyusutan') is-invalid @enderror"
                                            name="Penyusutan" id="Penyusutan" value="{{ old('Penyusutan') }}"
                                            placeholder="Penyusutan akan dihitung otomatis" readonly>
                                        @error('Penyusutan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Maintenance" class="form-label fw-bold">Maintenance</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Maintenance') is-invalid @enderror"
                                            name="Maintenance" id="Maintenance" value="{{ old('Maintenance') }}"
                                            placeholder="Maintenance akan dihitung otomatis" readonly>
                                        @error('Maintenance')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Pegawai" class="form-label fw-bold">Pegawai</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Pegawai') is-invalid @enderror"
                                            name="Pegawai" id="Pegawai" value="{{ old('Pegawai') }}"
                                            placeholder="Masukkan biaya pegawai" oninput="formatRupiahAndCalculateTotal()">
                                        @error('Pegawai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="SewaGedung" class="form-label fw-bold">Sewa Gedung</label>
                                        <input type="text"
                                            class="form-control rupiah @error('SewaGedung') is-invalid @enderror"
                                            name="SewaGedung" id="SewaGedung" value="{{ old('SewaGedung') }}"
                                            placeholder="Masukkan biaya sewa gedung"
                                            oninput="formatRupiahAndCalculateTotal()">
                                        @error('SewaGedung')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="TotalBiayaTetap" class="form-label fw-bold">Total Biaya Tetap</label>
                                        <input type="text"
                                            class="form-control rupiah @error('TotalBiayaTetap') is-invalid @enderror"
                                            name="TotalBiayaTetap" id="TotalBiayaTetap"
                                            value="{{ old('TotalBiayaTetap') }}" placeholder="Masukkan total biaya tetap"
                                            readonly>
                                        @error('TotalBiayaTetap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <h6 class="fw-bold mb-3">Biaya Variable</h6>
                                    <div class="mb-3">
                                        <label for="Konsumable" class="form-label fw-bold">Konsumable</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Konsumable') is-invalid @enderror"
                                            name="Konsumable" id="Konsumable" value="{{ old('Konsumable') }}"
                                            placeholder="Masukkan biaya konsumable"
                                            oninput="formatRupiahInput(this); calculateTotalVariable();">
                                        @error('Konsumable')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Dokter" class="form-label fw-bold">Dokter</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Dokter') is-invalid @enderror"
                                            name="Dokter" id="Dokter" value="{{ old('Dokter') }}"
                                            placeholder="Masukkan biaya dokter"
                                            oninput="formatRupiahInput(this); calculateTotalVariable();">
                                        @error('Dokter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="TotalBiayaVariable" class="form-label fw-bold">Total Biaya
                                            Variable</label>
                                        <input type="text"
                                            class="form-control rupiah @error('TotalBiayaVariable') is-invalid @enderror"
                                            name="TotalBiayaVariable" id="TotalBiayaVariable"
                                            value="{{ old('TotalBiayaVariable') }}"
                                            placeholder="Masukkan total biaya variable" readonly>
                                        @error('TotalBiayaVariable')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Tambahan Kolom: Jumlah Perhari Pakai -->

                                </div>

                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">-</h6>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="BungaBank" class="form-label fw-bold">Bunga Bank</label>
                                            <input type="text"
                                                class="form-control @error('BungaBank') is-invalid @enderror"
                                                name="BungaBank" id="BungaBank" value="{{ old('BungaBank') }}"
                                                placeholder="Masukkan bunga bank (%)"
                                                oninput="calculateDependentFields();">
                                            @error('BungaBank')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="EstimasiPembiayaan" class="form-label fw-bold">Estimasi
                                                Pembiayaan (%)</label>
                                            <input type="text"
                                                class="form-control @error('EstimasiPembiayaan') is-invalid @enderror"
                                                name="EstimasiPembiayaan" id="EstimasiPembiayaan"
                                                value="{{ old('EstimasiPembiayaan') }}"
                                                placeholder="Masukkan estimasi pembiayaan (%)"
                                                oninput="calculateDependentFields();">
                                            @error('EstimasiPembiayaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="UmurEkonomis" class="form-label fw-bold">Umur Ekonomis
                                                (tahun)</label>
                                            <input type="text"
                                                class="form-control @error('UmurEkonomis') is-invalid @enderror"
                                                name="UmurEkonomis" id="UmurEkonomis" value="{{ old('UmurEkonomis') }}"
                                                placeholder="Masukkan umur ekonomis (tahun)"
                                                oninput="calculateDependentFields();">
                                            @error('UmurEkonomis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="Maintenance2" class="form-label fw-bold">Maintenance2 (%)</label>
                                            <input type="text"
                                                class="form-control @error('Maintenance2') is-invalid @enderror"
                                                name="Maintenance2" id="Maintenance2" value="{{ old('Maintenance2') }}"
                                                placeholder="Masukkan persentase Maintenance (contoh: 2 untuk 2%)"
                                                oninput="calculateDependentFields();">
                                            @error('Maintenance2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Kolom Jumlah Perhari Pakai/Jumlah Alat tetap nonaktif, input JumlahPakaiPertahun tetap -->
                                    <div class="mb-3">
                                        <label for="JumlahPakaiPertahun" class="form-label fw-bold">Jumlah Pakai
                                            Pertahun</label>
                                        <input type="number"
                                            class="form-control @error('JumlahPakaiPertahun') is-invalid @enderror"
                                            name="JumlahPakaiPertahun" id="JumlahPakaiPertahun"
                                            value="{{ old('JumlahPakaiPertahun') }}"
                                            placeholder="Akan terisi otomatis dari jumlah perhari pakai x jumlah alat">
                                        @error('JumlahPakaiPertahun')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label for="Tarif" class="form-label fw-bold">Tarif</label>
                                    <input type="text"
                                        class="form-control rupiah @error('Tarif') is-invalid @enderror" name="Tarif"
                                        id="Tarif" value="{{ old('Tarif') }}" placeholder="Masukkan tarif"
                                        oninput="formatRupiahInput(this); autoGenerateJumlahPasienUmumAndBpjs(); autoFillTarifUmum();">
                                    @error('Tarif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="fw-bold mb-0">Data Rugi Laba (7 Tahun)</h5>
                                </div>
                                <div class="card-body py-3">
                                    <div class="table-responsive">
                                        <table class="table align-middle" id="tabel-rugi-laba">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Keterangan</th>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <th>Tahun {{ $i }}</th>
                                                    @endfor
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th><strong>Tahun Ke</strong></th>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            {{ $i }}
                                                            <input type="hidden"
                                                                name="rugi_laba[TahunKe][{{ $i }}]"
                                                                value="{{ $i }}">
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Jumlah Pasien / Tindakan Umum</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm jumlah-pasien-umum"
                                                                id="JumlahPasienUmum{{ $i }}"
                                                                name="rugi_laba[JumlahPasien][{{ $i }}]"
                                                                value="{{ old("rugi_laba.JumlahPasien.$i") }}"
                                                                placeholder="Masukan jumlah pasien" readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Jumlah Pasien / Tindakan BPJS</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm jumlah-pasien-bpjs"
                                                                id="JumlahPasienBpjs{{ $i }}"
                                                                name="rugi_laba[JumlahPasienBpjs][{{ $i }}]"
                                                                value="{{ old("rugi_laba.JumlahPasienBpjs.$i") }}"
                                                                placeholder="Masukan jumlah pasien Bpjs" readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Tarif Umum</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="TarifUmum{{ $i }}"
                                                                name="rugi_laba[TarifUmum][{{ $i }}]"
                                                                value="{{ old("rugi_laba.TarifUmum.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.TarifUmum.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan tarif umum"
                                                                oninput="formatRupiahInput(this); if (this.id === 'TarifUmum1') autoFillTarifUmum();">
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Tarif BPJS</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[TarifBpjs][{{ $i }}]"
                                                                id="TarifBpjs{{ $i }}"
                                                                value="{{ old("rugi_laba.TarifBpjs.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.TarifBpjs.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan tarif BPJS"
                                                                oninput="formatRupiahInput(this); if (this.id === 'TarifBpjs1') autoFillTarifBpjs();">
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Revenue</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[Revenue][{{ $i }}]"
                                                                id="Revenue{{ $i }}"
                                                                value="{{ old("rugi_laba.Revenue.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.Revenue.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan revenue"
                                                                oninput="formatRupiahInput(this)">
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Total Biaya (Tetap + Var)</strong>
                                                        <br>
                                                        <small class="text-muted">= Biaya Tetap + Biaya Variable</small>
                                                    </td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[TotalBiaya][{{ $i }}]"
                                                                id="TotalBiaya{{ $i }}"
                                                                value="{{ old("rugi_laba.TotalBiaya.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.TotalBiaya.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Total = Biaya Tetap + Biaya Variable"
                                                                readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Biaya Tetap</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[BiayaTetap][{{ $i }}]"
                                                                id="BiayaTetap{{ $i }}"
                                                                value="{{ old("rugi_laba.BiayaTetap.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.BiayaTetap.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan biaya tetap" readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Biaya Variable</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[BiayaVariable][{{ $i }}]"
                                                                id="BiayaVariable{{ $i }}"
                                                                value="{{ old("rugi_laba.BiayaVariable.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.BiayaVariable.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan biaya variable" readonly>
                                                        </td>
                                                    @endfor
                                                </tr>

                                                <tr>
                                                    <td><strong>Net Profit</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[NetProfit][{{ $i }}]"
                                                                id="NetProfit{{ $i }}"
                                                                value="{{ old("rugi_laba.NetProfit.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.NetProfit.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan net profit"
                                                                oninput="formatRupiahInput(this)">
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>EBITDA</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[Ebitda][{{ $i }}]"
                                                                id="Ebitda{{ $i }}"
                                                                value="{{ old("rugi_laba.Ebitda.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.Ebitda.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan EBITDA" readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>Akumulasi EBITDA</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                name="rugi_laba[AkumEbitda][{{ $i }}]"
                                                                id="AkumEbitda{{ $i }}"
                                                                value="{{ old("rugi_laba.AkumEbitda.$i") ? 'Rp ' . number_format((int) preg_replace('/\D/', '', old("rugi_laba.AkumEbitda.$i")), 0, ',', '.') : '' }}"
                                                                placeholder="Masukan akumulasi EBITDA" readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                                <tr>
                                                    <td><strong>ROI Tahun Ke-</strong></td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="rugi_laba[RoiTahunKe][{{ $i }}]"
                                                                id="RoiTahunKe{{ $i }}"
                                                                value="{{ old("rugi_laba.RoiTahunKe.$i") }}"
                                                                placeholder="Masukan ROI tahun ke-{{ $i }}"
                                                                readonly>
                                                        </td>
                                                    @endfor
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('ajukan.show', encrypt($idPengajuan)) }}"
                                    class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" id="btnSimpanFs" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                </form>

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
    <script>
        function formatRupiah(angka, prefix = 'Rp ') {
            angka = angka ? angka.toString().replace(/[^,\d]/g, '') : '0';
            let split = angka.split(',');
            let sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                rupiah += (sisa ? '.' : '') + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + (rupiah ? rupiah : '0');
        }

        function parseRupiahToInt(str) {
            if (!str) return 0;
            return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
        }

        function calculateDependentFields() {
            // Nilai Investasi
            let nilaiInvestasiEl = document.getElementById('NilaiInvestasi');
            let nilaiInvestasi = parseRupiahToInt(nilaiInvestasiEl?.value);

            // Estimasi Pembiayaan (%) tanpa '%'
            let estimasiPembiayaanEl = document.getElementById('EstimasiPembiayaan');
            let estimasiPembiayaanStr = (estimasiPembiayaanEl?.value || '').replace('%', '').replace(',', '.').trim();
            let estimasiPembiayaan = estimasiPembiayaanStr !== "" ? parseFloat(estimasiPembiayaanStr) : 0;
            if (estimasiPembiayaan > 1) estimasiPembiayaan = estimasiPembiayaan / 100;

            // Bunga Bank (%) tanpa '%'
            let bungaBankEl = document.getElementById('BungaBank');
            let bungaBankStr = (bungaBankEl?.value || '').replace('%', '').replace(',', '.').trim();
            let bungaBank = bungaBankStr !== "" ? parseFloat(bungaBankStr) : 0;
            if (bungaBank > 1) bungaBank = bungaBank / 100;

            // Umur Ekonomis (tahun)
            let umurEkonomisEl = document.getElementById('UmurEkonomis');
            let umurEkonomisRaw = (umurEkonomisEl?.value || '').replace(',', '.').trim();
            let umurEkonomis = umurEkonomisRaw ? parseFloat(umurEkonomisRaw) : 0;

            // Maintenance2: treat as percent, e.g. 2 means 2%
            let maintenance2El = document.getElementById('Maintenance2');
            let maintenance2Raw = (maintenance2El?.value || '').replace('%', '').replace(',', '.').trim();
            let maintenance2 = maintenance2Raw ? parseFloat(maintenance2Raw) : 0;
            if (maintenance2 > 1) maintenance2 = maintenance2 / 100;

            // 1. BUNGA TETAP = (NilaiInvestasi * EstimasiPembiayaan) * BungaBank
            let bungaTetap = 0;
            if (nilaiInvestasi && estimasiPembiayaan && bungaBank) {
                bungaTetap = Math.round((nilaiInvestasi * estimasiPembiayaan) * bungaBank);
            }

            // 2. PENYUSUTAN = NilaiInvestasi / UmurEkonomis
            let penyusutan = 0;
            if (nilaiInvestasi && umurEkonomis) {
                penyusutan = Math.round(nilaiInvestasi / umurEkonomis);
            }

            // 3. MAINTENANCE = NilaiInvestasi * Maintenance2(%)
            let maintenance = 0;
            if (nilaiInvestasi && maintenance2) {
                maintenance = Math.round(nilaiInvestasi * maintenance2);
            }

            // Set calculated fields
            let bungaTetapEl = document.getElementById('BungaTetap');
            if (bungaTetapEl) {
                bungaTetapEl.value = bungaTetap > 0 ? formatRupiah(bungaTetap.toString()) : '';
            }
            let penyusutanEl = document.getElementById('Penyusutan');
            if (penyusutanEl) {
                penyusutanEl.value = penyusutan > 0 ? formatRupiah(penyusutan.toString()) : '';
            }
            let maintenanceEl = document.getElementById('Maintenance');
            if (maintenanceEl) {
                maintenanceEl.value = maintenance > 0 ? formatRupiah(maintenance.toString()) : '';
            }

            // Continue with total calculation (Pegawai + SewaGedung + calculated fields)
            formatRupiahAndCalculateTotal();
        }

        function formatRupiahAndCalculateTotal() {
            const fieldIds = [
                'BungaTetap',
                'Penyusutan',
                'Maintenance',
                'Pegawai',
                'SewaGedung'
            ];

            let total = 0;
            fieldIds.forEach(id => {
                let input = document.getElementById(id);
                if (input) {
                    let val = input.value;
                    input.value = val ? formatRupiah(val.replace(/^Rp\s*/, '')) : '';
                    total += parseRupiahToInt(input.value);
                }
            });

            let totalField = document.getElementById('TotalBiayaTetap');
            if (totalField) {
                totalField.value = total > 0 ? formatRupiah(total.toString()) : '';
            }

            // Run the update for Biaya Tetap Rugi Laba table per tahun
            updateBiayaTetapRugiLaba();
        }

        // Format for single input on the fly (for variable biaya & Tarif)
        function formatRupiahInput(input) {
            let val = input.value.replace(/^Rp\s*\.?/i, '');
            input.value = val ? formatRupiah(val) : '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Format initial currency
            var investasiInput = document.getElementById('NilaiInvestasi');
            if (investasiInput && investasiInput.value) {
                investasiInput.value = formatRupiah(investasiInput.value);
            }
            calculateDependentFields();

            let konsumableEl = document.getElementById('Konsumable');
            let dokterEl = document.getElementById('Dokter');
            let tarifEl = document.getElementById('Tarif');
            if (konsumableEl && konsumableEl.value) konsumableEl.value = formatRupiah(konsumableEl.value.replace(
                /^Rp\s*\.?/i, ''));
            if (dokterEl && dokterEl.value) dokterEl.value = formatRupiah(dokterEl.value.replace(/^Rp\s*\.?/i, ''));
            if (tarifEl && tarifEl.value) tarifEl.value = formatRupiah(tarifEl.value.replace(/^Rp\s*\.?/i, ''));
            calculateTotalVariable();

            autoGenerateJumlahPasienUmumAndBpjs();
            autoFillTarifUmum();
            autoFillTarifBpjs();

            updateBiayaTetapRugiLaba();
            updateBiayaVariableRugiLaba();
            updateTotalBiayaTetapVarRugiLaba();
        });

        // Re-calculate BungaTetap, Penyusutan, Maintenance when component fields change
        [
            'NilaiInvestasi',
            'EstimasiPembiayaan',
            'BungaBank',
            'UmurEkonomis',
            'Maintenance2'
        ].forEach(function(id) {
            document.addEventListener('input', function(e) {
                if (e && e.target && e.target.id === id) {
                    calculateDependentFields();
                }
            });
        });
    </script>
    <script>
        // Tidak berubah: TotalBiayaVariable (di atas form) hanya menjumlah konsumable + dokter secara langsung
        function calculateTotalVariable() {
            let konsumableEl = document.getElementById('Konsumable');
            let dokterEl = document.getElementById('Dokter');

            let konsumable = konsumableEl ? parseRupiahToInt(konsumableEl.value) : 0;
            let dokter = dokterEl ? parseRupiahToInt(dokterEl.value) : 0;
            let total = konsumable + dokter;

            let totalField = document.getElementById('TotalBiayaVariable');
            if (totalField) {
                totalField.value = total > 0 ? formatRupiah(total.toString()) : '';
            }

            // Update variable biaya per tahun di tabel rugi laba setelah input berubah
            updateBiayaVariableRugiLaba();
        }

        // Format & inisialisasi on load
        document.addEventListener('DOMContentLoaded', function() {
            let konsumableEl = document.getElementById('Konsumable');
            let dokterEl = document.getElementById('Dokter');
            let tarifEl = document.getElementById('Tarif');
            if (konsumableEl && konsumableEl.value) konsumableEl.value = formatRupiah(konsumableEl.value.replace(
                /^Rp\s*\.?/i, ''));
            if (dokterEl && dokterEl.value) dokterEl.value = formatRupiah(dokterEl.value.replace(/^Rp\s*\.?/i, ''));
            if (tarifEl && tarifEl.value) tarifEl.value = formatRupiah(tarifEl.value.replace(/^Rp\s*\.?/i, ''));
            calculateTotalVariable();

            autoGenerateJumlahPasienUmumAndBpjs();
            autoFillTarifUmum();
            autoFillTarifBpjs();

            updateBiayaTetapRugiLaba();
            updateBiayaVariableRugiLaba();
            updateTotalBiayaTetapVarRugiLaba();
        });
    </script>
    <script>
        function parseRupiahToIntPasien(rupiahStr) {
            if (!rupiahStr) return 0;
            return parseInt(rupiahStr.replace(/[^0-9]/g, '')) || 0;
        }

        // Main: Calculate BPJS and UMUM patients according to the requested formula.
        function autoGenerateJumlahPasienUmumAndBpjs() {
            let jumlahPakaiPertahunInput = document.getElementById('JumlahPakaiPertahun');
            let jumlahPakaiPertahun = jumlahPakaiPertahunInput ? parseFloat(jumlahPakaiPertahunInput.value) : 0;

            // If no jumlah pakai pertahun, clear fields and return
            if (!jumlahPakaiPertahun || isNaN(jumlahPakaiPertahun)) {
                for (let i = 1; i <= 8; i++) {
                    let fieldUmum = document.getElementById('JumlahPasienUmum' + i);
                    let fieldBpjs = document.getElementById('JumlahPasienBpjs' + i);
                    if (fieldUmum) fieldUmum.value = '';
                    if (fieldBpjs) fieldBpjs.value = '';
                }
                updateBiayaVariableRugiLaba();
                updateTotalBiayaTetapVarRugiLaba();
                return;
            }

            // 1. Hitung dulu jumlahBPJS tahun 1-8
            let bpjs = [];
            // Tahun 1
            bpjs[0] = Math.round(jumlahPakaiPertahun * 0.85);
            // Tahun 2-8 (index 1..7)
            for (let i = 1; i < 8; i++) {
                let prev = bpjs[i - 1];
                let percent = 0.10;
                if (i === 3) percent = 0.075; // Tahun 4
                bpjs[i] = Math.round(prev + (prev * percent));
            }
            // Tetapi tahun 4 (i==3) = tahun3+(tahun3*7.5%)
            // Sudah handled pada logic di atas

            // Set result to input
            for (let i = 0; i < 8; i++) {
                const field = document.getElementById('JumlahPasienBpjs' + (i + 1));
                if (field) field.value = bpjs[i];
            }

            // 2. Hitung jumlah UMUM
            let umum = [];
            // Tahun 1 = Jumlah Pakai Pertahun - BPJS tahun 1
            umum[0] = Math.round(jumlahPakaiPertahun - bpjs[0]);
            // Tahun 2-8
            for (let i = 1; i < 8; i++) {
                let prev = umum[i - 1];
                let percent = 0.10;
                if (i === 3) percent = 0.075; // Tahun 4
                umum[i] = Math.round(prev + (prev * percent));
            }
            // Set result to input
            for (let i = 0; i < 8; i++) {
                const field = document.getElementById('JumlahPasienUmum' + (i + 1));
                if (field) field.value = umum[i];
            }

            updateBiayaVariableRugiLaba();
            updateTotalBiayaTetapVarRugiLaba();
        }
    </script>
    <script>
        function autoFillTarifUmum() {
            let tarifUmum1 = document.getElementById('TarifUmum1');
            if (!tarifUmum1) return;
            formatRupiahInput(tarifUmum1);
            let value = tarifUmum1.value;
            for (let i = 2; i <= 8; i++) {
                let inputOther = document.getElementById('TarifUmum' + i);
                if (inputOther) {
                    inputOther.value = value;
                }
            }
        }

        function autoFillTarifBpjs() {
            let tarifBpjs1 = document.getElementById('TarifBpjs1');
            if (!tarifBpjs1) return;
            formatRupiahInput(tarifBpjs1);

            let value = tarifBpjs1.value;
            for (let i = 2; i <= 8; i++) {
                let inputOther = document.getElementById('TarifBpjs' + i);
                if (inputOther) {
                    inputOther.value = value;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            autoFillTarifBpjs();
        });
    </script>
    <script>
        function updateBiayaTetapRugiLaba() {
            const totalBiayaTetapInput = document.getElementById('TotalBiayaTetap');
            const penyusutanInput = document.getElementById('Penyusutan');
            let totalBiayaTetap = totalBiayaTetapInput ? parseRupiahToInt(totalBiayaTetapInput.value) : 0;
            let penyusutanVal = penyusutanInput ? parseRupiahToInt(penyusutanInput.value) : 0;

            let biayaTetap = [];
            for (let tahun = 1; tahun <= 8; tahun++) {
                if (tahun === 1) {
                    biayaTetap[tahun] = totalBiayaTetap;
                } else if (tahun === 2 || tahun === 3 || tahun === 4) {
                    biayaTetap[tahun] = Math.round(biayaTetap[tahun - 1] + biayaTetap[tahun - 1] * 0.05);
                } else if (tahun === 5) {
                    biayaTetap[tahun] = Math.round(biayaTetap[tahun - 1] + biayaTetap[tahun - 1] * 0.10);
                } else {
                    biayaTetap[tahun] = Math.round(biayaTetap[tahun - 1] + biayaTetap[tahun - 1] * 0.10);
                }
                let field = document.getElementById('BiayaTetap' + tahun);
                if (field) {
                    field.value = biayaTetap[tahun] && !isNaN(biayaTetap[tahun]) ?
                        formatRupiah(biayaTetap[tahun].toString(), 'Rp ') : '';
                }
            }
            updateTotalBiayaTetapVarRugiLaba();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const penyusutanInput = document.getElementById('Penyusutan');
            if (penyusutanInput) {
                penyusutanInput.addEventListener('input', function() {
                    updateBiayaTetapRugiLaba();
                });
            }
            updateBiayaTetapRugiLaba();
        });
    </script>
    <script>
        function updateBiayaVariableRugiLaba() {
            let baseKonsumable = parseRupiahToInt(document.getElementById('Konsumable') ? document.getElementById(
                'Konsumable').value : '0');
            let baseDokter = parseRupiahToInt(document.getElementById('Dokter') ? document.getElementById('Dokter').value :
                '0');

            for (let i = 1; i <= 8; i++) {
                let pasienUmum = parseRupiahToInt(document.getElementById('JumlahPasienUmum' + i) ? document.getElementById(
                    'JumlahPasienUmum' + i).value : '0');
                let pasienBpjs = parseRupiahToInt(document.getElementById('JumlahPasienBpjs' + i) ? document.getElementById(
                    'JumlahPasienBpjs' + i).value : '0');
                let totalPasien = pasienUmum + pasienBpjs;
                let fieldVar = document.getElementById('BiayaVariable' + i);

                let biayaVar = 0;

                if (i === 1) {
                    biayaVar = baseKonsumable + baseDokter;
                } else {
                    let konsumableNaik = baseKonsumable + (baseKonsumable * 0.05); // Naik 5% dari konsumable
                    biayaVar = (konsumableNaik * totalPasien) + baseDokter;
                }

                if (fieldVar) {
                    fieldVar.value = biayaVar && !isNaN(biayaVar) ? formatRupiah(biayaVar.toString(), 'Rp ') : '';
                }
            }
            updateTotalBiayaTetapVarRugiLaba();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('Konsumable')?.addEventListener('input', function() {
                updateBiayaVariableRugiLaba();
            });
            document.getElementById('Dokter')?.addEventListener('input', function() {
                updateBiayaVariableRugiLaba();
            });
        });

        function updateTotalBiayaTetapVarRugiLaba() {
            for (let i = 1; i <= 8; i++) {
                let biayaTetapInput = document.getElementById('BiayaTetap' + i);
                let biayaVarInput = document.getElementById('BiayaVariable' + i);
                let totalBiayaInput = document.getElementById('TotalBiaya' + i);

                let biayaTetap = biayaTetapInput ? parseRupiahToInt(biayaTetapInput.value) : 0;
                let biayaVar = biayaVarInput ? parseRupiahToInt(biayaVarInput.value) : 0;
                let total = biayaTetap + biayaVar;
                if (totalBiayaInput) {
                    totalBiayaInput.value = total > 0 ? formatRupiah(total.toString(), 'Rp ') : '';
                }
            }
        }
    </script>
    <script>
        // --- EBITDA, Akumulasi EBITDA, ROI Calculation ---
        function hitungRugiLabaSemuaTahun() {
            let tahun_count = 8;
            const bungaTetapEl = document.getElementById('BungaTetap');
            const penyusutanEl = document.getElementById('Penyusutan');
            let valBungaTetap = bungaTetapEl ? parseRupiahToInt(bungaTetapEl.value) : 0;
            let valPenyusutan = penyusutanEl ? parseRupiahToInt(penyusutanEl.value) : 0;

            let nilaiInvestasiEl = document.getElementById('NilaiInvestasi');
            let valInvestasi = nilaiInvestasiEl ? parseRupiahToInt(nilaiInvestasiEl.value) : 1;

            let akumEbitdaBefore = 0;

            for (let i = 1; i <= tahun_count; i++) {
                let jumlahPasienUmum = document.getElementById(`JumlahPasienUmum${i}`);
                let jumlahPasienBpjs = document.getElementById(`JumlahPasienBpjs${i}`);
                let tarifUmum = document.getElementById(`TarifUmum${i}`);
                let tarifBpjs = document.getElementById(`TarifBpjs${i}`);
                let revenue = document.getElementById(`Revenue${i}`);
                let biayaTetap = document.querySelector(`input[name="rugi_laba[BiayaTetap][${i}]"]`);
                let biayaVariable = document.querySelector(`input[name="rugi_laba[BiayaVariable][${i}]"]`);
                let totalBiaya = document.querySelector(`input[name="rugi_laba[TotalBiaya][${i}]"]`);
                let netProfit = document.querySelector(`input[name="rugi_laba[NetProfit][${i}]"]`);
                let ebitda = document.querySelector(`#Ebitda${i}`);
                let akumEbitda = document.querySelector(`#AkumEbitda${i}`);
                let roiTahunKe = document.querySelector(`#RoiTahunKe${i}`);

                let valJumlahPasienUmum = jumlahPasienUmum ? parseRupiahToInt(jumlahPasienUmum.value) : 0;
                let valJumlahPasienBpjs = jumlahPasienBpjs ? parseRupiahToInt(jumlahPasienBpjs.value) : 0;
                let valTarifUmum = tarifUmum ? parseRupiahToInt(tarifUmum.value) : 0;
                let valTarifBpjs = tarifBpjs ? parseRupiahToInt(tarifBpjs.value) : 0;

                let valRevenue = Math.ceil((valJumlahPasienUmum * valTarifUmum) + (valJumlahPasienBpjs * valTarifBpjs));

                if (revenue) {
                    revenue.value = valRevenue ? 'Rp ' + valRevenue.toLocaleString('id-ID') : '';
                }

                let valBiayaTetap = biayaTetap ? parseRupiahToInt(biayaTetap.value) : 0;
                let valBiayaVariable = biayaVariable ? parseRupiahToInt(biayaVariable.value) : 0;

                let valTotalBiaya = valBiayaTetap + valBiayaVariable;
                if (totalBiaya) {
                    totalBiaya.value = valTotalBiaya ? 'Rp ' + valTotalBiaya.toLocaleString('id-ID') : '';
                }

                let valNetProfit = valRevenue - valTotalBiaya;
                if (netProfit) {
                    netProfit.value = valNetProfit ? 'Rp ' + valNetProfit.toLocaleString('id-ID') : '';
                }

                // EBITDA = BungaTetap + Penyusutan + NetProfit
                let valEbitda = valBungaTetap + valPenyusutan + valNetProfit;
                if (ebitda) {
                    ebitda.value = valEbitda ? 'Rp ' + valEbitda.toLocaleString('id-ID') : '';
                }
                let valAkumEbitda = valEbitda + akumEbitdaBefore;
                if (akumEbitda) {
                    akumEbitda.value = valAkumEbitda ? 'Rp ' + valAkumEbitda.toLocaleString('id-ID') : '';
                }
                akumEbitdaBefore = valAkumEbitda;

                let valRoi = valInvestasi ? Math.round((valAkumEbitda / valInvestasi) * 100) : 0;
                if (roiTahunKe) {
                    roiTahunKe.value = valRoi ? (valRoi + '%') : '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            hitungRugiLabaSemuaTahun();
            const inputs = document.querySelectorAll(
                '#tabel-rugi-laba input:not([readonly]):not(.jumlah-pasien-umum):not(.jumlah-pasien-bpjs)');
            inputs.forEach(function(input) {
                input.addEventListener('input', hitungRugiLabaSemuaTahun);
            });

            for (let tahun = 1; tahun <= 8; tahun++) {
                let psUmum = document.getElementById(`JumlahPasienUmum${tahun}`);
                let psBpjs = document.getElementById(`JumlahPasienBpjs${tahun}`);
                let tUmum = document.getElementById(`TarifUmum${tahun}`);
                let tBpjs = document.getElementById(`TarifBpjs${tahun}`);
                if (psUmum) psUmum.addEventListener('input', hitungRugiLabaSemuaTahun);
                if (psBpjs) psBpjs.addEventListener('input', hitungRugiLabaSemuaTahun);
                if (tUmum) tUmum.addEventListener('input', hitungRugiLabaSemuaTahun);
                if (tBpjs) tBpjs.addEventListener('input', hitungRugiLabaSemuaTahun);
            }

            // Pastikan hitung ulang jika formula fields berubah
            ['BungaTetap', 'Penyusutan', 'NilaiInvestasi', 'JumlahPakaiPertahun'].forEach(id => {
                let el = document.getElementById(id);
                if (el) el.addEventListener('input', function() {
                    autoGenerateJumlahPasienUmumAndBpjs();
                    hitungRugiLabaSemuaTahun();
                });
            });
        });

        // Trigger value update after calculateDependentFields
        document.addEventListener('input', function(e) {
            if (
                ['NilaiInvestasi', 'EstimasiPembiayaan', 'BungaBank', 'UmurEkonomis', 'Maintenance2']
                .indexOf(e.target.id) !== -1
            ) {
                setTimeout(hitungRugiLabaSemuaTahun, 200);
            }
        });

        // Trigger auto generate when JumlahPakaiPertahun changes
        document.addEventListener('input', function(e) {
            if (e.target.id === 'JumlahPakaiPertahun') {
                setTimeout(autoGenerateJumlahPasienUmumAndBpjs, 150);
            }
        });
    </script>
    <!-- SWEET ALERT KONFIRMASI & LOADING SAAT SUBMIT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('formFeasibilityStudy');
            var btnSimpan = document.getElementById('btnSimpanFs');
            if (form && btnSimpan) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin menyimpan data Feasibility Study ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-check"></i> Ya, Simpan!',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#d33',
                        customClass: {
                            confirmButton: 'btn btn-success me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {

                            let loadingSwal;

                            Swal.fire({
                                title: '',
                                html: `
                                    <div style="display: flex; flex-direction: column; align-items: center; padding:16px 0 4px 0;">
                                        <div class="spinner-border text-success mb-3" style="width: 3.5rem; height: 3.5rem;" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div style="font-size: 1.12rem; font-weight: 500; color: #198754; margin-bottom: 2px;">
                                            Mohon tunggu
                                        </div>
                                        <div style="font-size: 1rem; color: #555;">
                                            Data sedang diproses & email notifikasi dikirimkan...
                                        </div>
                                        <span style="margin-top: 10px; font-size: .98rem; color:#888;">
                                            <b>Waktu diproses:</b>
                                            <span id="swal-timer" style="font-variant-numeric: tabular-nums;">0</span>
                                            <span>detik</span>
                                        </span>
                                    </div>
                                `,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    let elapsed = 0;
                                    document.getElementById('swal-timer').textContent =
                                        elapsed;
                                    loadingSwal = setInterval(() => {
                                        elapsed++;
                                        document.getElementById('swal-timer')
                                            .textContent = elapsed;
                                    }, 1000);
                                },
                                willClose: () => {
                                    if (loadingSwal) clearInterval(loadingSwal);
                                }
                            });

                            setTimeout(function() {
                                form.submit();
                            }, 700); // Delay supaya loading tampil dulu
                        }
                    });

                    return false;
                });
            }
        });
    </script>
@endpush
