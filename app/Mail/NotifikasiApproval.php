<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiApproval extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $nextApproval;

    public function __construct($data, $nextApproval)
    {
        $this->data = $data;
        $this->nextApproval = $nextApproval;
    }

    public function build()
    {
        return $this
            ->subject(
                'Permintaan Pembelian - '
                . ($this->data->getPerusahaan->NamaLengkap ?? '-') . ' - '
                . (isset($this->data->getDetail[0]->getBarang->Nama) ? $this->data->getDetail[0]->getBarang->Nama : '-') . ' - '
                . ($this->data->getDetail[0]->RencanaPenempatan ?? '-')
            )
            ->view('emails.approval-berikutnya')
            ->with([
                'data' => $this->data,
                'nextApproval' => $this->nextApproval,
            ]);
    }
}
