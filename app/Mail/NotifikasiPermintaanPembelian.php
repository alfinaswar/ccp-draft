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
        // dd($this->permintaan->getDetail[0]->getBarang->Nama);
        return new Envelope(
            subject: "Permintaan Pembelian - {$this->permintaan->getPerusahaan->NamaLengkap} - {$this->permintaan->getDetail[0]->getBarang->Nama}"
        );
    }

    public function content(): Content
    {
        // dd($this->approval);
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
        return [];
    }
}
