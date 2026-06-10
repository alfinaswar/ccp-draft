<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NotifikasiPengajuanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $hta;
    public $parameter;
    public $penilai;
    public $approval2;
    public $fileLampiran;  // opsional
    public $permintaan;
    public $ApprovalPermintaan;
    public function __construct($pengajuan, $hta, $parameter, $penilai, $approval2, $fileLampiran = null, $ApprovalPermintaan = null, $permintaan = null)
    {
        $this->pengajuan = $pengajuan;
        $this->hta = $hta;
        $this->parameter = $parameter;
        $this->penilai = $penilai;
        $this->approval2 = $approval2;
        $this->fileLampiran = $fileLampiran;
        $this->ApprovalPermintaan = $ApprovalPermintaan;
        $this->permintaan = $permintaan;
    }

    public function build()
    {
        if ($this->hta->JenisForm == '1') {
            $form = 'HTA';
        } else {
            $form = 'GPA';
        }
        $subjectRencana = $this->permintaan->getDetail[0]->RencanaPenempatan ?? '-';

        $email = $this
            ->subject($form . ' - ' . $this->pengajuan->getPerusahaan->NamaLengkap . ' - ' . $this->pengajuan->getPengajuanItem[0]->getBarang->Nama . ' - ' . $subjectRencana)
            ->view('emails.notifikasi-pengajuan-hta')
            ->with([
                'penilai' => $this->penilai,
                'pengajuan' => $this->pengajuan,
                'form' => $form,
            ]);

        if (
            ($this->hta->JenisForm == '2' || $this->hta->JenisForm == '16') &&
            $this->fileLampiran &&
            is_array($this->fileLampiran)
        ) {
            foreach ($this->fileLampiran as $file) {
                if (!empty($file)) {
                    $email->attach(storage_path('app/public/upload/gpa/' . $file));
                }
            }
        } else {
            $pdf = Pdf::loadView('hta-gpa.cetak-hta-gpa-email', [
                'data' => $this->pengajuan,
                'hta' => $this->hta,
                'parameter' => $this->parameter,
                'penilai' => $this->penilai,
                'approval2' => $this->approval2,
            ])->setPaper('a4', 'landscape');

            $email->attachData($pdf->output(), 'HTA_GPA.pdf');
        }

        return $email;
    }
}
