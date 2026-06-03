<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Persetujuan Penilaian HTA / GPA</title>
</head>

<body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,sans-serif;">
    <table width="100%" bgcolor="#f4f6fa" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" width="600" bgcolor="#fff" cellpadding="0" cellspacing="0"
                    style="margin:40px auto 0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#198754;text-align:center;padding:32px 32px 16px 32px;">
                            {{-- <img src="https://id.wikipedia.org/wiki/Berkas:RS_Awal_Bros_merah.png" width="104"
                                alt="Approval Icon"> --}}
                            <h2 style="color:#fff;margin:0;font-weight:bold;font-size:2rem;letter-spacing:1px;">
                                Persetujuan Penilaian HTA / GPA</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 0 32px;color:#222;">
                            <p style="font-size:17px;margin:0 0 10px 0;">
                                <b>Yth. Bapak/Ibu {{ $penilai->Nama ?? 'Penilai' }},</b>
                            </p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px 0;">
                                Dengan hormat,<br>
                                Bersama email ini, kami informasikan bahwa proses penilaian <b>HTA / GPA</b> telah
                                selesai dan membutuhkan persetujuan lebih lanjut dari Bapak/Ibu sebagai penilai terkait.
                            </p>
                            <p style="font-size:15px;line-height:1.6;margin:0 0 18px 0;">
                                Mohon kesediaan Bapak/Ibu untuk memberikan persetujuan dengan menekan tombol di bawah
                                ini:
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:1px 32px 0 32px;color:#222;">

                            <table style="font-size:15px; border-collapse:collapse; margin:0 0 18px 0;">
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">Kode
                                        Pengajuan</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $pengajuan->KodePengajuan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Rumah Sakit / Cisco</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $pengajuan->getPerusahaan->NamaLengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Nama Permintaan</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $pengajuan->getPermintaan->getDetail[0]->getBarang->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Rencana Penempatan</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $pengajuan->getPermintaan->getDetail[0]->RencanaPenempatan ?? '-' }}</td>
                                </tr>


                            </table>




                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:16px 32px;">
                            <a href="{{ route('htagpa.approve', $penilai->ApprovalToken) }}"
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
                                ">
                                SETUJUI HTA / GPA
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 32px 0px 32px;">
                            <p style="font-size:13px;color:#666;background:#f2f4f7;padding:16px;border-radius:7px;">
                                Apabila tombol di atas tidak dapat diakses, silakan salin dan buka tautan berikut pada
                                browser Anda:<br>
                                <a style="color:#198754;word-break:break-all;"
                                    href="{{ route('htagpa.approve', $penilai->ApprovalToken) }}">{{ route('htagpa.approve', $penilai->ApprovalToken) }}</a>
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
    {{-- </body> {{ dd($pengajuan) }} --}}

</html>
