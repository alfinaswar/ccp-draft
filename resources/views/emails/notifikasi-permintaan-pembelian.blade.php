<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Permohonan Persetujuan Permintaan Pembelian</title>
</head>

<body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,sans-serif;">
    <table width="100%" bgcolor="#f4f6fa" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" width="600" bgcolor="#fff" cellpadding="0" cellspacing="0"
                    style="margin:40px auto 0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#198754;text-align:center;padding:32px 32px 16px 32px;">
                            <h2 style="color:#fff;margin:0;font-weight:bold;font-size:2rem;letter-spacing:1px;">
                                Permohonan Persetujuan Permintaan Pembelian
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 0 32px;color:#222;">
                            <p style="font-size:17px;margin:0 0 10px 0;">
                                <b>Yth. Bapak/Ibu {{ $penilai->Nama ?? '-' }},</b>
                            </p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px 0;">
                                Dengan hormat,<br>
                                Bersama email ini, kami informasikan bahwa terdapat permintaan persetujuan
                                <b>Permintaan Pembelian</b> yang membutuhkan tindak lanjut dari Bapak/Ibu.
                            </p>
                            <table style="font-size:15px;border-collapse:collapse;margin:14px 0 18px 0;width:100%;">
                                <tr>
                                    <td style="padding:7px 0;width:170px;"><strong>Nomor Pengajuan</strong></td>
                                    <td style="padding:7px 0;">{{ $permintaan->NomorPermintaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 0;"><strong>RS / Cisco</strong></td>
                                    <td style="padding:7px 0;">{{ $permintaan->getPerusahaan->NamaLengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 0;"><strong>Tanggal</strong></td>
                                    <td style="padding:7px 0;">
                                        {{ \Carbon\Carbon::parse($permintaan->Tanggal)->format('d-m-Y') ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 0;"><strong>Tujuan</strong></td>
                                    <td style="padding:7px 0;">{{ $permintaan->Tujuan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 0;"><strong>Nama Barang / Jasa</strong></td>
                                    <td style="padding:7px 0;">
                                        @if (isset($permintaan->getDetail) && count($permintaan->getDetail))
                                            <ul style="margin:0;padding-left:18px;">
                                                @foreach ($permintaan->getDetail as $detail)
                                                    <li>{{ $detail->getBarang->Nama ?? '-' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 0;"><strong>Rencana Penempatan</strong></td>
                                    <td style="padding:7px 0;">
                                        @if (isset($permintaan->getDetail) && count($permintaan->getDetail))
                                            <ul style="margin:0;padding-left:18px;">
                                                @foreach ($permintaan->getDetail as $detail)
                                                    <li>{{ $detail->RencanaPenempatan ?? '-' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>

                            </table>
                            <p style="font-size:15px;line-height:1.6;margin:0 0 18px 0;">
                                Mohon kesediaan Bapak/Ibu untuk meninjau dan memberikan persetujuan
                            </p>
                        </td>
                    </tr>
                    <tr>
                        {{-- {{ dd($approval->ApprovalToken) }} --}}
                        <td align="center" style="padding:16px 32px;">
                            <a href="{{ route('pp.approve-email', $approval->ApprovalToken) }}"
                                style="
                                    display:inline-block;
                                    background:#198754;
                                    color:#ffffff;
                                    padding:16px 36px;
                                    text-decoration:none;
                                    border-radius:7px;
                                    font-size:17px;
                                    font-weight:bold;
                                    box-shadow:0 4px 16px rgba(25,135,84,0.18);
                                    letter-spacing:1px;
                                    transition:background 0.3s;
                                    margin-right: 10px;
                                ">
                                Setujui
                            </a>
                            <a href="{{ route('pp.show', encrypt($permintaan->id)) }}"
                                style="
                                    display:inline-block;
                                    background:#0d6efd;
                                    color:#ffffff;
                                    padding:16px 36px;
                                    text-decoration:none;
                                    border-radius:7px;
                                    font-size:17px;
                                    font-weight:bold;
                                    box-shadow:0 4px 16px rgba(13,110,253,0.15);
                                    letter-spacing:1px;
                                    transition:background 0.3s;
                                ">
                                Lihat
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 32px 0px 32px;">
                            <p style="font-size:13px;color:#666;background:#f2f4f7;padding:16px;border-radius:7px;">
                                Apabila tombol di atas tidak dapat diakses, silakan salin dan buka tautan berikut pada
                                browser Anda:<br>
                                <a style="color:#198754;word-break:break-all;"
                                    href="{{ route('pp.approve-email', encrypt($penilai->ApprovalToken)) }}">{{ route('pp.approve-email', encrypt($penilai->ApprovalToken)) }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 32px 24px 32px;">
                            <p style="font-size:15px;margin:24px 0 4px 0;">Atas perhatian dan kerja sama Bapak/Ibu, kami
                                ucapkan terima kasih.</p>
                            <p style="font-size:15px;margin:0;">
                                Hormat kami,<br>
                                <span style="font-weight:bold;color:#198754;">Departemen Procurement</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background:#e9ecef;text-align:center;padding:16px 0;font-size:13px;color:#999;border-radius:0 0 10px 10px;">
                            &copy; {{ date('Y') }} ABPROC. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
