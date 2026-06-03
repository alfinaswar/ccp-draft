<?php

namespace App\Http\Controllers;

use App\Mail\NotifFs;
use App\Models\AktivitasPengajuan;
use App\Models\DokumenApproval;
use App\Models\FeasibilityStudy;
use App\Models\FeasibilityStudyDetail;
use App\Models\MasterBarang;
use App\Models\MasterForm;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FeasibilityStudyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($idPengajuan, $idPengajuanItem)
    {
        $idPengajuan = decrypt($idPengajuan);
        $idPengajuanItem = decrypt($idPengajuanItem);
        $data = PengajuanItem::with([
            'getFui',
            'getRekomendasi.getRekomedasiDetail' => function ($query) {
                $query->where('Rekomendasi', 1);
            },
            'getHtaGpa'
        ])->find($idPengajuanItem);
        // dd($data);
        $barang = MasterBarang::where('id', $data->IdBarang)->first();
        return view('feasibility-study.create', compact('data', 'idPengajuan', 'idPengajuanItem', 'barang'));
    }
    public function edit($idPengajuan, $idPengajuanItem)
    {

        $fs = FeasibilityStudy::with('getFsDetail', 'getBarang')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->firstOrFail();

        $approval = null;
        if ($fs !== null) {
            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $fs->JenisForm)
                ->where('DokumenId', $fs->id)
                ->orderBy('Urutan', 'asc')
                ->get();
        } else {
            $approval = null;
        }
        $barang = MasterBarang::where('id', $fs->IdBarang)->first();
        return view('feasibility-study.edit', compact('fs', 'idPengajuan', 'idPengajuanItem', 'barang', 'approval'));
    }
    public function kirimUlangNotifikasi($id)
    {
        // dd($id);
        $header = FeasibilityStudy::find($id);
        // dd($fs);
        if (!$header) {
            return back()->with('error', 'Permintaan pembelian tidak ditemukan.');
        }

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $header->JenisForm)
            ->where('DokumenId', $header->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->get();
        // dd($approval);
        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $header->JenisForm)
            ->where('DokumenId', $header->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        $data222 = FeasibilityStudy::with('getFsDetail', 'getBarang')
            ->where('IdPengajuan', $header->IdPengajuan)
            ->where('PengajuanItemId', $header->PengajuanItemId)
            ->firstOrFail();
        foreach ($approval as $penilai) {
            try {
                if (empty($penilai->Email))
                    continue;
                Mail::to($penilai->Email)
                    ->send(new NotifFs(
                        $header,
                        $penilai,
                        $approval2,
                        $data222
                    ));
                $penilai->StatusEmail = 'Terkirim';
                $penilai->save();
            } catch (\Exception $e) {
                $penilai->StatusEmail = 'Gagal Kirim';
                $penilai->save();

            }
        }
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($header)
                ->withProperties(['ip' => request()->ip()])
                ->log('Kirim Ulang Email FS: ' . ($header->id ?? $header->id));
        }
        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim ulang.');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'idPengajuan' => 'required',
            'idPengajuanItem' => 'required',
            'NamaBarang' => 'required',
            'NilaiInvestasi' => 'required',
            'Spesifikasi' => 'required',
            'BungaTetap' => 'required',
            'Penyusutan' => 'required',
            'Maintenance' => 'required',
            'Pegawai' => 'required',
            'SewaGedung' => 'required',
            'TotalBiayaTetap' => 'required',
            'Konsumable' => 'required',
            'Dokter' => 'required',
            'TotalBiayaVariable' => 'required',
            'BungaBank' => 'required',
            'EstimasiPembiayaan' => 'required',
            'UmurEkonomis' => 'required',
            'Maintenance2' => 'required',
            'JumlahPakaiPertahun' => 'required',
            'Tarif' => 'required',
            'rugi_laba' => 'required|array',
            'rugi_laba.TahunKe' => 'required|array',
            'rugi_laba.JumlahPasien' => 'required|array',
            'rugi_laba.JumlahPasienBpjs' => 'required|array',
            'rugi_laba.TarifUmum' => 'required|array',
            'rugi_laba.TarifBpjs' => 'required|array',
            'rugi_laba.Revenue' => 'required|array',
            'rugi_laba.TotalBiaya' => 'required|array',
            'rugi_laba.BiayaTetap' => 'required|array',
            'rugi_laba.BiayaVariable' => 'required|array',
            'rugi_laba.NetProfit' => 'required|array',
            'rugi_laba.Ebitda' => 'required|array',
            'rugi_laba.AkumEbitda' => 'required|array',
            'rugi_laba.RoiTahunKe' => 'required|array'
        ];

        $this->validate($request, $rules);
        $idPengajuan = $request->input('idPengajuan');
        $idPengajuanItem = $request->input('idPengajuanItem');
        $parseRupiah = function ($value) {
            return (int) preg_replace('/[^\d]/', '', $value ?? 0);
        };

        $header = FeasibilityStudy::updateOrCreate(
            [
                'IdPengajuan' => $idPengajuan,
                'PengajuanItemId' => $idPengajuanItem,
                'JenisForm' => 6,
            ],
            [
                'NamaBarang' => $request->input('NamaBarang'),
                'NilaiInvestasi' => preg_replace('/[^\d]/', '', $request->input('NilaiInvestasi')),
                'Spesifikasi' => $request->input('Spesifikasi'),
                'BiayaTetap' => preg_replace('/[^\d]/', '', $request->input('BiayaTetap')),
                'BungaTetap' => preg_replace('/[^\d]/', '', $request->input('BungaTetap')),
                'Penyusutan' => preg_replace('/[^\d]/', '', $request->input('Penyusutan')),
                'Maintenance' => preg_replace('/[^\d]/', '', $request->input('Maintenance')),
                'JumlahPerHariPakai' => preg_replace('/[^\d]/', '', $request->input('JumlahPerHariPakai')),
                'JumlahAlat' => preg_replace('/[^\d]/', '', $request->input('JumlahAlat')),
                'JumlahPakaiPertahun' => preg_replace('/[^\d]/', '', $request->input('JumlahPakaiPertahun')),
                'JumlahHariRawat' => preg_replace('/[^\d]/', '', $request->input('JumlahHariRawat')),
                'Pegawai' => preg_replace('/[^\d]/', '', $request->input('Pegawai')),
                'SewaGedung' => preg_replace('/[^\d]/', '', $request->input('SewaGedung')),
                'TotalBiayaTetap' => preg_replace('/[^\d]/', '', $request->input('TotalBiayaTetap')),
                'Konsumable' => preg_replace('/[^\d]/', '', $request->input('Konsumable') ?? 0),
                'Dokter' => preg_replace('/[^\d]/', '', $request->input('Dokter') ?? 0),
                'TotalBiayaVariable' => preg_replace('/[^\d]/', '', $request->input('TotalBiayaVariable') ?? 0),
                'Tarif' => preg_replace('/[^\d]/', '', $request->input('Tarif')) ?? 0,
                'BungaBank' => preg_replace('/[^\d]/', '', $request->input('BungaBank')),
                'EstimasiPembiayaan' => preg_replace('/[^\d]/', '', $request->input('EstimasiPembiayaan')),
                'UmurEkonomis' => preg_replace('/[^\d]/', '', $request->input('UmurEkonomis')),
                'Maintenance2' => preg_replace('/[^\d]/', '', $request->input('Maintenance2')),
                'UserCreate' => auth()->user()->name ?? null,
                'KodePerusahaan' => auth()->user()->kodeperusahaan ?? null,
            ]
        );

        $rugiLaba = $request->input('rugi_laba', []);
        $tahunKeArr = isset($rugiLaba['TahunKe']) ? $rugiLaba['TahunKe'] : [];

        for ($i = 1; $i <= 8; $i++) {
            $detailData = [
                'IdFs' => $header->id,
                'TahunKe' => $tahunKeArr[$i] ?? $i,
                'JumlahPasien' => preg_replace('/[^\d]/', '', $rugiLaba['JumlahPasien'][$i] ?? 0),
                'JumlahPasienBpjs' => preg_replace('/[^\d]/', '', $rugiLaba['JumlahPasienBpjs'][$i] ?? 0),
                'TarifUmum' => preg_replace('/[^\d]/', '', $rugiLaba['TarifUmum'][$i] ?? 0),
                'TarifBpjs' => preg_replace('/[^\d]/', '', $rugiLaba['TarifBpjs'][$i] ?? 0),
                'Revenue' => preg_replace('/[^\d]/', '', $rugiLaba['Revenue'][$i] ?? 0),
                'TotalBiaya' => preg_replace('/[^\d]/', '', $rugiLaba['TotalBiaya'][$i] ?? 0),
                'BiayaTetap' => preg_replace('/[^\d]/', '', $rugiLaba['BiayaTetap'][$i] ?? 0),
                'BiayaVariable' => preg_replace('/[^\d]/', '', $rugiLaba['BiayaVariable'][$i] ?? 0),
                'NetProfit' => preg_replace('/[^\d]/', '', $rugiLaba['NetProfit'][$i] ?? 0),
                'Ebitda' => preg_replace('/[^\d]/', '', $rugiLaba['Ebitda'][$i] ?? 0),
                'AkumEbitda' => preg_replace('/[^\d]/', '', $rugiLaba['AkumEbitda'][$i] ?? 0),
                'RoiTahunKe' => preg_replace('/[^\d]/', '', $rugiLaba['RoiTahunKe'][$i] ?? 0),
                'UserCreate' => auth()->user()->name ?? null,
            ];
            FeasibilityStudyDetail::updateOrCreate(
                [
                    'IdFs' => $header->id,
                    'TahunKe' => $tahunKeArr[$i] ?? $i,
                ],
                $detailData
            );
        }

        $Form = MasterForm::with([
            'getApproval' => function ($q) use ($header) {
                $q->where('KodePerusahaan', $header->KodePerusahaan);
            },
            'getApproval.getUser'
        ])
            ->where('id', $header->JenisForm)
            ->first();
        // dd($Form);
        foreach ($Form->getApproval as $approvalSetting) {
            $isAutoApprove = ($approvalSetting->UserId ?? null) == (auth()->user()->id ?? null);

            $status = $isAutoApprove ? 'Approved' : 'Pending';
            $tanggalApprove = $isAutoApprove ? now() : null;
            $ttd = $isAutoApprove ? (auth()->user()->ttd ?? null) : null; // opsional, pakai field ttd dari user jika ada

            DokumenApproval::updateOrCreate(
                [
                    'JenisFormId' => $header->JenisForm,
                    'DokumenId' => $header->id,
                    'Urutan' => $approvalSetting->Urutan ?? null,
                ],
                [
                    'JenisUser' => $approvalSetting->JenisUser ?? 'Master',
                    'DepartemenId' => $approvalSetting->Departemen,
                    'PerusahaanId' => $approvalSetting->KodePerusahaan,
                    'JabatanId' => $approvalSetting->JabatanId ?? null,
                    'UserId' => $approvalSetting->UserId ?? null,
                    'Nama' => $approvalSetting->getUser->name ?? null,
                    'Email' => $approvalSetting->getUser->email ?? null,
                    'Status' => $status,
                    'TanggalApprove' => $tanggalApprove,
                    'ApprovalToken' => str_replace('-', '', Str::uuid()->toString()),
                    'Catatan' => null,
                    'Ttd' => $ttd,
                    'UserCreate' => auth()->user()->name,
                ]
            );
        }

        $approvalDocs = DokumenApproval::where([
            'JenisFormId' => $header->JenisForm,
            'DokumenId' => $header->id,
        ])->orderBy('Urutan', 'asc')->get();

        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $header->JenisForm)
            ->where('DokumenId', $header->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        // dd($approval2);

        foreach ($approval2 as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }

        $data222 = FeasibilityStudy::with('getFsDetail', 'getBarang', 'getPerusahaan')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->firstOrFail();

        $Pengajuan = PengajuanPembelian::find($idPengajuan);

        // Mulai logika pengiriman email sesuai instruksi
        $emailsToSend = [];

        // Cek urutan 1 dari approval2
        $approval1 = $approval2->where('Urutan', 1)->first();

        if ($approval1) {
            if (
                !empty($approval1->Email) && $approval1->Email != '-' && $approval1->UserId != 2
            ) {
                if ($approval1->Status == 'Pending') {
                    $emailsToSend[] = $approval1;
                } else if ($approval1->Status == 'Approved') {
                    // Cari urutan ke-2
                    $approval2second = $approval2->where('Urutan', 2)->first();
                    if (
                        $approval2second &&
                        !empty($approval2second->Email) &&
                        $approval2second->Email != '-' &&
                        $approval2second->UserId != 2 &&
                        $approval2second->Status != 'Approved'
                    ) {
                        $emailsToSend[] = $approval2second;
                    }
                }
            }
        }

        // Kalau tidak ada urutan 1, fallback ke existing logic (kirim ke semua yang bukan approved)
        if (empty($emailsToSend)) {
            foreach ($approval2 as $penilai) {
                if (
                    empty($penilai->Email) || $penilai->Email == '-' || $penilai->UserId == 2 ||
                    $penilai->Status == 'Approved'
                ) {
                    continue;
                }
                $emailsToSend[] = $penilai;
            }
        }

        foreach ($emailsToSend as $penilai) {
            Mail::to($penilai->Email)
                ->send(new NotifFs(
                    $header,
                    $penilai,
                    $approval2,
                    $data222,
                    $Pengajuan
                ));
        }

        // Untuk FS
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($header)
                ->withProperties(['ip' => request()->ip()])
                ->log('Pembuatan Feasibility Study (FS): ' . ($header->id ?? $header->id));
        }

        $pengajuanFs = PengajuanPembelian::find($header->IdPengajuan ?? null);
        $kodePengajuan = $pengajuanFs ? $pengajuanFs->KodePengajuan : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'FS',
            'Keterangan' => 'Pembuatan Feasibility Study (FS) untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' sudah dibuat',
            'UserCreate' => auth()->user()->name,
        ]);
        return redirect()->back()->with('success', 'Feasibility Study berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show($idPengajuan, $idPengajuanItem)
    {
        $data = FeasibilityStudy::with('getFsDetail', 'getBarang')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->firstOrFail();

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->JenisForm)
            ->where('DokumenId', $data->id)
            ->orderBy('Urutan', 'asc')
            ->get();

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

        return view('feasibility-study.show', [
            'data' => $data,
            'approval' => $approval,
            'idPengajuan' => $idPengajuan,
            'idPengajuanItem' => $idPengajuanItem
        ]);
    }

    public function cetak($idPengajuan, $idPengajuanItem)
    {
        $data = FeasibilityStudy::with('getFsDetail', 'getBarang')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->firstOrFail();

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->JenisForm)
            ->where('DokumenId', $data->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        // Generate QR code untuk setiap approval yang approved
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

        // Render blade view to HTML
        $pdfView = view('feasibility-study.cetak', [
            'data' => $data,
            'approval' => $approval,
            'idPengajuan' => $idPengajuan,
            'idPengajuanItem' => $idPengajuanItem
        ])->render();

        // Generate PDF dari HTML view
        $pdf = \PDF::loadHTML($pdfView);

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream('Feasibility-Study-' . $data->id . '.pdf');
    }

    public function approve($token)
    {
        $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', $token)->firstOrFail();
        if ($penilai->Status !== 'Pending') {
            return view('emails.setelah-approval', compact('penilai'))->with([
                'message' => 'Persetujuan sudah diproses sebelumnya.',
            ]);
        }
        $penilai->update([
            'Status' => 'Approved',
            'TanggalApprove' => Carbon::now(),
        ]);

        $pengajuan = null;
        $kodePengajuan = null;
        $fs = null;

        if ($penilai->DokumenId ?? false) {
            $fs = FeasibilityStudy::find($penilai->DokumenId);
            if ($fs) {
                $pengajuan = PengajuanPembelian::find($fs->IdPengajuan);
                $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($fs->Nomor ?? $fs->id);
            }
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'Feasibility Study',
            'Keterangan' => ($penilai->Nama ?? '-') . ' telah menyetujui Feasibility Study',
            'UserCreate' => $penilai->Nama ?? '-',
        ]);

        // === CEK APPROVAL SELANJUTNYA ===
        $nextApproval = DokumenApproval::where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->where('Urutan', '>', $penilai->Urutan)
            ->orderBy('Urutan', 'asc')
            ->first();
        // && $nextApproval->UserId != 2
        if ($nextApproval) {
            if (!empty($nextApproval->Email) && $nextApproval->Email != '-') {
                $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $penilai->JenisFormId)
                    ->where('DokumenId', $penilai->DokumenId)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $dataFs = FeasibilityStudy::with('getFsDetail', 'getBarang', 'getPerusahaan')
                    ->where('id', $penilai->DokumenId)
                    ->first();

                // Generate QR Code jika diperlukan (sesuai logic store)
                foreach ($approval2 as $item) {
                    if ($item->Status == 'Approved') {
                        $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                            ->setSize(300)
                            ->setMargin(10);
                        $writer = new PngWriter();
                        $result = $writer->write($qrCode);
                        $item->qrCode = base64_encode($result->getString());
                    }
                }

                try {
                    Mail::to($nextApproval->Email)
                        ->send(new NotifFs(
                            $fs,
                            $nextApproval,
                            $approval2,
                            $dataFs,
                            $pengajuan
                        ));

                    $nextApproval->StatusEmail = 'Terkirim';
                    $nextApproval->save();

                } catch (\Exception $e) {
                    $nextApproval->StatusEmail = 'Gagal Kirim';
                    $nextApproval->save();
                }
            }
        } else {
            if ($fs) {
                $fs->update([
                    'Status' => 'Final',
                    'UserUpdate' => auth()->user()->name ?? null,
                ]);
                // Log aktivitas final approval
                AktivitasPengajuan::create([
                    'KodePengajuan' => $kodePengajuan,
                    'Jenis' => 'Feasibility Study',
                    'Keterangan' => 'Feasibility Study ' . ($kodePengajuan ?? $fs->id) . ' telah selesai disetujui seluruh approver',
                    'UserCreate' => 'System',
                ]);
            }
        }

        return view('emails.setelah-approval', compact('penilai'))->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $idPengajuan = $request->input('idPengajuan');
        $idPengajuanItem = $request->input('idPengajuanItem');
        $parseRupiah = function ($value) {
            return (int) preg_replace('/[^\d]/', '', $value ?? 0);
        };

        $header = FeasibilityStudy::where('id', $id)->firstOrFail();

        $header->update([
            'NamaBarang' => $request->input('NamaBarang'),
            'NilaiInvestasi' => preg_replace('/[^\d]/', '', $request->input('NilaiInvestasi')),
            'Spesifikasi' => $request->input('Spesifikasi'),
            'BiayaTetap' => preg_replace('/[^\d]/', '', $request->input('BiayaTetap')),
            'BungaTetap' => preg_replace('/[^\d]/', '', $request->input('BungaTetap')),
            'Penyusutan' => preg_replace('/[^\d]/', '', $request->input('Penyusutan')),
            'Maintenance' => preg_replace('/[^\d]/', '', $request->input('Maintenance')),
            'JumlahPerHariPakai' => preg_replace('/[^\d]/', '', $request->input('JumlahPerHariPakai')),
            'JumlahAlat' => preg_replace('/[^\d]/', '', $request->input('JumlahAlat')),
            'JumlahPakaiPertahun' => preg_replace('/[^\d]/', '', $request->input('JumlahPakaiPertahun')),
            'JumlahHariRawat' => preg_replace('/[^\d]/', '', $request->input('JumlahHariRawat')),
            'Pegawai' => preg_replace('/[^\d]/', '', $request->input('Pegawai')),
            'SewaGedung' => preg_replace('/[^\d]/', '', $request->input('SewaGedung')),
            'TotalBiayaTetap' => preg_replace('/[^\d]/', '', $request->input('TotalBiayaTetap')),
            'Konsumable' => preg_replace('/[^\d]/', '', $request->input('Konsumable') ?? 0),
            'Dokter' => preg_replace('/[^\d]/', '', $request->input('Dokter') ?? 0),
            'TotalBiayaVariable' => preg_replace('/[^\d]/', '', $request->input('TotalBiayaVariable') ?? 0),
            'Tarif' => preg_replace('/[^\d]/', '', $request->input('Tarif')) ?? 0,
            'BungaBank' => preg_replace('/[^\d]/', '', $request->input('BungaBank')),
            'EstimasiPembiayaan' => preg_replace('/[^\d]/', '', $request->input('EstimasiPembiayaan')),
            'UmurEkonomis' => preg_replace('/[^\d]/', '', $request->input('UmurEkonomis')),
            'Maintenance2' => preg_replace('/[^\d]/', '', $request->input('Maintenance2')),
            'UserCreate' => auth()->user()->name ?? null,
            'KodePerusahaan' => auth()->user()->kodeperusahaan ?? null,
        ]);

        $rugiLaba = $request->input('rugi_laba', []);
        $tahunKeArr = isset($rugiLaba['TahunKe']) ? $rugiLaba['TahunKe'] : [];

        for ($i = 1; $i <= 8; $i++) {
            $detailData = [
                'IdFs' => $header->id,
                'TahunKe' => $tahunKeArr[$i] ?? $i,
                'JumlahPasien' => preg_replace('/[^\d]/', '', $rugiLaba['JumlahPasien'][$i] ?? 0),
                'JumlahPasienBpjs' => preg_replace('/[^\d]/', '', $rugiLaba['JumlahPasienBpjs'][$i] ?? 0),
                'TarifUmum' => preg_replace('/[^\d]/', '', $rugiLaba['TarifUmum'][$i] ?? 0),
                'TarifBpjs' => preg_replace('/[^\d]/', '', $rugiLaba['TarifBpjs'][$i] ?? 0),
                'Revenue' => preg_replace('/[^\d]/', '', $rugiLaba['Revenue'][$i] ?? 0),
                'TotalBiaya' => preg_replace('/[^\d]/', '', $rugiLaba['TotalBiaya'][$i] ?? 0),
                'BiayaTetap' => preg_replace('/[^\d]/', '', $rugiLaba['BiayaTetap'][$i] ?? 0),
                'BiayaVariable' => preg_replace('/[^\d]/', '', $rugiLaba['BiayaVariable'][$i] ?? 0),
                'NetProfit' => preg_replace('/[^\d]/', '', $rugiLaba['NetProfit'][$i] ?? 0),
                'Ebitda' => preg_replace('/[^\d]/', '', $rugiLaba['Ebitda'][$i] ?? 0),
                'AkumEbitda' => preg_replace('/[^\d]/', '', $rugiLaba['AkumEbitda'][$i] ?? 0),
                'RoiTahunKe' => preg_replace('/[^\d]/', '', $rugiLaba['RoiTahunKe'][$i] ?? 0),
                'UserCreate' => auth()->user()->name ?? null,
            ];
            FeasibilityStudyDetail::updateOrCreate(
                [
                    'IdFs' => $header->id,
                    'TahunKe' => $tahunKeArr[$i] ?? $i,
                ],
                $detailData
            );
        }
        return redirect()->back()->with('success', 'Feasibility Study berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeasibilityStudy $feasibilityStudy)
    {
        //
    }
}
