<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifMengetahuiDirektur extends Mailable
{
    use Queueable, SerializesModels;

    public $usulan;
    public $VendorAcc;
    public $direktur; // Ini adalah object User (Direktur) dari controller
    public $approval2;
    public $dataRekom;
    public $data2;

    // Parameter disesuaikan persis dengan pemanggilan di Controller
    public function __construct($usulan, $VendorAcc, $direktur, $approval2, $dataRekom = null, $data2 = null)
    {
        $this->usulan = $usulan;
        $this->VendorAcc = $VendorAcc;
        $this->direktur = $direktur;
        $this->approval2 = $approval2;
        $this->dataRekom = $dataRekom;
        $this->data2 = $data2;
    }

    public function build()
    {
        // Ambil data dengan aman untuk Subject Email
        $namaPerusahaan = 'Perusahaan';
        if ($this->dataRekom && isset($this->dataRekom->getRekomedasiDetail[0]->getPerusahaan)) {
            $namaPerusahaan = $this->dataRekom->getRekomedasiDetail[0]->getPerusahaan->NamaLengkap;
        }

        $namaBarang = 'Barang/Jasa';
        if ($this->usulan && isset($this->usulan->getBarang)) {
            $namaBarang = $this->usulan->getBarang->Nama;
        }

        $email = $this
            ->subject('Pemberitahuan & Persetujuan FUI - ' . $namaPerusahaan . ' - ' . $namaBarang)
            ->view('emails.notifikasi-mengetahui-direktur') // Pastikan view ini ada, atau buat view khusus direktur
            ->with([
                'direkturUser' => $this->direktur, // Ganti key jadi "direkturUser"
                'usulan' => $this->usulan,
                'approval2' => $this->approval2,
                'dataRekom' => $this->dataRekom,
                'data2' => $this->data2,
            ]);

        // Ambil IdPengajuan dari data usulan
        $idPengajuan = $this->usulan->IdPengajuan ?? 'unknown';
        $filename = 'fui-' . $idPengajuan . '.pdf';

        // Siapkan path file FUI pada storage
        $fsFullPath = storage_path('app/public/rekap-file/pengajuan-' . $idPengajuan . '/' . $filename);

        if (file_exists($fsFullPath) && filesize($fsFullPath) > 0) {
            $email->attach($fsFullPath, [
                'as' => $filename,
                'mime' => 'application/pdf',
            ]);
            Log::info('Lampiran FUI berhasil di-attach untuk Direktur: ' . $fsFullPath);
        } else {
            $pdfViewPath = 'form-usulan-investasi.cetak-fui-email';

            $pdf = Pdf::loadView($pdfViewPath, [
                'data' => $this->usulan,
                'VendorAcc' => $this->VendorAcc,
                'approval' => $this->approval2,
                'approval2' => $this->approval2,
                'penilai' => $this->direktur,
                'dataRekom' => $this->dataRekom,
                'data2' => $this->data2,
            ])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'chroot' => public_path(),
                ]);

            $email->attachData($pdf->output(), $filename);
            Log::warning('File FUI tidak ditemukan di storage: ' . $fsFullPath . '. Melampirkan FUI yang digenerate on the fly untuk Direktur.');
        }

        return $email;
    }
}
