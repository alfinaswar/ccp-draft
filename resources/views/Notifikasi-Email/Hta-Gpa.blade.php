<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>

    <h3>Detail Penilaian HTA / GPA</h3>

    {{-- INFORMASI BARANG --}}
    <table width="100%" border="1" cellpadding="6" cellspacing="0">
        <tr>
            <td width="25%"><b>Nama Barang</b></td>
            <td>{{ $pengajuan->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Merek</b></td>
            <td>{{ $pengajuan->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
        </tr>
    </table>

    <br>

    {{-- LOOP VENDOR --}}
    @foreach ($pengajuan->getVendor as $Vendor)
        <h4>Vendor : {{ $Vendor->getNamaVendor->Nama ?? '-' }}</h4>

        <table width="100%" border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Parameter</th>
                    <th>Deskripsi</th>
                    <th>Nilai</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengajuan->getJenisPermintaan->getForm->Parameter as $key => $pm)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $parameter[$pm - 1]->Nama ?? '-' }}</td>
                        <td>{!! $Vendor->getHtaGpa->Deskripsi[$key] ?? '-' !!}</td>
                        <td>
                            {{ $Vendor->getHtaGpa->Nilai1[$key] ?? '-' }},
                            {{ $Vendor->getHtaGpa->Nilai2[$key] ?? '-' }},
                            {{ $Vendor->getHtaGpa->Nilai3[$key] ?? '-' }},
                            {{ $Vendor->getHtaGpa->Nilai4[$key] ?? '-' }},
                            {{ $Vendor->getHtaGpa->Nilai5[$key] ?? '-' }}
                        </td>
                        <td><b>{{ $Vendor->getHtaGpa->SubTotal[$key] ?? '-' }}</b></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" align="right"><b>Grand Total</b></td>
                    <td><b>{{ $Vendor->getHtaGpa->GrandTotal ?? '-' }}</b></td>
                </tr>
            </tfoot>
        </table>

        <br>

        <table width="100%" border="1" cellpadding="6" cellspacing="0">
            <tr>
                <td><b>Umur Ekonomis</b></td>
                <td>{{ $Vendor->getHtaGpa->UmurEkonomis ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Tarif Diusulkan</b></td>
                <td>{{ $Vendor->getHtaGpa->TarifDiusulkan ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Buyback Period</b></td>
                <td>{{ $Vendor->getHtaGpa->BuybackPeriod ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Target Pemakaian Bulanan</b></td>
                <td>{{ $Vendor->getHtaGpa->TargetPemakaianBulanan ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Keterangan</b></td>
                <td>{!! $Vendor->getHtaGpa->Keterangan ?? '-' !!}</td>
            </tr>
        </table>

        <hr>
    @endforeach

    <p><b>Sistem ABPROC</b></p>

</body>

</html>
