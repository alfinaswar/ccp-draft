<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Validasi Tidak Sah / Belum Disetujui</title>
</head>

<body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,sans-serif;">
    <table width="100%" bgcolor="#f4f6fa" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" width="600" bgcolor="#fff" cellpadding="0" cellspacing="0"
                    style="margin:40px auto 0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#dc3545;text-align:center;padding:32px 32px 16px 32px;">
                            <img src="{{ asset('images/logo.png') }}" width="104" alt="Invalid Icon">
                            <h2 style="color:#fff;margin:0;font-weight:bold;font-size:2rem;letter-spacing:1px;">
                                VALIDASI TIDAK SAH / BELUM DISETUJUI
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 36px 40px 36px;color:#222;text-align:center;">
                            <p style="font-size:20px;font-weight:bold;color:#dc3545;margin-top:0;margin-bottom:18px;">
                                Maaf, validasi dokumen TIDAK SAH, TIDAK VALID atau BELUM DISETUJUI (BELUM DI-ACC).
                            </p>
                            <p style="font-size:16px;line-height:1.7;margin-bottom:25px;">
                                Sistem tidak menemukan atau tidak mengesahkan permohonan persetujuan ini.<br>
                                Hal ini bisa terjadi karena link tidak valid, dokumen belum di-ACC/disetujui, sudah
                                diproses sebelumnya, atau terjadi kesalahan data.<br>
                                Jika Anda yakin terjadi kendala, silakan hubungi tim pengelola atau administrator
                                sistem.
                            </p>
                            <p style="font-size:16px;line-height:1.7;color:#b71c1c;font-weight:bold;margin-top:18px;">
                                <span
                                    style="background:#ffeaea;padding:6px 14px;border-radius:6px;display: inline-block;">
                                    Jika ditemukan dokumen yang tidak sah atau belum di-ACC, segera laporkan ke pihak
                                    terkait.
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background:#f2dede;text-align:center;padding:17px 0;font-size:13px;color:#a94442;border-radius:0 0 10px 10px;">
                            &copy; {{ date('Y') }} ABPROC. Seluruh hak cipta dilindungi undang-undang.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
