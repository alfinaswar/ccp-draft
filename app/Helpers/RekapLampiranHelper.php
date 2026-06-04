<?php

namespace App\Helpers;

use App\Models\PengajuanPembelian;
use App\Models\LembarDisposisi;
use App\Models\UsulanInvestasi;
use App\Models\FeasibilityStudy;
use App\Models\Rekomendasi;
use App\Models\PermintaanPembelian;
use App\Models\DokumenApproval;
use App\Models\MasterParameter;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode as EndroidQrCode;

class RekapLampiranHelper
{
    /**
     * Generate PDF rekap lampiran berdasarkan ID atau KodePengajuan
     *
     * @param int|string $identifier ID Pengajuan atau KodePengajuan
     * @param int|null $pengajuanItemId Optional: filter per item
     * @param string $type 'id' atau 'kode'
     * @return \Illuminate\Http\Response|string PDF output
     */
    public static function generate($identifier, $pengajuanItemId = null, $type = 'id')
    {
        // Ambil data utama PengajuanPembelian
        $pengajuan = $type === 'kode'
            ? PengajuanPembelian::where('KodePengajuan', $identifier)->first()
            : PengajuanPembelian::find($identifier);

        if (!$pengajuan) {
            throw new \Exception("Data pengajuan tidak ditemukan");
        }

        $idPengajuan = $pengajuan->id;
        $itemId = $pengajuanItemId ?? $pengajuan->PengajuanItemId;

        // ========== 1. DISPOSISI ==========
        $disposisi = LembarDisposisi::with(['getDetail', 'getBarang', 'getVendor'])
            ->where('IdPengajuan', $idPengajuan)
            ->when($itemId, fn($q) => $q->where('PengajuanItemId', $itemId))
            ->first();

        $approvalDispo = collect();
        if ($disposisi) {
            $approvalDispo = self::getApprovalWithQr($disposisi->JenisForm, $disposisi->id);
        }

        // ========== 2. FUI (Usulan Investasi) ==========
        $fui = UsulanInvestasi::with(['getFuiDetail.getVendor', 'getBarang', 'getVendor'])
            ->where('IdPengajuan', $idPengajuan)
            ->when($itemId, fn($q) => $q->where('PengajuanItemId', $itemId))
            ->first();

        $approvalFui = collect();
        $jenisFormFui = null;

        if ($fui && filled($fui->BiayaAkhir)) {
            $jenisFormFui = self::getJenisFormFui($pengajuan->Jenis, $fui->BiayaAkhir);
            $approvalFui = self::getApprovalWithQr($jenisFormFui, $fui->id);

            // Fallback ke JenisForm lama jika tidak ditemukan
            if ($approvalFui->isEmpty() && filled($fui->JenisForm) && $fui->JenisForm != $jenisFormFui) {
                $approvalFui = self::getApprovalWithQr($fui->JenisForm, $fui->id);
            }
        }

        // ========== 3. HTA/GPA ==========
        $dataHta = null;
        $approvalHta = collect();

        if ($pengajuan->getHtaGpa) {
            $dataHta = PengajuanPembelian::with([
                'getHtaGpa.getDetailHta' => fn($q) => $itemId && $q->where('PengajuanItemId', $itemId),
                'getHtaGpa.getPenilai1',
                'getHtaGpa.getPenilai2',
                'getHtaGpa.getPenilai3',
                'getHtaGpa.getPenilai4',
                'getHtaGpa.getPenilai5',
                'getHtaGpa.getPenilai',
                'getPengajuanItem' => fn($q) => $itemId ? $q->where('id', $itemId)->with('getBarang.getMerk') : $q,
            ])->find($idPengajuan);

            if ($dataHta?->getHtaGpa) {
                $approvalHta = self::getApprovalWithQr($dataHta->getHtaGpa->JenisForm, $dataHta->getHtaGpa->id);
            }
        }

        // ========== 4. FEASIBILITY STUDY ==========
        $datafs = FeasibilityStudy::with(['getFsDetail', 'getBarang'])
            ->where('IdPengajuan', $idPengajuan)
            ->when($itemId, fn($q) => $q->where('PengajuanItemId', $itemId))
            ->first();

        $approvalfS = collect();
        if ($datafs) {
            $approvalfS = self::getApprovalWithQr($datafs->JenisForm, $datafs->id);
        }

        // ========== 5. REKOMENDASI ==========
        $rekomendasi = Rekomendasi::with([
            'getRekomedasiDetail.getPerusahaan',
            'getRekomedasiDetail.getBarang',
            'getRekomedasiDetail.getNegara',
            'getRekomedasiDetail.getNamaVendor'
        ])->when(
                $itemId,
                fn($q) => $q->where('PengajuanItemId', $itemId),
                fn($q) => $q->where('IdPengajuan', $idPengajuan)
            )->first();

        // Generate QR Code untuk rekomendasi jika ada
        if ($rekomendasi) {
            if (filled($rekomendasi->UserNego)) {
                $rekomendasi->qrCodeNego = self::generateQrBase64($rekomendasi->id);
            }
            if (filled($rekomendasi->DisetujuiOleh)) {
                $rekomendasi->qrCodeApprove = self::generateQrBase64($rekomendasi->id);
            }
        }

        // ========== 6. PERMINTAAN PEMBELIAN ==========
        $permintaan = null;
        $approval3 = collect();

        if ($pengajuan->IdPermintaan) {
            $permintaan = PermintaanPembelian::with([
                'getDetail.getBarang.getMerk',
                'getDiajukanOleh',
                'getDetail.getBarang.getSatuan'
            ])->find($pengajuan->IdPermintaan);

            if ($permintaan) {
                $approval3 = self::getApprovalWithQr($permintaan->JenisForm, $permintaan->id, 80);
            }
        }

        // ========== 7. VENDOR ACC (untuk data2) ==========
        $VendorAcc = null;
        $data2 = null;

        if ($rekomendasi) {
            $VendorAcc = Rekomendasi::with([
                'getRekomedasiDetail' => fn($q) => $q->where('Rekomendasi', 1),
                'getRekomedasiDetail.getNamaVendor'
            ])->when(
                    $itemId,
                    fn($q) => $q->where('PengajuanItemId', $itemId),
                    fn($q) => $q->where('IdPengajuan', $idPengajuan)
                )->first();

            if ($VendorAcc?->getRekomedasiDetail?->isNotEmpty()) {
                $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor;
                $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan;

                $data2 = PengajuanPembelian::with([
                    'getVendor' => fn($q) => $q->where('NamaVendor', $Acc),
                    'getVendor.getVendorDetail' => fn($q) => $q->where('NamaBarang', $NamaBarangAcc),
                    'getRekomendasi' => fn($q) => $q->with([
                        'getRekomedasiDetail' => fn($q2) => $q2->where('Rekomendasi', 1)
                    ])
                ])->find($idPengajuan);
            }
        }

        // ========== 8. DATA DISPOSISI UNTUK VIEW ==========
        $dataDispo = null;
        if ($disposisi) {
            $dataDispo = [
                'lembarDisposisi' => $disposisi,
                'namaBarang' => $disposisi->getBarang->Nama ?? '-',
                'harga' => $disposisi->Harga,
                'rencanaVendor' => $disposisi->getVendor->Nama ?? '-',
                'tujuanPenempatan' => $disposisi->TujuanPenempatan,
                'formPermintaan' => $disposisi->FormPermintaanUser,
                'approval' => $approvalDispo,
            ];
        }

        // ========== 9. APPROVAL FUI (jika ada) ==========
        $approval2 = collect();
        if ($fui && filled($fui->JenisForm)) {
            $approval2 = self::getApprovalWithQr($fui->JenisForm, $fui->id);
        }

        // ========== 10. MASTER PARAMETER ==========
        $parameter = MasterParameter::get();

        // ========== 11. DATA REKOM DETAIL ==========
        $dataRekom = null;
        if ($fui) {
            $dataRekom = Rekomendasi::with([
                'getRekomedasiDetail.getBarang',
                'getRekomedasiDetail.getNamaVendor'
            ])->where('IdPengajuan', $fui->IdPengajuan)->first();
        }

        // ========== GENERATE PDF ==========
        $pdf = PDF::loadView('rekomendasi-pembelian.rekap-pdf', compact(
            'disposisi',
            'fui',
            'dataHta',
            'datafs',
            'rekomendasi',
            'permintaan',
            'VendorAcc',
            'data2',
            'dataDispo',
            'approvalDispo',
            'approvalFui',
            'approvalHta',
            'approvalfS',
            'approval3',
            'approval2',
            'parameter',
            'dataRekom',
            'pengajuan'
        ))->setOptions(['isRemoteEnabled' => true]);

        return $pdf;
    }

    /**
     * Helper: Ambil approval dengan QR Code
     */
    protected static function getApprovalWithQr($jenisFormId, $dokumenId, $qrSize = 300)
    {
        $approval = DokumenApproval::with(['getUser', 'getJabatan', 'getDepartemen'])
            ->where('JenisFormId', $jenisFormId)
            ->where('DokumenId', $dokumenId)
            ->orderBy('Urutan', 'asc')
            ->get();

        foreach ($approval as $item) {
            if ($item->Status === 'Approved' && filled($item->ApprovalToken)) {
                $item->qrCode = self::generateQrBase64(
                    route('approval.validasi', $item->ApprovalToken),
                    $qrSize
                );
            }
        }

        return $approval;
    }

    /**
     * Helper: Generate QR Code ke Base64
     */
    protected static function generateQrBase64($content, $size = 300, $margin = 10)
    {
        try {
            $qrCode = EndroidQrCode::create($content ?? '')
                ->setSize($size)
                ->setMargin($margin);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            return base64_encode($result->getString());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper: Tentukan JenisForm FUI berdasarkan nominal
     */
    protected static function getJenisFormFui($jenisPengajuan, $biayaAkhir)
    {
        $biaya = floatval(str_replace(['.', ','], ['', '.'], $biayaAkhir));

        if ($jenisPengajuan == '1') { // Medis
            if ($biaya < 50000000)
                return '7';
            if ($biaya <= 100000000)
                return '11';
            return '12';
        } else { // Umum
            if ($biaya < 50000000)
                return '14';
            if ($biaya <= 100000000)
                return '15';
            return '13';
        }
    }

    /**
     * Helper: Check apakah dokumen tersedia
     */
    public static function checkAvailableDocs($identifier, $type = 'id')
    {
        $pengajuan = $type === 'kode'
            ? PengajuanPembelian::where('KodePengajuan', $identifier)->first()
            : PengajuanPembelian::find($identifier);

        if (!$pengajuan)
            return [];

        $docs = [];
        $itemId = $pengajuan->PengajuanItemId;

        // Disposisi
        if (LembarDisposisi::where('IdPengajuan', $pengajuan->id)->where('PengajuanItemId', $itemId)->exists()) {
            $docs['disposisi'] = true;
        }

        // FUI
        if (
            UsulanInvestasi::where('IdPengajuan', $pengajuan->id)
                ->where('PengajuanItemId', $itemId)
                ->whereNotNull('BiayaAkhir')
                ->exists()
        ) {
            $docs['fui'] = true;
        }

        // HTA
        if ($pengajuan->getHtaGpa) {
            $docs['hta'] = true;
        }

        // FS
        if (
            FeasibilityStudy::where('IdPengajuan', $pengajuan->id)
                ->where('PengajuanItemId', $itemId)
                ->exists()
        ) {
            $docs['fs'] = true;
        }

        return $docs;
    }
}
