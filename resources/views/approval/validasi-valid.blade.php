<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Validasi Sah</title>
</head>

<body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,sans-serif;">
    <table width="100%" bgcolor="#f4f6fa" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" width="600" bgcolor="#fff" cellpadding="0" cellspacing="0"
                    style="margin:40px auto 0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#198754;text-align:center;padding:32px 32px 16px 32px;">
                            <img src="{{ asset('assets/img/ccp/RS_Awal_Bros_merah.png') }}" width="254"
                                alt="Valid Icon">
                            <h2 style="color:#fff;margin:0;font-weight:bold;font-size:2rem;letter-spacing:1px;">
                                VALIDASI SAH
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 36px 40px 36px;color:#222;text-align:center;">
                            <p style="font-size:20px;font-weight:bold;color:#198754;margin-top:0;margin-bottom:18px;">
                                Selamat! Validasi dokumen <span style="color:#198754;">SAH / VALID</span>.
                            </p>
                            <p style="font-size:16px;line-height:1.7;margin-bottom:25px;">
                                Sistem berhasil menemukan dan mengesahkan permohonan persetujuan
                                ini.<br>
                                Pastikan Anda menjaga dokumen ini dengan baik dan hanya membagikan ke pihak yang
                                berwenang.<br>
                                Untuk informasi lebih lanjut atau verifikasi tambahan, silakan hubungi tim
                                pengelola/administrator sistem.
                            </p>
                            <table
                                style="margin: 28px auto 18px auto; border-collapse: collapse; width: 89%; background: #f7faf7; border-radius: 7px; box-shadow:0 1px 4px rgba(20,83,45,0.06);">
                                <tr>
                                    <td colspan="2"
                                        style="text-align:left;padding:13px 18px 7px 18px;font-size:16px;color:#198754;font-weight:bold;">
                                        Informasi Persetujuan:
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="text-align:left;padding:5px 18px 5px 22px;font-size:15px;color:#14532d;width:48%;">
                                        Disetujui oleh</td>
                                    <td style="text-align:left;padding:5px 18px;font-size:15px;color:#14532d;">
                                        {{ $approval->getUser ? $approval->getUser->name : '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align:left;padding:5px 18px 5px 22px;font-size:15px;color:#14532d;">
                                        Disetujui Pada</td>
                                    <td style="text-align:left;padding:5px 18px;font-size:15px;color:#14532d;">
                                        {{ $approval->updated_at ? \Carbon\Carbon::parse($approval->updated_at)->translatedFormat('d F Y H:i') : '-' }}
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size:16px;line-height:1.7;color:#14532d;font-weight:bold;margin-top:18px;">
                                <span
                                    style="background:#e7fbe9;padding:6px 14px;border-radius:6px;display: inline-block;">
                                    Jika Anda menemukan kejanggalan data, harap segera hubungi pihak terkait.
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background:#e7fbe9;text-align:center;padding:17px 0;font-size:13px;color:#198754;border-radius:0 0 10px 10px;">
                            &copy; {{ date('Y') }} ABPROC. Seluruh hak cipta dilindungi undang-undang.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
