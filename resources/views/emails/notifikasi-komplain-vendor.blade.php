<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Permohonan Persetujuan Komplain Vendor</title>
</head>

<body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" bgcolor="#f4f6fa" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0"
                    style="margin:32px auto;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#d21f26;text-align:center;padding:28px 32px;">
                            <h2 style="color:#ffffff;margin:0;font-weight:bold;font-size:22px;letter-spacing:0.5px;">
                                Permohonan Persetujuan Komplain Vendor
                            </h2>
                            <p style="color:#ffe4e6;margin:6px 0 0;font-size:13px;">
                                {{ $komplain->KodeKomplain }} &bull; {{ $komplain->NamaRS }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 12px;color:#222222;">
                            <p style="font-size:15px;margin:0 0 12px;">
                                <b>Yth. Bapak/Ibu {{ $targetApproval->Nama ?? ($targetApproval->getUser->name ?? '-') }},</b>
                            </p>
                            <p style="font-size:14px;line-height:1.65;margin:0 0 16px;color:#333333;">
                                Dengan hormat,<br>
                                Bersama email ini kami informasikan terdapat pengajuan <b>Form Komplain Vendor</b>
                                yang membutuhkan peninjauan dan persetujuan dari Bapak/Ibu pada tahap
                                <b>Approval Urutan #{{ $targetApproval->Urutan }}</b>.
                            </p>

                            <table style="font-size:14px;border-collapse:collapse;width:100%;margin:12px 0 18px;border:1px solid #e5e7eb;">
                                <tr style="background:#f9fafb;">
                                    <td style="padding:8px 12px;width:170px;border-bottom:1px solid #e5e7eb;"><strong>Kode Komplain</strong></td>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;">{{ $komplain->KodeKomplain }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;"><strong>Nama RS</strong></td>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;">{{ $komplain->NamaRS }}</td>
                                </tr>
                                <tr style="background:#f9fafb;">
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;"><strong>Tanggal Laporan</strong></td>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;">
                                        {{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;"><strong>Pelapor</strong></td>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;">
                                        {{ $komplain->NamaPelapor }} ({{ $komplain->Jabatan }} - {{ $komplain->UnitKerja }})
                                    </td>
                                </tr>
                                <tr style="background:#f9fafb;">
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;"><strong>Nama Barang</strong></td>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;">{{ $komplain->NamaBarang }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;"><strong>Merk / Distributor</strong></td>
                                    <td style="padding:8px 12px;border-bottom:1px solid #e5e7eb;">
                                        {{ $komplain->Merk }} / {{ $komplain->Distributor }}
                                    </td>
                                </tr>
                                <tr style="background:#f9fafb;">
                                    <td style="padding:8px 12px;"><strong>Parameter Evaluasi</strong></td>
                                    <td style="padding:8px 12px;">
                                        {{ implode(', ', $komplain->ParameterEvaluasi ?? []) }}
                                        @if ($komplain->ParameterLainnya)
                                            ({{ $komplain->ParameterLainnya }})
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:13.5px;line-height:1.6;margin:0 0 18px;color:#555555;">
                                Laporan lengkap beserta berkas pendukung telah dilampirkan pada email ini.
                                Silakan klik tombol di bawah untuk meninjau dan memberikan persetujuan melalui sistem atau melalui menu <b>Approval Saya</b>:
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:8px 32px 28px;">
                            <a href="{{ route('komplain.show', encrypt($komplain->id)) }}"
                                style="display:inline-block;background:#d21f26;color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;padding:12px 28px;border-radius:6px;">
                                Review &amp; Setujui Dokumen
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc;padding:16px 32px;text-align:center;color:#64748b;font-size:12px;border-top:1px solid #e2e8f0;">
                            Email ini dikirim secara otomatis oleh sistem CCP RS Awal Bros.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
