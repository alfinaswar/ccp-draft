<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekomendasi CCP - Cetak Per Barang</title>
</head>

<body>
    @if ($permintaan->Jenis == 1)
        @include('rekomendasi-pembelian.rekap.hasil-rekomendasi')
    @else
        @include('rekomendasi-pembelian.rekap.hasil-rekomendasi-umum')
    @endif
    <div style="page-break-after: always;"></div>

    @if ($permintaan->Jenis == 1)
        @include('rekomendasi-pembelian.rekap.hasil-disposisi')
    @else
        @include('rekomendasi-pembelian.rekap.hasil-disposisi-umum')
    @endif
    <div style="page-break-after: always;"></div>
    @if ($permintaan->Jenis == 1)
        @include('rekomendasi-pembelian.rekap.hasil-hta-gpa')
        <div style="page-break-after: always;"></div>
    @endif

    <style>
        @page {
            size: A4 portrait;
        }
    </style>
    @include('rekomendasi-pembelian.rekap.fui')
    <div style="page-break-after: always;"></div>
    @if ($permintaan->Jenis == 1)
        @if (!is_null($datafs))
            @include('rekomendasi-pembelian.rekap.hasil-fs')
            <div style="page-break-after: always;"></div>
        @endif
    @endif
    @include('rekomendasi-pembelian.rekap.permintaan')
</body>

</html>
