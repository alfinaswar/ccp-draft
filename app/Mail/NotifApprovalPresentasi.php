<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;  // Make sure you have 'barryvdh/laravel-dompdf' installed to use PDF

class NotifApprovalPresentasi extends Mailable
{
    use Queueable, SerializesModels;

    public $rekomendasi;
    public $disposisi;
    public $fui;
    public $user;
    public $approvalDispo;
    public $approvalFui;
    public $rekomendasiLampiran;
    public $data;
    public $data2;
    public $usulan;
    public $approval;
    public $VendorAcc;
    public $approval2;
    public $permintaan;
    public $approval3;
    public $dataHta;
    public $approvalHta;
    public $parameter;
    public $datafs;
    public $approvalfS;
    public $dataRekom;
    public $valueTesting;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $rekomendasi,
        $disposisi = null,  // Default to null, disposisi tidak wajib
        $fui = null,
        $user,
        $approvalDispo = null,
        $approvalFui = null,
        $rekomendasiLampiran = null,
        $data = null,
        $data2 = null,
        $usulan = null,
        $approval = null,
        $VendorAcc = null,
        $approval2 = null,
        $permintaan = null,
        $approval3 = null,
        $dataHta = null,
        $approvalHta = null,
        $parameter = null,
        $datafs = null,
        $approvalfS = null,
        $dataRekom = null,
        $valueTesting = null,
    ) {
        $this->rekomendasi = $rekomendasi;
        $this->disposisi = $disposisi;
        $this->fui = $fui;
        $this->user = $user;
        $this->approvalDispo = $approvalDispo;
        $this->approvalFui = $approvalFui;
        $this->rekomendasiLampiran = $rekomendasiLampiran;
        $this->data = $data;
        $this->data2 = $data2;
        $this->usulan = $usulan;
        $this->approval = $approval;
        $this->VendorAcc = $VendorAcc;
        $this->approval2 = $approval2;
        $this->permintaan = $permintaan;
        $this->approval3 = $approval3;
        $this->dataHta = $dataHta;
        $this->approvalHta = $approvalHta;
        $this->parameter = $parameter;
        $this->datafs = $datafs;
        $this->approvalfS = $approvalfS;
        $this->dataRekom = $dataRekom;
        $this->valueTesting = $valueTesting;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'nama' => $this->user->name,
            'email' => $this->user->email,
            'tanggalPresentasi' => $this->rekomendasi->TanggalPresentasi,
            'noPengajuan' => $this->rekomendasi->NoPengajuan ?? '-',
            'approvalDispo' => null,
            'approvalFui' => null,
            'hasFui' => false,
            'valueTesting' => $this->valueTesting,
        ];

        // Set approvalDispo only if available (disposisi tidak wajib)
        if ($this->approvalDispo) {
            $data['approvalDispo'] = [
                'token' => $this->approvalDispo->ApprovalToken,
                'dokumenId' => $this->approvalDispo->DokumenId,
                'jenisFormId' => $this->approvalDispo->JenisFormId,
                'namaDokumen' => 'Lembar Disposisi',
            ];
        }

        if ($this->approvalFui) {
            $data['approvalFui'] = [
                'token' => $this->approvalFui->ApprovalToken,
                'dokumenId' => $this->approvalFui->DokumenId,
                'jenisFormId' => $this->approvalFui->JenisFormId,
                'namaDokumen' => 'Form Usulan Investasi',
                'biayaAkhir' => $this->fui ? $this->fui->BiayaAkhir : 0,
            ];
            $data['hasFui'] = true;
        }

        $pdf = PDF::loadView('rekomendasi-pembelian.rekap-pdf', [
            'disposisi' => $this->disposisi,
            'fui' => $this->fui,
            'user' => $this->user,
            'approvalDispo' => $this->approvalDispo,
            'approvalFui' => $this->approvalFui,
            'rekomendasi' => $this->rekomendasiLampiran,
            'data' => $this->data,
            'data2' => $this->data2,
            'usulan' => $this->usulan,
            'approval' => $this->approval,
            'VendorAcc' => $this->VendorAcc,
            'approval2' => $this->approval2,
            'permintaan' => $this->permintaan,
            'approval3' => $this->approval3,
            'dataHta' => $this->dataHta,
            'approvalHta' => $this->approvalHta,
            'parameter' => $this->parameter,
            'datafs' => $this->datafs,
            'approvalfS' => $this->approvalfS,
            'dataRekom' => $this->dataRekom,
            'valueTesting' => $this->valueTesting,
        ])->setOptions([
            'isRemoteEnabled' => true,
        ]);

        return $this
            ->subject('Persetujuan UsulanInvestasi')
            ->view('emails.notif-approval-presentasi')
            ->with($data)
            ->attachData($pdf->output(), 'Lampiran-Presentasi.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
