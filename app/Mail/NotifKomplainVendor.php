<?php

namespace App\Mail;

use App\Models\DokumenApproval;
use App\Models\KomplainVendor;
use App\Models\MasterParameterKomplain;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class NotifKomplainVendor extends Mailable
{
    use Queueable, SerializesModels;

    public KomplainVendor $komplain;
    public DokumenApproval $targetApproval;
    public Collection $daftarApproval;

    public function __construct(
        KomplainVendor $komplain,
        DokumenApproval $targetApproval,
        Collection $daftarApproval
    ) {
        $this->komplain = $komplain;
        $this->targetApproval = $targetApproval;
        $this->daftarApproval = $daftarApproval;
    }

    public function build()
    {
        $subjek = 'Form Komplain Vendor - ' . $this->komplain->KodeKomplain . ' - ' . $this->komplain->NamaRS . ' - ' . $this->komplain->NamaBarang;

        // Siapkan QR Code untuk setiap tahap approval yang sudah Approved
        $writer = new PngWriter();
        foreach ($this->daftarApproval as $item) {
            if ($item->Status === 'Approved' && $item->ApprovalToken && empty($item->qrCode)) {
                $qr = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(220)
                    ->setMargin(8);
                $item->qrCode = base64_encode($writer->write($qr)->getString());
            }
        }

        $masterParams = MasterParameterKomplain::daftar('ParameterEvaluasi');
        $parameterEvaluasi = array_values(array_unique(array_merge(
            $masterParams,
            (array) ($this->komplain->ParameterEvaluasi ?? [])
        )));

        $pdf = Pdf::loadView('komplain.pdf', [
            'komplain' => $this->komplain,
            'approval' => $this->daftarApproval,
            'parameterEvaluasi' => $parameterEvaluasi,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);

        $email = $this->subject($subjek)
            ->view('emails.notifikasi-komplain-vendor')
            ->with([
                'komplain' => $this->komplain,
                'targetApproval' => $this->targetApproval,
            ])
            ->attachData($pdf->output(), 'Laporan-Komplain-' . $this->komplain->KodeKomplain . '.pdf', [
                'mime' => 'application/pdf',
            ]);

        // Lampirkan seluruh berkas pendukung yang diunggah pelapor
        foreach ((array) ($this->komplain->Lampiran ?? []) as $file) {
            $path = $file['path'] ?? null;
            if (!$path || !Storage::disk('public')->exists($path)) {
                continue;
            }

            $fullPath = Storage::disk('public')->path($path);
            if (!is_file($fullPath)) {
                continue;
            }

            $options = [];
            if (!empty($file['nama'])) {
                $options['as'] = $file['nama'];
            }
            if (!empty($file['tipe'])) {
                $options['mime'] = $file['tipe'];
            }

            $email->attach($fullPath, $options);
        }

        return $email;
    }
}
