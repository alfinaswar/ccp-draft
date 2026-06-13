<?php

namespace App\Mail;

use App\Models\RekomendasiDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NotifApprovalPresentasi extends Mailable
{
    use Queueable, SerializesModels;

    public $fui;
    public $rekomendasi;
    public $approvalFUITesting;

    public function __construct(
        $fui,
        $rekomendasi = null,
        $approvalFUITesting = null,
    ) {
        $this->fui = $fui;
        $this->rekomendasi = $rekomendasi;
        $this->approvalFUITesting = $approvalFUITesting;
    }

    public function build()
    {
        $rekomendasiDetail = RekomendasiDetail::with('getBarang', 'getNamaVendor')
            ->where('IdPengajuan', $this->rekomendasi->id)
            ->where('Rekomendasi', 1)
            ->first();

        $vendorAcc = $rekomendasiDetail->getNamaVendor->Nama ?? null;
        $harga = $rekomendasiDetail->HargaNego ?? null;

        $data = [
            'KodePengajuan' => $this->rekomendasi->KodePengajuan,
            'AsalRumahSakit' => $this->rekomendasi->getPerusahaan->NamaLengkap,
            'NamaPermintaan' => $rekomendasiDetail->getBarang->Nama,
            'RencanaPenempatan' => $this->rekomendasi->getPermintaan->getDetail[0]->RencanaPenempatan ?? '-',
            'VendorACC' => $vendorAcc,
            'Harga' => $harga,
            'Approval' => $this->approvalFUITesting,
        ];

        $mailable = $this
            ->subject('Usulan Investasi - ' . $data['AsalRumahSakit'] . ' - ' . $data['NamaPermintaan'] . ' - ' . $data['RencanaPenempatan'])
            ->view('emails.notif-approval-presentasi')
            ->with($data);

        // ==========================================
        // HANYA ATTACH FILE fs-{id}.pdf
        // ==========================================
        $idPengajuan = $this->rekomendasi->id;
        $fsFileName = 'fs-' . $idPengajuan . '.pdf';
        $fsFullPath = storage_path('app/public/rekap-file/pengajuan-' . $idPengajuan . '/' . $fsFileName);

        if (file_exists($fsFullPath) && filesize($fsFullPath) > 0) {
            $mailable->attach($fsFullPath, [
                'as' => $fsFileName,
                'mime' => 'application/pdf',
            ]);
            Log::info('Lampiran FS berhasil di-attach: ' . $fsFullPath);
        } else {
            Log::warning('File FS tidak ditemukan atau kosong: ' . $fsFullPath);
        }

        return $mailable;
    }
}
