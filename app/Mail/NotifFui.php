<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifFui extends Mailable
{
    use Queueable, SerializesModels;

    public $cetakUsulan;
    public $VendorAcc;
    public $penilai;
    public $approval2;
    public $dataRekom;
    public $data2;
    public $cekjenis;

    // Tambahkan $dataRekom ke constructor
    public function __construct($cetakUsulan, $VendorAcc, $penilai, $approval2, $dataRekom = null, $data2 = null, $cekjenis = null)
    {
        $this->cetakUsulan = $cetakUsulan;
        $this->VendorAcc = $VendorAcc;
        $this->penilai = $penilai;  // Data spesifik penilai (dengan QR code)
        $this->approval2 = $approval2;  // Full collection untuk PDF
        $this->dataRekom = $dataRekom;  // Data rekomendasi
        $this->data2 = $data2;  // Data rekomendasi
        $this->cekjenis = $cekjenis;  // Data rekomendasi
    }

    public function build()
    {
        // dd($this->cetakUsulan);
        $email = $this
            ->subject('Form Usulan Investasi - ' . $this->dataRekom->getRekomedasiDetail[0]->getPerusahaan->NamaLengkap . ' - ' . $this->cetakUsulan->getBarang->Nama)
            ->view('emails.notifikasi-pengajuan-fui')
            ->with([
                'penilai' => $this->penilai,
                'cekjenis' => $this->cekjenis,
            ]);

        $pdf = Pdf::loadView('form-usulan-investari.cetak-fui-email', [
            'data' => $this->cetakUsulan,
            'VendorAcc' => $this->VendorAcc,
            'approval' => $this->penilai,
            'approval2' => $this->approval2,
            'penilai' => $this->penilai,
            'dataRekom' => $this->dataRekom,
            'data2' => $this->data2,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);

        $email->attachData($pdf->output(), 'Form Usulan Investasi.pdf');

        return $email;
    }
}
