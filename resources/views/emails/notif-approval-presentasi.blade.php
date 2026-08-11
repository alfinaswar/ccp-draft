<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Notifikasi Persetujuan Dokumen Presentasi</title>
</head>

@php
    // Helper function to format date in Indonesian, fallback if not provided elsewhere
    if (!function_exists('indoDate')) {
        function indoDate($date)
        {
            $bulan = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
            $dt = \Carbon\Carbon::parse($date);
            $month = $bulan[intval($dt->format('m'))];
            return $dt->format('d') . ' ' . $month . ' ' . $dt->format('Y');
        }
    }
@endphp

<body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,sans-serif;">
    <table width="100%" bgcolor="#f4f6fa" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" width="600" bgcolor="#fff" cellpadding="0" cellspacing="0"
                    style="margin:40px auto 0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#198754;text-align:center;padding:32px 32px 16px 32px;">
                            <h2 style="color:#fff;margin:0;font-weight:bold;font-size:2rem;letter-spacing:1px;">
                                Notifikasi Persetujuan Presentasi
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 0 32px;color:#222;">
                            <p style="font-size:17px;margin:0 0 10px 0;">
                                <b>Yth. Bapak / Ibu {{ $valueTesting->Nama ?? 'Penerima Persetujuan' }},</b>
                            </p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px 0;">
                                Dengan hormat,<br>
                                Melalui email ini kami sampaikan bahwa terdapat permohonan <b>Persetujuan Pembelian</b>
                                yang membutuhkan tindak lanjut dari Bapak/Ibu.<br>
                                <br>
                            <table style="font-size:15px; border-collapse:collapse; margin:0 0 18px 0;">
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">Kode
                                        Pengajuan</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $KodePengajuan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Rumah Sakit / Cisco</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $AsalRumahSakit ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Nama Permintaan</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $NamaPermintaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Rencana Penempatan</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $RencanaPenempatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Vendor ACC</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        {{ $VendorACC ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;width:160px;padding:4px 0;vertical-align:top;">
                                        Harga</td>
                                    <td style="padding:4px 0 4px 16px;vertical-align:top;">:
                                        @if (isset($Harga) && $Harga !== null)
                                            Rp {{ number_format($Harga, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>


                            </p>

                            <p style="font-size:15px;line-height:1.6;margin:0 0 18px 0;">
                                Silakan klik tombol di bawah ini untuk menyetujui pembelian:
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px; text-align:center;">
                            <a href="{{ route('usulan-investasi.approve', $Approval->ApprovalToken) }}"
                                style="display:inline-block;background:#198754;color:#fff;padding:16px 36px;border-radius:7px;font-size:17px;font-weight:bold;text-decoration:none;letter-spacing:1px;box-shadow:0 4px 16px rgba(25,135,84,0.14);">
                                ✓ Setujui
                            </a>
                        </td>
                    </tr>



                    <tr>
                        <td style="padding:10px 32px 24px 32px;">
                            <p style="font-size:15px;margin:24px 0 4px 0;">
                                Atas perhatian dan kerja sama Bapak, kami ucapkan terima kasih.
                            </p>
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
