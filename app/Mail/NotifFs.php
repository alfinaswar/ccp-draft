<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
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
        $email = $this
            ->subject('Persetujuan FS - ' . $this->data222->getPerusahaan->NamaLengkap . ' - ' . $this->data222->getBarang->Nama)
            ->view('emails.notifikasi-pengajuan-fs')
            ->with([
                'penilai' => $this->penilai,
                'Pengajuan' => $this->Pengajuan,
            ]);

        // belum pasti
        // $pdf = Pdf::loadView('feasibility-study.lampiran-email', [
        //     'data' => $this->data222,
        //     'approval' => $this->penilai,
        //     'approval2' => $this->approval2,
        //     'penilai' => $this->penilai,
        // ])
        //     ->setPaper('A4', 'portrait')
        //     ->setOptions([
        //         'isRemoteEnabled' => true,
        //         'chroot' => public_path(),
        //     ]);

        // $email->attachData($pdf->output(), 'Form Feasibility Study.pdf');


        return $email;
    }
}
