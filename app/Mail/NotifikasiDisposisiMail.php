<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiDisposisiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $disposisi;
    public $approval;
    public $approvalDispo;
    public $MasterVendor;
    public $MasterBarang;
    public $rekomendasi;
    public $cekjenis;

    public function __construct($disposisi, $approvalDispo, $approval, $MasterVendor, $MasterBarang, $rekomendasi, $cekjenis)
    {
        $this->disposisi = $disposisi;
        $this->approvalDispo = $approvalDispo;
        $this->approval = $approval;
        $this->MasterVendor = $MasterVendor;
        $this->MasterBarang = $MasterBarang;
        $this->rekomendasi = $rekomendasi;
        $this->cekjenis = $cekjenis;
    }

    public function build()
    {
        // dd($this->MasterBarang);
        $email = $this
            ->subject('Persetujuan Lembar Disposisi - ' . $this->rekomendasi->getRekomedasiDetail[0]->getPerusahaan->NamaLengkap . ' - ' . $this->MasterBarang->Nama)
            ->view('emails.notifikasi-disposisi')
            ->with([
                'disposisi' => $this->disposisi,
                'approvalDispo' => $this->approvalDispo,
                'approval' => $this->approval,
                'MasterVendor' => $this->MasterVendor,
                'MasterBarang' => $this->MasterBarang,
                'cekjenis' => $this->cekjenis,
            ]);

        $pdf = Pdf::loadView('lembar-disposisi.cetak-disposisi-email', [
            'disposisi' => $this->disposisi,
            'approvalDispo' => $this->approvalDispo,
            'approval' => $this->approval,
            'MasterVendor' => $this->MasterVendor,
            'MasterBarang' => $this->MasterBarang,
            'rekomendasi' => $this->rekomendasi,

        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);

        $email->attachData($pdf->output(), 'Lembar_Disposisi.pdf');
        return $email;
    }
}
