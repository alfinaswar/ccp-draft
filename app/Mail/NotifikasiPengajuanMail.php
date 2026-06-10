<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;

class NotifikasiPengajuanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $hta;
    public $parameter;
    public $penilai;
    public $approval2;
    public $fileLampiran;
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

        // ==========================================
        // BLOK IF: UNTUK FILE LAMPIRAN GPA (JenisForm 2 atau 16)
        // ==========================================
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
        }
        // ==========================================
        // BLOK ELSE: GABUNGKAN PDF PERMINTAAN + HTA-GPA
        // ==========================================
        else {
            // 1. Generate PDF HTA-GPA
            $pdf = Pdf::loadView('hta-gpa.cetak-hta-gpa-email', [
                'data' => $this->pengajuan,
                'hta' => $this->hta,
                'parameter' => $this->parameter,
                'penilai' => $this->penilai,
                'approval2' => $this->approval2,
            ])->setPaper('a4', 'landscape');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            $htaGpaOutput = $pdf->output();

            // 2. Simpan PDF HTA-GPA ke storage
            $idPengajuan = $this->pengajuan->id;
            $htaGpaFileName = 'hta-gpa-' . $idPengajuan . '.pdf';
            $htaGpaDirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
            $htaGpaStoragePath = $htaGpaDirPath . '/' . $htaGpaFileName;
            $htaGpaFullDirPath = storage_path('app/' . $htaGpaDirPath);

            if (!file_exists($htaGpaFullDirPath)) {
                mkdir($htaGpaFullDirPath, 0777, true);
            }

            Storage::put($htaGpaStoragePath, $htaGpaOutput);
            $htaGpaFullPath = storage_path('app/' . $htaGpaStoragePath);

            // 3. Cek apakah PDF Permintaan sudah ada
            $idPermintaan = $this->pengajuan->IdPermintaan ?? null;
            $permintaanFullPath = null;

            if ($idPermintaan) {
                $permintaanFileName = 'permintaan_' . $idPermintaan . '.pdf';
                $permintaanFullPath = storage_path('app/public/rekap-file/permintaan/' . $permintaanFileName);

                if (!file_exists($permintaanFullPath)) {
                    $permintaanFullPath = null;
                }
            }

            // 4. Gabungkan PDF (jika PDF Permintaan ada)
            if ($permintaanFullPath) {
                try {
                    $combinedPdf = new Fpdi();

                    // A. Tambahkan halaman dari PDF Permintaan
                    $pageCount = $combinedPdf->setSourceFile($permintaanFullPath);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $tplIdx = $combinedPdf->importPage($i);
                        $size = $combinedPdf->getTemplateSize($tplIdx);
                        $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $combinedPdf->useTemplate($tplIdx);
                    }

                    // B. Tambahkan halaman dari PDF HTA-GPA
                    $pageCount = $combinedPdf->setSourceFile($htaGpaFullPath);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $tplIdx = $combinedPdf->importPage($i);
                        $size = $combinedPdf->getTemplateSize($tplIdx);
                        $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $combinedPdf->useTemplate($tplIdx);
                    }

                    // 5. Output hasil gabungan ke buffer (hindari error path TCPDF)
                    $combinedContent = $combinedPdf->Output('', 'S');

                    // 6. Simpan hasil gabungan
                    $combinedFileName = 'HTA_GPA_' . $idPengajuan . '.pdf';
                    $combinedStoragePath = $htaGpaDirPath . '/' . $combinedFileName;
                    Storage::put($combinedStoragePath, $combinedContent);

                    // 7. Attach hasil gabungan ke email
                    $email->attachData($combinedContent, $combinedFileName, [
                        'mime' => 'application/pdf',
                    ]);
                } catch (\Exception $e) {
                    // Fallback: jika gagal combine, attach PDF HTA-GPA saja
                    \Log::error('Error combining PDF: ' . $e->getMessage());
                    $email->attachData($htaGpaOutput, 'HTA_GPA.pdf', [
                        'mime' => 'application/pdf',
                    ]);
                }
            } else {
                // Jika PDF Permintaan tidak ada, attach PDF HTA-GPA saja
                $email->attachData($htaGpaOutput, 'HTA_GPA.pdf', [
                    'mime' => 'application/pdf',
                ]);
            }
        }

        return $email;
    }
}
