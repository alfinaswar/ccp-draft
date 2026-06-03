<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Usulan Investasi - RS Awal Bros</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            background: white;
            padding: 15mm;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .logo {
            background: #d32f2f;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 14pt;
            border: 2px solid #000;
        }

        .header-title {
            text-align: right;
            flex: 1;
            margin-left: 20px;
        }

        .header-title h1 {
            font-size: 12pt;
            margin-bottom: 3px;
        }

        .header-title p {
            font-size: 9pt;
        }

        .info-box {
            background: #f5f5f5;
            padding: 8px;
            margin: 10px 0;
            font-size: 8pt;
            border: 1px solid #999;
        }

        .section-title {
            background: #e0e0e0;
            padding: 5px 8px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            border: 1px solid #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 8pt;
        }

        td {
            font-size: 8pt;
        }

        .form-row {
            display: flex;
            margin-bottom: 8px;
        }

        .form-label {
            width: 150px;
            font-weight: normal;
        }

        .form-value {
            flex: 1;
            border-bottom: 1px solid #000;
            min-height: 20px;
        }

        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin: 0 5px;
            vertical-align: middle;
        }

        .note {
            font-size: 7pt;
            font-style: italic;
            color: #333;
            margin: 5px 0;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .footer-note {
            background: #ffeb3b;
            padding: 8px;
            margin-top: 20px;
            font-size: 7pt;
            border: 1px solid #000;
            text-align: right;
        }

        .small-text {
            font-size: 7pt;
        }

        .budget-section {
            margin: 10px 0;
        }

        .budget-row {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #999;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">RS AWAL BROS</div>
        <div class="header-title">
            <h1>FORMULIR USULAN INVESTASI</h1>
            <p>Nominal Permintaan 3 Juta s/d 50 Juta</p>
        </div>
    </div>

    <div class="info-box">
        <strong>Data lengkap tidak diperlukan, sertakan dokumentasi Anamnese dan Discharge, kemudian diserahkan ke
            Bagian : Pembelian Barang / Jasa Media dan Umum :</strong>
    </div>

    <div class="section-title">Sesi Awal Pemilihan Departemen terkait:</div>

    <table>
        <tr>
            <td style="width: 50%;">
                <strong>Tanggal</strong>: _______________________<br>
                <strong>Nama Kepala Divisi</strong>: _______________________<br>
                <strong>Kategori</strong>:
                <span class="checkbox"></span> Baru /
                <span class="checkbox"></span> Penggantian /
                <span class="checkbox"></span> Perbaikan (*)
            </td>
            <td style="width: 50%;">
                <strong>Tanggal</strong>: _______________________<br>
                <strong>Nama Kepala Divisi</strong>: _______________________<br>
                <strong>Kategori</strong>:
                <span class="checkbox"></span> Baru /
                <span class="checkbox"></span> Penggantian /
                <span class="checkbox"></span> Perbaikan (*)
            </td>
        </tr>
    </table>

    <div style="margin: 10px 0;">
        <strong>* pilih satu</strong><br>
        Dengan ini kami ajukan permohonan untuk pengajuan barang / jasa dengan <strong>alasan</strong> sebagai berikut
    </div>

    <div style="border: 1px solid #000; min-height: 60px; padding: 8px; margin: 10px 0;">
        <!-- Area untuk alasan -->
    </div>

    <table style="margin-top: 15px;">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama barang / jasa (sesuaikan spesifikasi yang diwajibkan, contohkan juga nama
                    supplier/current procure, nomor rekanan yang terdaftar)</th>
                <th style="width: 10%;">Jumlah</th>
                <th style="width: 25%;" colspan="3">Usulan-usulan yang harus dikibi</th>
                <th style="width: 15%;">Total</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Discount</th>
                <th>PPN</th>
                <th>PPN</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    Nama barang / jasa spesifikasi :<br>
                    _______________________________<br><br>
                    Contact person, nomor telepon yang bisa dihubungi :<br>
                    _______________________________
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>
                    Nama barang / jasa spesifikasi :<br>
                    (nama supplier)<br><br>
                    Contact person, nomor telepon yang bisa dihubungi :<br>
                    _______________________________
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>
                    Nama barang / jasa spesifikasi :<br>
                    (nama supplier)<br><br>
                    Contact person, nomor telepon yang bisa dihubungi :<br>
                    _______________________________
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="note">
        Lampiran : 1. Kebutuhan > Rp dan Rp 100.000.000<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Untuk nominal di atas Rp 100.000.000
        lengkapan ITLA/OPA<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Perencanaaan harga yang akan dimiliki dan
        perbandingan antara vendor / Jika tidak ada vendor perbandingan bisa menggunakan vendor yang sama dengan
        pekerjaan yang sama sebelumnya, sekalian untuk stock kontak KSO diharapkan tidak disertai anggarannya melainkan
        dengan stock yang sebetlumbaran:
    </div>

    <table style="margin-top: 15px;">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Biaya/ Harga Akhir</th>
                <th style="width: 35%;">Supplier yang dipilih</th>
                <th style="width: 15%;">Harga = Diskon = PPN</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" style="height: 40px;"></td>
            </tr>
        </tbody>
    </table>

    <div class="budget-section">
        <div class="budget-row">
            <strong>Verifikasi Keuangan</strong><br>
            Sudah masuk RKAP dan digunakannya 5% :
            <span class="checkbox"></span> Ya
            <span class="checkbox"></span> Tidak
            <br><br>
            Sisa Budget dari RKAP untuk tahun ini yang masih dapat digunakan Rp. _____________ (bila tidak di-gunakan
            tuliasi)
        </div>

        <div class="budget-row">
            <strong>Verifikasi Keuangan</strong><br>
            Sudah masuk RKAP dan digunakannya 5% :
            <span class="checkbox"></span> Ya
            <span class="checkbox"></span> Tidak
            <br><br>
            Sisa Budget dari RKAP untuk tahun ini yang masih dapat digunakan Rp. _____________
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Disetujui,<br><strong>Kepala Divisi Jasa/Obat</strong></div>
            <div class="signature-line">(Tgl: XXXXX)</div>
        </div>
        <div class="signature-box">
            <div>Mengetahui<br><strong>Direktur</strong></div>
            <div class="signature-line">(Tgl: XXXXX)</div>
        </div>
    </div>

    <div class="footer-note">
        *) Cetak Form bila perlu<br>
        **) Kategori Barang / Jasa : Media dan Umum Rp. 3.000.000 (belum)
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()"
            style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #d32f2f; color: white; border: none; border-radius: 5px;">
            Cetak PDF
        </button>
    </div>
</body>

</html>
