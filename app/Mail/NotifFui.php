<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

    public function __construct($cetakUsulan, $VendorAcc, $penilai, $approval2, $dataRekom = null, $data2 = null, $cekjenis = null)
    {
        $this->cetakUsulan = $cetakUsulan;
        $this->VendorAcc = $VendorAcc;
        $this->penilai = $penilai;
        $this->approval2 = $approval2;
        $this->dataRekom = $dataRekom;
        $this->data2 = $data2;
        $this->cekjenis = $cekjenis;
    }

    public function build()
    {
        $email = $this
            ->subject('Form Usulan Investasi - ' . $this->dataRekom->getRekomedasiDetail[0]->getPerusahaan->NamaLengkap . ' - ' . $this->cetakUsulan->getBarang->Nama)
            ->view('emails.notifikasi-pengajuan-fui')
            ->with([
                'penilai' => $this->penilai,
                'cekjenis' => $this->cekjenis,
            ]);

        // Ambil IdPengajuan dari data usulan
        $idPengajuan = $this->cetakUsulan->IdPengajuan ?? null;
        $filename = 'fui-' . $idPengajuan . '.pdf';

        // Siapkan path file FUI pada storage (rekap-file)
        $fsFullPath = storage_path('app/public/rekap-file/pengajuan-' . $idPengajuan . '/' . $filename);

        if (file_exists($fsFullPath) && filesize($fsFullPath) > 0) {
            $email->attach($fsFullPath, [
                'as' => $filename,
                'mime' => 'application/pdf',
            ]);
            Log::info('Lampiran FUI berhasil di-attach: ' . $fsFullPath);
        } else {
            // Fallback ke generate PDF on the fly jika file tidak ditemukan/kosong
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
            $email->attachData($pdf->output(), $filename);
            Log::warning('File FUI tidak ditemukan atau kosong: ' . $fsFullPath . '. Melampirkan FUI yang digenerate on the fly.');
        }

        return $email;
    }
}
