<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifFs extends Mailable
{
    use Queueable, SerializesModels;

    public $feasibilityStudy;
    public $penilai;
    public $approval2;
    public $data222;
    public $Pengajuan;

    public function __construct($feasibilityStudy, $penilai, $approval2, $data222, $Pengajuan)
    {
        $this->feasibilityStudy = $feasibilityStudy;
        $this->penilai = $penilai;
        $this->approval2 = $approval2;
        $this->data222 = $data222;
        $this->Pengajuan = $Pengajuan;
    }

    public function build()
    {
        $HargaRekom = 0;
        $HargaRekom = $this->Pengajuan->getRekomendasi[0]->getRekomedasiDetail()->where('Rekomendasi', '1')->first();

        $email = $this
            ->subject(
                'FS - '
                . ($this->data222->getPerusahaan->NamaLengkap ?? '-')
                . ' - '
                . ($this->data222->getBarang->Nama ?? '-')
                . ' - '
                . ($this->Pengajuan->getPermintaan->getDetail[0]->RencanaPenempatan ?? '-')
            )
            ->view('emails.notifikasi-pengajuan-fs')
            ->with([
                'penilai' => $this->penilai,
                'Pengajuan' => $this->Pengajuan,
                'HargaRekom' => $HargaRekom,
            ]);

        // Lampirkan file pdf hasil FS (combine)
        $idPengajuan = $this->data222->IdPengajuan ?? $this->Pengajuan->id;
        // dd($idPengajuan);
        $pdfPath = storage_path('app/public/rekap-file/pengajuan-' . $idPengajuan . '/fs-' . $idPengajuan . '.pdf');
        if (file_exists($pdfPath)) {
            $email->attach($pdfPath, [
                'as' => 'Form Feasibility Study - Pengajuan ' . $idPengajuan . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
