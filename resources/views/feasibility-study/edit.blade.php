@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Edit Data Feasibility Study</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fs.index') }}">Feasibility Study</a></li>
                    <li class="breadcrumb-item active">Edit Data</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Edit Data Feasibility Study</h4>
                </div>

                <form id="formFeasibilityStudy" action="{{ route('fs.update', $fs->id) }}" method="POST"
                    autocomplete="off">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="idPengajuan" value="{{ $idPengajuan }}">
                    <input type="hidden" name="idPengajuanItem" value="{{ $idPengajuanItem }}">

                    <div class="card-body">
                        <div class="alert" style="border: 2px solid #ffc107; background-color: #fff8e1; color: #664d03;"
                            role="alert">
                            Apabila Anda mengalami kesulitan dalam pengisian data atau formulir, silakan hubungi tim support
                            melalui grup <b>ABPROC</b> untuk mendapatkan bantuan.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="NamaBarang" class="form-label fw-bold">Nama Barang</label>
                                <select class="form-select select2 @error('NamaBarang') is-invalid @enderror"
                                    name="NamaBarang" id="NamaBarang" disabled>
                                    <option value="">-- Pilih Nama Barang --</option>
                                    @if (isset($barang))
                                        <option value="{{ $barang->id }}"
                                            {{ old('NamaBarang', $barang->id ?? '') == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->Nama }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden" name="NamaBarang" value="{{ $barang->id ?? '' }}">
                                @error('NamaBarang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="NilaiInvestasi" class="form-label fw-bold">Nilai Investasi</label>
                                <input type="text"
                                    class="form-control rupiah @error('NilaiInvestasi') is-invalid @enderror"
                                    name="NilaiInvestasi" id="NilaiInvestasi"
                                    value="{{ old('NilaiInvestasi', isset($fs->NilaiInvestasi) ? number_format($fs->NilaiInvestasi, 0, ',', '.') : '0') }}"
                                    placeholder="Masukkan nilai investasi"
                                    oninput="this.value = formatRupiah(this.value); calculateDependentFields();">

                                @error('NilaiInvestasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="Spesifikasi" class="form-label fw-bold">Spesifikasi</label>
                                <textarea class="ckeditor @error('Spesifikasi') is-invalid @enderror" id="ckeditor" name="Spesifikasi" rows="10"
                                    placeholder="Masukkan spesifikasi">{{ old('Spesifikasi', $fs->Spesifikasi ?? '') }}</textarea>
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
                                            name="BungaTetap" id="BungaTetap"
                                            value="{{ old('BungaTetap', $fs->BungaTetap ?? '') }}"
                                            placeholder="Bunga Tetap akan dihitung otomatis">
                                        @error('BungaTetap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Penyusutan" class="form-label fw-bold">Penyusutan</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Penyusutan') is-invalid @enderror"
                                            name="Penyusutan" id="Penyusutan"
                                            value="{{ old('Penyusutan', $fs->Penyusutan ?? '') }}"
                                            placeholder="Penyusutan akan dihitung otomatis">
                                        @error('Penyusutan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Maintenance" class="form-label fw-bold">Maintenance</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Maintenance') is-invalid @enderror"
                                            name="Maintenance" id="Maintenance"
                                            value="{{ old('Maintenance', $fs->Maintenance ?? '') }}"
                                            placeholder="Maintenance akan dihitung otomatis">
                                        @error('Maintenance')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="Pegawai" class="form-label fw-bold">Pegawai</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Pegawai') is-invalid @enderror"
                                            name="Pegawai" id="Pegawai"
                                            value="{{ old('Pegawai', $fs->Pegawai ?? '') }}"
                                            placeholder="Masukkan biaya pegawai"
                                            oninput="formatRupiahAndCalculateTotal()">
                                        @error('Pegawai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="SewaGedung" class="form-label fw-bold">Sewa Gedung</label>
                                        <input type="text"
                                            class="form-control rupiah @error('SewaGedung') is-invalid @enderror"
                                            name="SewaGedung" id="SewaGedung"
                                            value="{{ old('SewaGedung', $fs->SewaGedung ?? '') }}"
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
                                            value="{{ old('TotalBiayaTetap', $fs->TotalBiayaTetap ?? '') }}"
                                            placeholder="Masukkan total biaya tetap">
                                        @error('TotalBiayaTetap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <h6 class="fw-bold mb-3">Biaya Variable</h6>
                                    <div class="mb-3">
                                        <label for="Konsumable" class="form-label fw-bold">Konsumable</label>
                                        <input type="text"
                                            class="form-control rupiah @error('Konsumable') is-invalid @enderror"
                                            name="Konsumable" id="Konsumable"
                                            value="{{ old('Konsumable', $fs->Konsumable ?? '') }}"
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
                                            name="Dokter" id="Dokter" value="{{ old('Dokter', $fs->Dokter ?? '') }}"
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
                                            value="{{ old('TotalBiayaVariable', $fs->TotalBiayaVariable ?? '') }}"
                                            placeholder="Masukkan total biaya variable">
                                        @error('TotalBiayaVariable')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                </div>

                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">-</h6>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="BungaBank" class="form-label fw-bold">Bunga Bank</label>
                                            <input type="text"
                                                class="form-control @error('BungaBank') is-invalid @enderror"
                                                name="BungaBank" id="BungaBank"
                                                value="{{ old('BungaBank', $fs->BungaBank ?? '') }}"
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
                                                value="{{ old('EstimasiPembiayaan', $fs->EstimasiPembiayaan ?? '') }}"
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
                                                name="UmurEkonomis" id="UmurEkonomis"
                                                value="{{ old('UmurEkonomis', $fs->UmurEkonomis ?? '') }}"
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
                                                name="Maintenance2" id="Maintenance2"
                                                value="{{ old('Maintenance2', $fs->Maintenance2 ?? '') }}"
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
                                            value="{{ old('JumlahPakaiPertahun', $fs->JumlahPakaiPertahun ?? '') }}"
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
                                        id="Tarif" value="{{ old('Tarif', $fs->Tarif ?? '') }}"
                                        placeholder="Masukkan tarif"
                                        oninput="formatRupiahInput(this); autoGenerateJumlahPasienUmumAndBpjs(); autoFillTarifUmum();">
                                    @error('Tarif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="fw-bold mb-0">Data Rugi Laba (8 Tahun)</h5>
                                </div>
                                <div class="card-body py-3">
                                    <div class="table-responsive">
                                        <table class="table align-middle" id="tabel-rugi-laba">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Keterangan</th>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <th>Tahun {{ $detail->TahunKe }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th><strong>Tahun Ke</strong></th>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            {{ $detail->TahunKe }}
                                                            <input type="hidden"
                                                                name="rugi_laba[TahunKe][{{ $detail->TahunKe }}]"
                                                                value="{{ $detail->TahunKe }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Jumlah Pasien / Tindakan Umum</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm jumlah-pasien-umum"
                                                                id="JumlahPasienUmum{{ $detail->TahunKe }}"
                                                                name="rugi_laba[JumlahPasien][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.JumlahPasien.{$detail->TahunKe}", $detail->JumlahPasien) }}"
                                                                placeholder="Masukan jumlah pasien">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Jumlah Pasien / Tindakan BPJS</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm jumlah-pasien-bpjs"
                                                                id="JumlahPasienBpjs{{ $detail->TahunKe }}"
                                                                name="rugi_laba[JumlahPasienBpjs][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.JumlahPasienBpjs.{$detail->TahunKe}", $detail->JumlahPasienBpjs) }}"
                                                                placeholder="Masukan jumlah pasien Bpjs">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Tarif Umum</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="TarifUmum{{ $detail->TahunKe }}"
                                                                name="rugi_laba[TarifUmum][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.TarifUmum.{$detail->TahunKe}", $detail->TarifUmum ? 'Rp ' . number_format($detail->TarifUmum, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan tarif umum"
                                                                oninput="formatRupiahInput(this); if (this.id === 'TarifUmum1') autoFillTarifUmum();">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Tarif BPJS</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="TarifBpjs{{ $detail->TahunKe }}"
                                                                name="rugi_laba[TarifBpjs][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.TarifBpjs.{$detail->TahunKe}", $detail->TarifBpjs ? 'Rp ' . number_format($detail->TarifBpjs, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan tarif BPJS"
                                                                oninput="formatRupiahInput(this); if (this.id === 'TarifBpjs1') autoFillTarifBpjs();">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Revenue</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="Revenue{{ $detail->TahunKe }}"
                                                                name="rugi_laba[Revenue][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.Revenue.{$detail->TahunKe}", $detail->Revenue ? 'Rp ' . number_format($detail->Revenue, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan revenue"
                                                                oninput="formatRupiahInput(this)">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Total Biaya (Tetap + Var)</strong>
                                                        <br>
                                                        <small class="text-muted">= Biaya Tetap + Biaya Variable</small>
                                                    </td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="TotalBiaya{{ $detail->TahunKe }}"
                                                                name="rugi_laba[TotalBiaya][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.TotalBiaya.{$detail->TahunKe}", $detail->TotalBiaya ? 'Rp ' . number_format($detail->TotalBiaya, 0, ',', '.') : '') }}"
                                                                placeholder="Total = Biaya Tetap + Biaya Variable">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Biaya Tetap</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="BiayaTetap{{ $detail->TahunKe }}"
                                                                name="rugi_laba[BiayaTetap][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.BiayaTetap.{$detail->TahunKe}", $detail->BiayaTetap ? 'Rp ' . number_format($detail->BiayaTetap, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan biaya tetap">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Biaya Variable</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="BiayaVariable{{ $detail->TahunKe }}"
                                                                name="rugi_laba[BiayaVariable][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.BiayaVariable.{$detail->TahunKe}", $detail->BiayaVariable ? 'Rp ' . number_format($detail->BiayaVariable, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan biaya variable">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Net Profit</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="NetProfit{{ $detail->TahunKe }}"
                                                                name="rugi_laba[NetProfit][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.NetProfit.{$detail->TahunKe}", $detail->NetProfit ? 'Rp ' . number_format($detail->NetProfit, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan net profit"
                                                                oninput="formatRupiahInput(this)">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>EBITDA</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="Ebitda{{ $detail->TahunKe }}"
                                                                name="rugi_laba[Ebitda][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.Ebitda.{$detail->TahunKe}", $detail->Ebitda ? 'Rp ' . number_format($detail->Ebitda, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan EBITDA">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Akumulasi EBITDA</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm rupiah-input"
                                                                id="AkumEbitda{{ $detail->TahunKe }}"
                                                                name="rugi_laba[AkumEbitda][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.AkumEbitda.{$detail->TahunKe}", $detail->AkumEbitda ? 'Rp ' . number_format($detail->AkumEbitda, 0, ',', '.') : '') }}"
                                                                placeholder="Masukan akumulasi EBITDA">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>ROI Tahun Ke-</strong></td>
                                                    @foreach ($fs->getFsDetail as $detail)
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="RoiTahunKe{{ $detail->TahunKe }}"
                                                                name="rugi_laba[RoiTahunKe][{{ $detail->TahunKe }}]"
                                                                value="{{ old("rugi_laba.RoiTahunKe.{$detail->TahunKe}", $detail->RoiTahunKe) }}"
                                                                placeholder="Masukan ROI tahun ke-{{ $detail->TahunKe }}">
                                                        </td>
                                                    @endforeach
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
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div>
                </form>

            </div>

        </div>

    </div>
    </div>
    @if (!empty($fs))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Status Approval</h5>
                    {{-- @if (!empty($approval) && count($approval) > 0)
                        <a href="{{ route('fs.kirim-ulang-notifikasi', $fs->id) }}" class="btn btn-primary"
                            id="btnKonfirmasiKirimUlang">
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
                                                @php
                                                    $approvalUrl = route('fs.approve', $item->ApprovalToken ?? '');
                                                @endphp
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="navigator.clipboard.writeText('{{ $approvalUrl }}'); Swal.fire('Disalin!','Link approval telah disalin ke clipboard!','success')">
                                                    <i class="fa fa-copy"></i> Salin Link Approval
                                                </button>
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
