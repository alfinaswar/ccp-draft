<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiPermintaanPembelian extends Mailable
{
    use Queueable, SerializesModels;

    public $permintaan;
    public $approval;

    public function __construct($permintaan, $approval)
    {
        $this->permintaan = $permintaan;
        $this->approval = $approval;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Permintaan Pembelian - {$this->permintaan->getPerusahaan->NamaLengkap} - {$this->permintaan->getDetail[0]->getBarang->Nama}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notifikasi-permintaan-pembelian',
            with: [
                'permintaan' => $this->permintaan,
                'penilai' => $this->approval,
            ]
        );
    }

    public function attachments(): array
    {
        // Lampirkan PDF permintaan pembelian jika file tersedia
        $lampiran = [];

        // Path file PDF sesuai logic pada PermintaanPembelianController@savePdfToStorage
        $pdfFileName = 'permintaan_' . $this->permintaan->id . '.pdf';
        $storagePath = storage_path('app/public/rekap-file/permintaan/' . $pdfFileName);

        if (file_exists($storagePath)) {
            $lampiran[] = [
                'file' => $storagePath,      // Full path file
                'options' => [
                    'as' => $pdfFileName,   // Nama file attachment saat dikirim
                    'mime' => 'application/pdf'
                ]
            ];
        }

        // Laravel expects the array of attachments as objects or string paths (for 10.x/9.x)
        // So we transform our array for compatibility with Mailable::attach()
        return array_map(function ($item) {
            return \Illuminate\Mail\Mailables\Attachment::fromPath($item['file'])
                ->as($item['options']['as'])
                ->withMime($item['options']['mime']);
        }, $lampiran);
    }
}
