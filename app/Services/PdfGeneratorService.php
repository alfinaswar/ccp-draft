<?php

namespace App\Services;

use App\Models\DokumenApproval;
use App\Models\FeasibilityStudy;
use App\Models\MasterParameter;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use App\Models\PermintaanPembelian;
use App\Models\Rekomendasi;
use App\Models\UsulanInvestasi;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfGeneratorService
{
    /**
     * Generate SEMUA dokumen PDF untuk 1 pengajuan
     */
    public function generateAll($idPengajuan)
    {
        // dd($idPengajuan);
        $results = [];

        try {
            // 1. Generate Permintaan
            $pengajuan = PengajuanPembelian::find($idPengajuan);
            if ($pengajuan && $pengajuan->IdPermintaan) {
                $results['permintaan'] = $this->generatePermintaan(encrypt($pengajuan->IdPermintaan));
            }

            // 2. Generate HTA-GPA
            $results['hta_gpa'] = $this->generateHtaGpa($idPengajuan);

            // 3. Generate Rekomendasi
            $results['rekomendasi'] = $this->generateRekomendasi($idPengajuan);

            // 4. Generate FS (gabungan rekomendasi + fs + hta-gpa)
            $results['fs'] = $this->generateFs($idPengajuan);

            // 5. Generate FUI (gabungan fs + fui)
            $results['fui'] = $this->generateFui($idPengajuan);

            return $results;
        } catch (\Exception $e) {
            Log::error('Error generateAll PDF: ' . $e->getMessage());
            return $results;
        }
    }

    /**
     * Hapus semua file PDF di folder pengajuan
     */
    public function deleteAll($idPengajuan)
    {
        $folderPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
        if (Storage::disk('local')->exists($folderPath)) {
            Storage::disk('local')->deleteDirectory($folderPath);
            Log::info('Folder PDF dihapus: ' . $folderPath);
        }
    }

    // ==========================================
    // 1. GENERATE PERMINTAAN
    // ==========================================
    public function generatePermintaan($encryptedId)
    {
        try {
            $decryptedId = decrypt($encryptedId);

            $data = PermintaanPembelian::with([
                'getDetail.getBarang.getMerk',
                'getDiajukanOleh',
                'getDetail.getBarang.getSatuan'
            ])->find($decryptedId);

            if (!$data) {
                return null;
            }

            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $data->JenisForm)
                ->where('DokumenId', $data->id)
                ->orderBy('Urutan', 'asc')
                ->get();

            $this->generateQrCodes($approval);

            $pdf = \PDF::loadView('form.permintaan-pembelian.cetak-permintaan', compact('data', 'approval'));
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            $pdfFileName = 'permintaan_' . $decryptedId . '.pdf';
            $storagePath = 'public/rekap-file/permintaan/' . $pdfFileName;

            $dirPath = storage_path('app/public/rekap-file/permintaan/');
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            if (Storage::disk('public')->exists('rekap-file/permintaan/' . $pdfFileName)) {
                Storage::disk('public')->delete('rekap-file/permintaan/' . $pdfFileName);
            }

            Storage::put($storagePath, $pdf->output());

            return 'storage/rekap-file/permintaan/' . $pdfFileName;
        } catch (\Exception $e) {
            Log::error('Error generate Permintaan: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // 2. GENERATE HTA-GPA (gabungkan dengan Permintaan)
    // ==========================================

    public function generateHtaGpa($idPengajuan)
    {
        try {
            $pengajuan = PengajuanPembelian::with([
                'getVendor.getVendorDetail',
                'getHtaGpa',
                'getVendor.getHtaGpa',
                'getJenisPermintaan.getForm',
                'getHtaGpa.getPenilai1',
                'getHtaGpa.getPenilai2',
                'getHtaGpa.getPenilai3',
                'getHtaGpa.getPenilai4',
                'getHtaGpa.getPenilai5',
                'getHtaGpa.getPenilai',
                'getPengajuanItem.getBarang.getMerk',
                'getPermintaan.getDetail'
            ])->find($idPengajuan);

            if (!$pengajuan || !$pengajuan->getHtaGpa) {
                return null;
            }

            $idPengajuanItem = $pengajuan->getHtaGpa->PengajuanItemId;

            $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $pengajuan->getHtaGpa->JenisForm)
                ->where('DokumenId', $pengajuan->getHtaGpa->id)
                ->orderBy('Urutan', 'asc')
                ->get();

            $this->generateQrCodes($approval2);

            // ==========================================
            // CEK JENIS: 1 = HTA (lengkap), != 1 = GPA (sederhana)
            // ==========================================
            if ($pengajuan->Jenis == 1) {
                // HTA - View lengkap dengan tabel penilaian
                $parameter = MasterParameter::get();
                $pdf = \PDF::loadView('hta-gpa.cetak-hta-gpa', [
                    'data' => $pengajuan,
                    'parameter' => $parameter,
                    'approval2' => $approval2
                ])->setPaper('a4', 'landscape');
            } else {
                // GPA - View sederhana (hanya info + TTD + justifikasi)
                $pdf = \PDF::loadView('hta-gpa.umum.cetak-gpa-simple', [
                    'data' => $pengajuan,
                    'approval2' => $approval2
                ])->setPaper('a4', 'portrait');
            }

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            $htaGpaOutput = $pdf->output();

            $dirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
            $fullDirPath = storage_path('app/' . $dirPath);
            if (!file_exists($fullDirPath)) {
                mkdir($fullDirPath, 0777, true);
            }

            $htaGpaTempPath = $fullDirPath . '/hta-gpa-temp-' . $idPengajuan . '.pdf';
            file_put_contents($htaGpaTempPath, $htaGpaOutput);

            // Cek PDF Permintaan
            $idPermintaan = $pengajuan->IdPermintaan ?? null;
            $permintaanFullPath = null;

            if ($idPermintaan) {
                $permintaanFileName = 'permintaan_' . $idPermintaan . '.pdf';
                $permintaanFullPath = storage_path('app/public/rekap-file/permintaan/' . $permintaanFileName);
                if (!file_exists($permintaanFullPath)) {
                    $permintaanFullPath = null;
                }
            }

            $pdfFileName = 'hta-gpa-' . $idPengajuan . '.pdf';
            $storagePath = $dirPath . '/' . $pdfFileName;

            if (Storage::exists($storagePath)) {
                Storage::delete($storagePath);
            }

            if ($permintaanFullPath) {
                try {
                    $combinedPdf = new Fpdi();

                    // A. PDF Permintaan (halaman awal)
                    $pageCount = $combinedPdf->setSourceFile($permintaanFullPath);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $tplIdx = $combinedPdf->importPage($i);
                        $size = $combinedPdf->getTemplateSize($tplIdx);
                        $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $combinedPdf->useTemplate($tplIdx);
                    }

                    // B. PDF HTA-GPA (halaman berikutnya)
                    $pageCount = $combinedPdf->setSourceFile($htaGpaTempPath);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $tplIdx = $combinedPdf->importPage($i);
                        $size = $combinedPdf->getTemplateSize($tplIdx);
                        $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $combinedPdf->useTemplate($tplIdx);
                    }

                    $combinedContent = $combinedPdf->Output('', 'S');
                    Storage::put($storagePath, $combinedContent);
                } catch (\Exception $e) {
                    Log::error('Error combining HTA-GPA PDF: ' . $e->getMessage());
                    Storage::put($storagePath, $htaGpaOutput);
                }
            } else {
                Storage::put($storagePath, $htaGpaOutput);
            }

            if (file_exists($htaGpaTempPath)) {
                unlink($htaGpaTempPath);
            }

            return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
        } catch (\Exception $e) {
            Log::error('Error generate HTA-GPA: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // 3. GENERATE REKOMENDASI
    // ==========================================
    public function generateRekomendasi($idPengajuan)
    {
        try {
            $rekomendasi = Rekomendasi::with(
                'getRekomedasiDetail.getPerusahaan',
                'getRekomedasiDetail.getBarang',
                'getRekomedasiDetail.getNegara',
                'getUserNego',
                'getDisetujuiOleh',
                'getPerusahaan',
                'getPengajuan.getVendor.getVendorDetail'
            )
                ->where('IdPengajuan', $idPengajuan)
                ->whereNotNull('DisetujuiOleh')
                ->first();

            if (is_null($rekomendasi)) {
                return null;
            }

            $jenis = PengajuanPembelian::find($rekomendasi->IdPengajuan);

            // Generate QR Codes
            if ($rekomendasi->UserNego !== null) {
                $qrCode = QrCode::create($rekomendasi->id)->setSize(300)->setMargin(10);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $rekomendasi->qrCodeNego = base64_encode($result->getString());
            }

            if ($rekomendasi->DisetujuiOleh !== null) {
                $qrCode = QrCode::create($rekomendasi->id ?? '')->setSize(300)->setMargin(10);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $rekomendasi->qrCodeApprove = base64_encode($result->getString());
            }

            $pdfFileName = 'rekomendasi_' . $idPengajuan . '.pdf';
            $dirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
            $storagePath = $dirPath . '/' . $pdfFileName;
            $fullDirPath = storage_path('app/' . $dirPath);

            if (!file_exists($fullDirPath)) {
                mkdir($fullDirPath, 0777, true);
            }

            if (Storage::exists($storagePath)) {
                Storage::delete($storagePath);
            }

            if ($jenis->Jenis == 1) {
                $pdf = \PDF::loadView('rekomendasi-pembelian.cetak-review', [
                    'rekomendasi' => $rekomendasi,
                ]);
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

                Storage::put($storagePath, $pdf->output());
                return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
            } else {
                $pdf = \PDF::loadView('rekomendasi-pembelian.cetak-review-umum', [
                    'rekomendasi' => $rekomendasi,
                ]);
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

                $hasAttachment = !empty($rekomendasi->File) &&
                    Storage::disk('public')->exists('rekomendasi_file/' . $rekomendasi->File);

                if (!$hasAttachment) {
                    Storage::put($storagePath, $pdf->output());
                    return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
                }

                $storedFilePath = Storage::disk('public')->path('rekomendasi_file/' . $rekomendasi->File);
                if (!file_exists($storedFilePath)) {
                    Storage::put($storagePath, $pdf->output());
                    return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
                }

                // Merge PDF
                $generatedFullPath = $fullDirPath . '/temp_rekom_' . time() . '_' . uniqid() . '.pdf';
                $pdf->save($generatedFullPath);

                $fpdi = new Fpdi();

                $pageCount = $fpdi->setSourceFile($generatedFullPath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $template = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($template);
                    $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $fpdi->useTemplate($template);
                }

                $attachCount = $fpdi->setSourceFile($storedFilePath);
                for ($i = 1; $i <= $attachCount; $i++) {
                    $template = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($template);
                    $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $fpdi->useTemplate($template);
                }

                $mergedContent = $fpdi->Output('', 'S');
                Storage::put($storagePath, $mergedContent);

                if (file_exists($generatedFullPath)) {
                    unlink($generatedFullPath);
                }

                return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
            }
        } catch (\Exception $e) {
            Log::error('Error generate Rekomendasi: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // 4. GENERATE FS (gabungan rekomendasi + fs + hta-gpa)
    // ==========================================
    public function generateFs($idPengajuan)
    {
        try {
            $data = FeasibilityStudy::with('getFsDetail', 'getBarang', 'getPengajuan')
                ->where('IdPengajuan', $idPengajuan)
                ->firstOrFail();

            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $data->JenisForm)
                ->where('DokumenId', $data->id)
                ->orderBy('Urutan', 'asc')
                ->get();

            $this->generateQrCodes($approval);

            $pdfView = view('feasibility-study.cetak', [
                'data' => $data,
                'approval' => $approval,
                'idPengajuan' => $idPengajuan,
            ])->render();

            $pdfFs = \PDF::loadHTML($pdfView);
            $pdfFs->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            $dirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
            $fullDirPath = storage_path('app/' . $dirPath);
            if (!file_exists($fullDirPath)) {
                mkdir($fullDirPath, 0777, true);
            }

            $fsTempPath = $fullDirPath . '/fs-temp-' . $idPengajuan . '.pdf';
            file_put_contents($fsTempPath, $pdfFs->output());

            $rekomendasiFileName = 'rekomendasi_' . $idPengajuan . '.pdf';
            $rekomendasiPath = $fullDirPath . '/' . $rekomendasiFileName;
            $htaGpaPath = $fullDirPath . '/hta-gpa-' . $idPengajuan . '.pdf';

            $combinedPdf = new Fpdi();
            $pdfFilesToMerge = [];

            if (file_exists($rekomendasiPath)) {
                $pdfFilesToMerge[] = $rekomendasiPath;
            }

            $pdfFilesToMerge[] = $fsTempPath;

            if (file_exists($htaGpaPath)) {
                $pdfFilesToMerge[] = $htaGpaPath;
            }

            foreach ($pdfFilesToMerge as $pdfFile) {
                try {
                    $pageCount = $combinedPdf->setSourceFile($pdfFile);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $tplIdx = $combinedPdf->importPage($i);
                        $size = $combinedPdf->getTemplateSize($tplIdx);
                        $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $combinedPdf->useTemplate($tplIdx);
                    }
                } catch (\Exception $e) {
                    Log::error('Error merging PDF FS: ' . $e->getMessage() . ' - File: ' . $pdfFile);
                }
            }

            $finalFileName = 'fs-' . $idPengajuan . '.pdf';
            $finalPath = $fullDirPath . '/' . $finalFileName;
            $finalPath = str_replace('\\', '/', $finalPath);

            if (file_exists($finalPath)) {
                unlink($finalPath);
            }

            $combinedPdf->Output($finalPath, 'F');

            if (file_exists($fsTempPath)) {
                unlink($fsTempPath);
            }

            return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $finalFileName;
        } catch (\Exception $e) {
            Log::error('Error generate FS: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // 5. GENERATE FUI (gabungan fs + fui)
    // ==========================================

    public function generateFui($idPengajuan)
    {
        try {
            $usulan = UsulanInvestasi::with(
                'getFuiDetail.getVendor',
                'getBarang',
                'getVendor',
                'getAccDirektur',
                'getAccKadiv',
                'getDepartemen',
                'getDepartemen2',
                'getNamaForm'
            )
                ->where('IdPengajuan', $idPengajuan)
                ->first();

            if (!$usulan) {
                return null;
            }

            // Ambil data pengajuan untuk cek Jenis
            $pengajuan = PengajuanPembelian::find($idPengajuan);
            if (!$pengajuan) {
                return null;
            }

            // Ambil item ID dari FUI
            $idPengajuanItem = $usulan->PengajuanItemId;

            $VendorAcc = Rekomendasi::with([
                'getRekomedasiDetail' => function ($query2) {
                    $query2->where('Rekomendasi', 1);
                },
                'getRekomedasiDetail.getNamaVendor'
            ])
                ->where('PengajuanItemId', $idPengajuanItem)
                ->first();

            $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')
                ->where('IdPengajuan', $idPengajuan)
                ->first();

            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $usulan->JenisForm)
                ->where('DokumenId', $usulan->id)
                ->orderBy('Urutan', 'asc')
                ->get();

            $this->generateQrCodes($approval);

            $Acc = $VendorAcc && isset($VendorAcc->getRekomedasiDetail[0]) ? $VendorAcc->getRekomedasiDetail[0]->IdVendor : null;
            $NamaBarangAcc = $VendorAcc && isset($VendorAcc->getRekomedasiDetail[0]) ? $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan : null;

            $data2 = PengajuanPembelian::with([
                'getVendor' => function ($query2) use ($Acc) {
                    $query2->where('NamaVendor', $Acc);
                },
                'getVendor.getVendorDetail' => function ($query) use ($NamaBarangAcc) {
                    $query->where('NamaBarang', $NamaBarangAcc);
                },
                'getRekomendasi' => function ($query) {
                    $query->with([
                        'getRekomedasiDetail' => function ($query2) {
                            $query2->where('Rekomendasi', 1);
                        }
                    ]);
                }
            ])->find($idPengajuan);

            // Generate PDF FUI
            $pdf = \PDF::loadView('form-usulan-investari.show-pdf', compact('usulan', 'VendorAcc', 'approval', 'data2', 'dataRekom'))
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif',
                ])
                ->setPaper('a4', 'portrait');

            $dirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
            $fullDirPath = storage_path('app/' . $dirPath);
            if (!file_exists($fullDirPath)) {
                mkdir($fullDirPath, 0777, true);
            }

            // Simpan FUI sementara
            $fuiTempPath = $fullDirPath . '/fui-temp-' . $idPengajuan . '.pdf';
            file_put_contents($fuiTempPath, $pdf->output());

            // ==========================================
            // TENTUKAN FILE YANG AKAN DIGABUNG BERDASARKAN JENIS
            // ==========================================
            $combinedPdf = new Fpdi();
            $pdfFilesToMerge = [];

            if ($pengajuan->Jenis == 1) {
                // ==========================================
                // JENIS == 1: GABUNGKAN FS + FUI
                // (FS sudah berisi: rekomendasi + fs + hta-gpa)
                // ==========================================
                $fsPath = $fullDirPath . '/fs-' . $idPengajuan . '.pdf';

                if (file_exists($fsPath)) {
                    $pdfFilesToMerge[] = $fsPath;
                }

                $pdfFilesToMerge[] = $fuiTempPath;

                Log::info('FUI Jenis 1: Gabungkan FS + FUI untuk pengajuan ' . $idPengajuan);
            } else {
                // ==========================================
                // JENIS != 1: GABUNGKAN REKOMENDASI + HTA-GPA + FUI
                // ==========================================
                $rekomendasiPath = $fullDirPath . '/rekomendasi_' . $idPengajuan . '.pdf';
                $htaGpaPath = $fullDirPath . '/hta-gpa-' . $idPengajuan . '.pdf';

                // 1. Rekomendasi (halaman pertama) - jika ada
                if (file_exists($rekomendasiPath)) {
                    $pdfFilesToMerge[] = $rekomendasiPath;
                }

                // 2. HTA-GPA (halaman kedua) - jika ada
                if (file_exists($htaGpaPath)) {
                    $pdfFilesToMerge[] = $htaGpaPath;
                }

                // 3. FUI (halaman terakhir) - yang baru di-generate
                $pdfFilesToMerge[] = $fuiTempPath;

                Log::info('FUI Jenis != 1: Gabungkan Rekomendasi + HTA-GPA + FUI untuk pengajuan ' . $idPengajuan);
            }

            // ==========================================
            // PROSES PENGGABUNGAN
            // ==========================================
            foreach ($pdfFilesToMerge as $pdfFile) {
                try {
                    $pageCount = $combinedPdf->setSourceFile($pdfFile);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $tplIdx = $combinedPdf->importPage($i);
                        $size = $combinedPdf->getTemplateSize($tplIdx);
                        $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $combinedPdf->useTemplate($tplIdx);
                    }
                } catch (\Exception $e) {
                    Log::error('Error merging PDF FUI: ' . $e->getMessage() . ' - File: ' . $pdfFile);
                }
            }

            // ==========================================
            // SIMPAN HASIL GABUNGAN
            // ==========================================
            $finalFileName = 'fui-' . $idPengajuan . '.pdf';
            $finalPath = $fullDirPath . '/' . $finalFileName;
            $finalPath = str_replace('\\', '/', $finalPath);

            // Hapus file lama
            if (file_exists($finalPath)) {
                unlink($finalPath);
            }

            $combinedPdf->Output($finalPath, 'F');

            // Hapus file temporary FUI
            if (file_exists($fuiTempPath)) {
                unlink($fuiTempPath);
            }

            return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $finalFileName;
        } catch (\Exception $e) {
            Log::error('Error generate FUI: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // HELPER: Generate QR Code untuk approval
    // ==========================================
    private function generateQrCodes($approval)
    {
        foreach ($approval as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $item->qrCode = base64_encode($result->getString());
            }
        }
    }
}
