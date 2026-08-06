<?php

namespace App\Http\Controllers;

use App\Mail\NotifikasiPengajuanMail;
use App\Models\AktivitasPengajuan;
use App\Models\DokumenApproval;
use App\Models\HtaDanGpa;
use App\Models\HtaDanGpaDetail;
use App\Models\MasterDepartemen;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterParameter;
use App\Models\PengajuanPembelian;
use App\Models\PenilaiHtaGpa;
use App\Models\PermintaanPembelian;
use App\Models\User;
use App\Services\PdfGeneratorService;
use Carbon\Carbon;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;

class HtaDanGpaController extends Controller
{
    protected $pdfGenerator;

    public function __construct(PdfGeneratorService $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function index($idPengajuan, $idPengajuanItem)
    {
        $data = PengajuanPembelian::with([
            // 'getHtaGpa',
            'getVendor.getVendorDetail',
            'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem)->with('getPenilai');
            },
            'getHtaGpa.getDetailHta' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem)->with('getPenilai');
            },
            'getJenisPermintaan.getForm',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);
        if (empty($data->getVendor) || count($data->getVendor) < 2) {
            return redirect()->back()->with('error', 'Minimal ada 2 vendor, tolong tambahkan vendor pembanding');
        }

        $approval = null;
        $htagpa = null;
        if ($data->getHtaGpa) {
            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $data->getHtaGpa->JenisForm)
                ->where('DokumenId', $data->getHtaGpa->id)
                ->orderBy('Urutan', 'asc')
                ->get();
            $htagpa = $data->getHtaGpa;
        }
        // dd($htagpa);
        // dd($approval);
        $parameter = MasterParameter::get();
        $user = User::get();
        $jabatan = MasterJabatan::get();
        $departemen = MasterDepartemen::get();

        if ($data->Jenis != 1) {
            return view('hta-gpa.umum.index', compact('data', 'parameter', 'user', 'approval', 'jabatan', 'departemen', 'htagpa'));
        } else {
            return view('hta-gpa.index', compact('data', 'parameter', 'user', 'approval', 'jabatan', 'departemen', 'htagpa'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function ajukan(Request $request)
    {
        $htaDanGpa = HtaDanGpa::where('IdPengajuan', $request->IdPengajuan)
            ->where('PengajuanItemId', $request->PengajuanItemId)
            ->where('IdBarang', $request->IdBarang)
            ->first();
        if ($htaDanGpa) {
            $htaDanGpa->Status = 'Final';
            $htaDanGpa->save();
        }

        return redirect()->back()->with('success', 'Hai ' . auth()->user()->name . ', HTA Berhasil Diajukan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $header = HtaDanGpa::updateOrCreate(
            [
                'JenisForm' => '1',
                'IdPengajuan' => $request->vendor[0]['IdPengajuan'],
                'PengajuanItemId' => $request->vendor[0]['PengajuanItemId'],
            ],
            [
                'JenisForm' => '1',
                'IdPengajuan' => $request->vendor[0]['IdPengajuan'],
                'PengajuanItemId' => $request->vendor[0]['PengajuanItemId'],
                'IdVendor' => $request->vendor[0]['IdVendor'],
                'IdBarang' => $request->vendor[0]['IdBarang'],
                'UserCreate' => auth()->user()->name,
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
                'Status' => 'Diajukan',
                'DiajukanOleh' => auth()->user()->id,
                'DiajukanPada' => now(),
            ]
        );
        // dd($header);
        $cekApproval = DokumenApproval::where('JenisFormId', $header->JenisForm)
            ->where('DokumenId', $header->id)
            ->where('PerusahaanId', $header->KodePerusahaan);
        // ->get();
        if (!$cekApproval->exists()) {
            // dd($cekApproval);
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
                DokumenApproval::updateOrCreate(
                    [
                        'JenisFormId' => $header->JenisForm,
                        'DokumenId' => $header->id,
                        'Urutan' => $approvalSetting->Urutan ?? null,
                    ],
                    [
                        'JenisUser' => $approvalSetting->JenisUser ?? 'Master',
                        'DepartemenId' => $approvalSetting->DepartemenId ?? null,
                        'PerusahaanId' => $approvalSetting->KodePerusahaan,
                        'JabatanId' => $approvalSetting->JabatanId ?? null,
                        'NamaJabatan' => $approvalSetting->NamaJabatan ?? null,
                        'UserId' => $approvalSetting->UserId ?? null,
                        'Nama' => $approvalSetting->getUser->name ?? null,
                        'Status' => 'Pending',
                        'TanggalApprove' => null,
                        'Catatan' => null,
                        'Ttd' => null,
                        'UserCreate' => auth()->user()->name,
                    ]
                );
            }
        }

        $detdetail = HtaDanGpaDetail::where('IdHtaGpa', $header->id)->forceDelete();
        // dd($header->id);
        foreach ($request->vendor as $key => $value) {
            $Isi = HtaDanGpaDetail::create(
                [
                    'IdPengajuan' => $value['IdPengajuan'],
                    'PengajuanItemId' => $value['PengajuanItemId'],
                    'IdHtaGpa' => $header->id,
                    'IdBarang' => $value['IdBarang'] ?? null,
                    'IdVendor' => $value['IdVendor'] ?? null,
                    'IdParameter' => $value['IdParameter'] ?? null,
                    'Parameter' => $value['Parameter'] ?? null,
                    'Deskripsi' => $value['Deskripsi'] ?? null,
                    'Nilai1' => $value['Nilai1'] ?? null,
                    'Nilai2' => $value['Nilai2'] ?? null,
                    'Nilai3' => $value['Nilai3'] ?? null,
                    'Nilai4' => $value['Nilai4'] ?? null,
                    'Nilai5' => $value['Nilai5'] ?? null,
                    'SubTotal' => $value['SubTotal'] ?? null,
                    'UmurEkonomis' => $value['UmurEkonomis'],
                    'BuybackPeriod' => $value['BuybackPeriod'],
                    'TarifDiusulkan' => preg_replace('/\D/', '', $value['TarifDiusulkan']),
                    'TargetPemakaianBulanan' => $value['TargetPemakaianBulanan'],
                    'Keterangan' => $value['Keterangan'],
                ]
            );
        }

        $pengajuan = PengajuanPembelian::find($header->IdPengajuan);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($header->Nomor ?? $header->id);
        // $this->savePdfToStorage($pengajuan->id, $pengajuan->PengajuanItemId);
        $this->pdfGenerator->generateAll($pengajuan->id);
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'HTA-GPA',
            'Keterangan' => 'Data GPA berhasil diperbarui dan seluruh data vendor beserta detail telah tersimpan.',
            'UserCreate' => auth()->user()->name,
        ]);

        activity('hta')
            ->causedBy(auth()->user())
            ->performedOn($header)
            ->withProperties([
                'attributes' => $header->toArray(),
                'vendor_items' => $request->vendor,
            ])
            ->log('Memperbarui data HTA dengan kode ' . ($header->Nomor ?? $header->id));
        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    public function storeUmum(Request $request)
    {
        $idPengajuan = $request->vendor[0]['IdPengajuan'];
        $pengajuan = PengajuanPembelian::find($idPengajuan);

        // jenis pengajuan (2 = logum, 16 = proyek)
        $jenisPengajuan = ($pengajuan && $pengajuan->Jenis == '2') ? 2 : 16;

        // Find or create header
        $header = HtaDanGpa::updateOrCreate(
            [
                'JenisForm' => $jenisPengajuan,
                'IdPengajuan' => $request->vendor[0]['IdPengajuan'],
                'PengajuanItemId' => $request->vendor[0]['PengajuanItemId'],
                'IdBarang' => $request->vendor[0]['IdBarang'],
            ],
            [
                'JenisForm' => $jenisPengajuan,
                'IdPengajuan' => $request->vendor[0]['IdPengajuan'],
                'PengajuanItemId' => $request->vendor[0]['PengajuanItemId'],
                'IdVendor' => $request->vendor[0]['IdVendor'],
                'IdBarang' => $request->vendor[0]['IdBarang'],
                'UserCreate' => auth()->user()->name,
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
                'DiajukanOleh' => auth()->user()->id,
                'DiajukanPada' => now(),
            ]
        );

        $cekApproval = DokumenApproval::where('JenisFormId', $header->JenisForm)
            ->where('DokumenId', $header->id)
            ->where('PerusahaanId', $header->KodePerusahaan);

        if (!$cekApproval->exists()) {
            $Form = MasterForm::with([
                'getApproval' => function ($q) use ($header) {
                    $q->where('KodePerusahaan', $header->KodePerusahaan);
                },
                'getApproval.getUser'
            ])
                ->where('id', $header->JenisForm)
                ->first();

            if ($Form && $Form->getApproval) {
                foreach ($Form->getApproval as $approvalSetting) {
                    DokumenApproval::updateOrCreate(
                        [
                            'JenisFormId' => $header->JenisForm,
                            'DokumenId' => $header->id,
                            'Urutan' => $approvalSetting->Urutan ?? null,
                        ],
                        [
                            'JenisUser' => $approvalSetting->JenisUser ?? 'Master',
                            'DepartemenId' => $approvalSetting->DepartemenId ?? null,
                            'PerusahaanId' => $approvalSetting->KodePerusahaan,
                            'JabatanId' => $approvalSetting->JabatanId ?? null,
                            'NamaJabatan' => $approvalSetting->NamaJabatan ?? null,
                            'UserId' => $approvalSetting->UserId ?? null,
                            'Nama' => $approvalSetting->getUser->name ?? null,
                            'Email' => $approvalSetting->getUser->email ?? null,
                            'Status' => 'Pending',
                            'StatusEmail' => null,
                            'TanggalApprove' => null,
                            'Catatan' => null,
                            'Ttd' => null,
                            'UserCreate' => auth()->user()->name,
                        ]
                    );
                }
            }
        }

        $filename = null;
        if ($request->hasFile('file_both_vendor')) {
            $file = $request->file('file_both_vendor');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('upload/gpa', $filename, 'public');
        } else {
            $filename = null;
        }

        // Hapus semua detail sebelum simpan ulang (delete lalu insert, sesuai instruksi)
        HtaDanGpaDetail::where('IdHtaGpa', $header->id)->forceDelete();

        foreach ($request->vendor as $key => $value) {
            $dataDetail = [];
            // File utama digunakan untuk semua vendor detail
            $dataDetail['File'] = $filename;
            $dataDetail['IdPengajuan'] = $value['IdPengajuan'];
            $dataDetail['PengajuanItemId'] = $value['PengajuanItemId'];
            $dataDetail['IdVendor'] = $value['IdVendor'] ?? null;
            $dataDetail['IdBarang'] = $value['IdBarang'] ?? null;
            $dataDetail['IdHtaGpa'] = $header->id;
            $dataDetail['KodePerusahaan'] = auth()->user()->kodeperusahaan;

            // Simpan detail baru langsung (tanpa update, selalu insert setelah delete)
            $detail = new HtaDanGpaDetail($dataDetail);
            $detail->save();
        }

        // --- Logging & aktifitas
        $pengajuan = PengajuanPembelian::find($header->IdPengajuan);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($header->Nomor ?? $header->id);
        $this->pdfGenerator->generateAll($pengajuan->id);
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'HTA-GPA',
            'Keterangan' => 'Data GPA berhasil diperbarui dan seluruh data vendor beserta detail telah tersimpan.',
            'UserCreate' => auth()->user()->name,
        ]);

        activity('hta')
            ->causedBy(auth()->user())
            ->performedOn($header)
            ->withProperties([
                'attributes' => $header->toArray(),
                'vendor_items' => $request->vendor,
            ])
            ->log('Memperbarui data HTA dengan kode ' . ($header->Nomor ?? $header->id));

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    // public function storeUmum(Request $request)
    // {
    //     $request->validate([
    //         'current_vendor_id' => 'required',
    //     ]);

    //     $vendorId = $request->current_vendor_id;
    //     $vendorData = collect($request->vendor)
    //         ->firstWhere('IdVendor', $vendorId);

    //     if (!$vendorData) {
    //         return back()->with('error', 'Vendor tidak ditemukan.');
    //     }

    //     $header = HtaDanGpa::firstOrCreate(
    //         [
    //             'JenisForm' => '2',
    //             'IdPengajuan' => $vendorData['IdPengajuan'],
    //             'PengajuanItemId' => $vendorData['PengajuanItemId'],
    //             'IdBarang' => $vendorData['IdBarang'],
    //         ],
    //         [
    //             'IdVendor' => $vendorId,
    //             'UserCreate' => auth()->user()->name,
    //             'KodePerusahaan' => auth()->user()->kodeperusahaan,
    //             'DiajukanOleh' => auth()->user()->id,
    //             'DiajukanPada' => now(),
    //         ]
    //     );

    //     if (isset($vendorData['file']) && $vendorData['file']) {

    //         $file = $vendorData['file'];

    //         $filename = uniqid() . '_' . $file->getClientOriginalName();

    //         $file->storeAs('upload/gpa', $filename, 'public');

    //         HtaDanGpaDetail::updateOrCreate(
    //             [
    //                 'IdHtaGpa' => $header->id,
    //                 'IdVendor' => $vendorId,
    //             ],
    //             [
    //                 'IdPengajuan' => $vendorData['IdPengajuan'],
    //                 'PengajuanItemId' => $vendorData['PengajuanItemId'],
    //                 'IdBarang' => $vendorData['IdBarang'],
    //                 'KodePerusahaan' => auth()->user()->kodeperusahaan,
    //                 'File' => $filename,
    //             ]
    //         );
    //     }

    //     activity('hta')
    //         ->causedBy(auth()->user())
    //         ->performedOn($header)
    //         ->withProperties([
    //             'vendor_id' => $vendorId,
    //         ])
    //         ->log('Update HTA vendor ' . $vendorId);

    //     return back()->with('success', 'Data vendor berhasil disimpan.');
    // }
    public function SimpanPenilai(Request $request)
    {
        $cariHTA = HtaDanGpa::with('getDetailHta')
            ->where('IdPengajuan', $request->IdPengajuan)
            ->where('PengajuanItemId', $request->PengajuanItemId)
            ->first();

        if (!$cariHTA) {
            return redirect()->back()->with('error', 'Data HTA tidak ditemukan.');
        }

        $approvalDocs = DokumenApproval::where([
            'JenisFormId' => $cariHTA->JenisForm,
            'DokumenId' => $cariHTA->id,
        ])->orderBy('Urutan', 'asc')->get();

        $sudahAdaToken = $approvalDocs && $approvalDocs->count() > 0 && $approvalDocs->where('ApprovalToken', '!=', '')->count() > 0;
        // JANGAN simpan/update data approval baru jika sudah ada token, agar token tidak berubah
        if (!$sudahAdaToken) {
            // dd($sudahAdaToken);
            foreach ($approvalDocs as $key => $approval) {
                $namaPenilai = $request->NamaPenilai[$key] ?? null;
                $userId = null;
                $userName = null;

                if ($namaPenilai && strpos($namaPenilai, ',') !== false) {
                    [$userId, $userName] = explode(',', $namaPenilai, 2);
                } else {
                    $userId = $namaPenilai;
                    $userName = null;
                }

                $approval->update([
                    'JenisUser' => $request->TipeInputPenilai[$key],
                    'JabatanId' => $request->JabatanId[$key],
                    'DepartemenId' => $request->DepartemenId[$key],
                    'NamaJabatan' => $approval->NamaJabatan,
                    'UserId' => $userId,
                    'Nama' => $request->NamaPenilaiManual[$key] ?? $userName,
                    'Email' => $request->EmailPenilai[$key],
                    'Urutan' => $approval->Urutan,
                    'StatusEmail' => 'Terkirim',
                    'ApprovalToken' => str_replace('-', '', Str::uuid()),
                    'UserUpdate' => auth()->user()->name,
                ]);
            }
        }
// dd(123);
        // Ambil ulang data lengkap dengan relasi untuk digunakan di email & QR
        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $cariHTA->JenisForm)
            ->where('DokumenId', $cariHTA->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        $idPengajuan = $request->IdPengajuan;
        $idPengajuanItem = $request->PengajuanItemId;

        $pengajuan = PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getJenisPermintaan.getForm',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);

        // PERMINTAAN
        $permintaan = PermintaanPembelian::with([
            'getDetail.getBarang.getMerk',
            'getDiajukanOleh',
            'getDetail.getBarang.getSatuan'
        ])->find($pengajuan->IdPermintaan);

        $ApprovalPermintaan = collect();
        if ($permintaan) {
            $approval3 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $permintaan->JenisForm)
                ->where('DokumenId', $permintaan->id)
                ->orderBy('Urutan', 'asc')
                ->get();

            // PERBAIKAN BUG: Looping dari $approval3, bukan $ApprovalPermintaan
            foreach ($approval3 as $item) {
                if ($item->Status == 'Approved') {
                    $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                        ->setSize(80)
                        ->setMargin(10);

                    $writer = new PngWriter();
                    $result = $writer->write($qrCode);

                    $item->qrCode = base64_encode($result->getString());
                }
                // Masukkan object $item ke dalam collection $ApprovalPermintaan
                $ApprovalPermintaan->push($item);
            }
        }

        $parameter = MasterParameter::get();

        // Gunakan $approval2 (yang sudah di-reload) agar data sinkron dengan DB
        $firstApprover = $approval2->first();

        if ($firstApprover && !empty($firstApprover->Email)) {
            $fileLampiran = [];
            if ($cariHTA->JenisForm == '2' || $cariHTA->JenisForm == '16') {
                foreach ($cariHTA->getDetailHta as $detail) {
                    if (!empty($detail->File)) {
                        $fileLampiran[] = $detail->File;
                    }
                }
            }

            Mail::to($firstApprover->Email)
                ->bcc(env('MAIL_DEV_BCC'))
                ->send(new NotifikasiPengajuanMail(
                    $pengajuan,
                    $cariHTA,
                    $parameter,
                    $firstApprover,
                    $approval2,
                    $fileLampiran,
                    $ApprovalPermintaan,
                    $permintaan
                ));
        }

        $kodePengajuan = null;
        $pengajuan = PengajuanPembelian::find($cariHTA->IdPengajuan);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($cariHTA->Nomor ?? $cariHTA->id);
        // Update status HTA/GPA menjadi "Final"
        if ($cariHTA) {
            $cariHTA->Status = 'Final';
            $cariHTA->save();
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'HTA-GPA',
            'Keterangan' => 'HTA/GPA dengan nomor ' . $kodePengajuan . ' telah dikirim ke email daftar approval',
            'UserCreate' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan & email notifikasi terkirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show($idPengajuan, $idPengajuanItem)
    {
        $data = PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getHtaGpa.getDetailHta' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem)->latest();
            },
            'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem)->latest();
            },
            'getJenisPermintaan.getForm',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);
        // dd($data);
        // Cek jika data vendor masih kosong, redirect back
        // if (empty($data->getVendor) || count($data->getVendor) < 2) {
        //     return redirect()->back()->with('error', 'Data vendor kurang dari 2. Data vendor belum lengkap.');
        // }

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->getHtaGpa->JenisForm)
            ->where('DokumenId', $data->getHtaGpa->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        // dd($approval);
        // Generate QR code untuk setiap approval yang approved
        foreach ($approval as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken ?? '0'))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }
        // dd($approval);
        $htagpa = null;
        if ($data->getHtaGpa) {
            DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $data->getHtaGpa->JenisForm)
                ->where('DokumenId', $data->getHtaGpa->id)
                ->orderBy('Urutan', 'asc')
                ->get();
            $htagpa = $data->getHtaGpa;
        }
        $parameter = MasterParameter::get();
        // dd($approval);
        if ($data->getHtaGpa->JenisForm == 2 || $data->getHtaGpa->JenisForm == 16) {
            return view('hta-gpa.umum.show', compact('data', 'parameter', 'approval', 'htagpa'));
        } else {
            if (auth()->user()->id == 12) {
                return view('hta-gpa.show-dr-ingen', compact('data', 'parameter', 'approval', 'htagpa'));
            } else {
                return view('hta-gpa.show', compact('data', 'parameter', 'approval', 'htagpa'));
            }
        }
    }

    public function print($idPengajuan, $idPengajuanItem)
    {
        $data = PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getJenisPermintaan.getForm',
            'getHtaGpa.getPenilai1',
            'getHtaGpa.getPenilai2',
            'getHtaGpa.getPenilai3',
            'getHtaGpa.getPenilai4',
            'getHtaGpa.getPenilai5',
            'getHtaGpa.getPenilai',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);

        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->getHtaGpa->JenisForm)
            ->where('DokumenId', $data->getHtaGpa->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        foreach ($approval2 as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken ?? '0'))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }

        $parameter = MasterParameter::get();

        $pdf = \PDF::loadView('hta-gpa.cetak-hta-gpa', compact('data', 'parameter', 'approval2'))
            ->setPaper('a4', 'landscape');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream('hta-gpa-' . $idPengajuan . '-' . $idPengajuanItem . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HtaDanGpa $htaDanGpa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HtaDanGpa $htaDanGpa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HtaDanGpa $htaDanGpa)
    {
        //
    }

    public function kirimUlangNotifikasi($id)
    {
        // dd($id);
        $htagpa = HtaDanGpa::find($id);
        // dd($htagpa);
        $idPengajuanItem = $htagpa->PengajuanItemId;
        $idPengajuan = $htagpa->IdPengajuan;
        if (!$htagpa) {
            return back()->with('error', 'Permintaan pembelian tidak ditemukan.');
        }

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $htagpa->JenisForm)
            ->where('DokumenId', $htagpa->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->get();
        if ($approval->count() == 0) {
            return back()->with('error', 'Semua dokumen sudah di-approve, tidak ada notifikasi dikirim ulang.');
        }
        // dd($approval);
        $pengajuan = PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getJenisPermintaan.getForm',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);
        $cariHTA = HtaDanGpa::with('getDetailHta')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();
        // dd($cariHTA);
        $parameter = MasterParameter::get();
        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $cariHTA->JenisForm)
            ->where('DokumenId', $cariHTA->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        foreach ($approval as $penilai) {
            try {
                if (empty($penilai->Email))
                    continue;
                $fileLampiran = [];
                if ($cariHTA->JenisForm == '2' || $cariHTA->JenisForm == '16') {
                    foreach ($cariHTA->getDetailHta as $detail) {
                        if (!empty($detail->File)) {
                            $fileLampiran[] = $detail->File;
                        }
                    }
                }

                Mail::to($penilai->Email)
                    ->bcc(env('MAIL_DEV_BCC'))
                    ->send(new NotifikasiPengajuanMail(
                        $pengajuan,
                        $cariHTA,
                        $parameter,
                        $penilai,
                        $approval2,
                        $fileLampiran,
                    ));
                $penilai->StatusEmail = 'Terkirim';
                $penilai->save();

                // $cariHTA->Status = 'Final';
                // $cariHTA->save();
            } catch (\Exception $e) {
                // dd($e);
                $penilai->StatusEmail = 'Gagal Kirim';
                $penilai->save();
            }
        }
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($htagpa)
                ->withProperties(['ip' => request()->ip()])
                ->log('Kirim Ulang Email Usulan: ' . ($htagpa->id ?? $htagpa->id));
        }
        // ini untuk kirim ulang notifikasi
        $pengajuan = PengajuanPembelian::find($idPengajuan);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($cariHTA->Nomor ?? $cariHTA->id);

        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'HTA-GPA',
            'Keterangan' => 'Notifikasi ulang HTA/GPA dengan nomor ' . $kodePengajuan . ' telah dikirim ulang ke email daftar approval',
            'UserCreate' => auth()->user()->name,
        ]);
        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim ulang.');
    }

    public function approve($token)
    {
        $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', $token)->firstOrFail();
        if (!$penilai) {
            return view('errors.proses-verifikasi');
        }
        if ($penilai->Status !== 'Pending') {
            return view('emails.setelah-approval', compact('penilai'))->with([
                'message' => 'Persetujuan sudah diproses sebelumnya.'
            ]);
        }

        if ($penilai->Status !== 'Pending') {
            return view('emails.setelah-approval', compact('penilai'))->with([
                'message' => 'Persetujuan sudah diproses sebelumnya.'
            ]);
        }

        $penilai->update([
            'Status' => 'Approved',
            'TanggalApprove' => Carbon::now(),
        ]);

        // Log aktivitas approval
        $pengajuan = null;
        $kodePengajuan = null;
        $hta = null;

        if ($penilai->DokumenId ?? false) {
            $hta = HtaDanGpa::find($penilai->DokumenId);
            if ($hta) {
                $pengajuan = PengajuanPembelian::find($hta->IdPengajuan);
                $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($hta->Nomor ?? $hta->id);
            }
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'HTA-GPA',
            'Keterangan' => ($penilai->Nama ?? '-') . ' telah menyetujui HTA/GPA',
            'UserCreate' => $penilai->Nama ?? '-',
        ]);

        // Cek approval selanjutnya
        $nextApproval = DokumenApproval::where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->where('Urutan', '>', $penilai->Urutan)
            ->orderBy('Urutan', 'asc')
            ->first();

        if ($nextApproval) {
            if (!empty($nextApproval->Email) && $nextApproval->UserId != 2) {
                $parameter = MasterParameter::get();
                $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $penilai->JenisFormId)
                    ->where('DokumenId', $penilai->DokumenId)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $fileLampiran = [];
                if ($hta && ($hta->JenisForm == '2' || $hta->JenisForm == '16')) {
                    foreach ($hta->getDetailHta as $detail) {
                        if (!empty($detail->File)) {
                            $fileLampiran[] = $detail->File;
                        }
                    }
                }

                try {
                    Mail::to($nextApproval->Email)
                        ->bcc(env('MAIL_DEV_BCC'))
                        ->send(new NotifikasiPengajuanMail(
                            $pengajuan,
                            $hta,
                            $parameter,
                            $nextApproval,
                            $approval2,
                            $fileLampiran
                        ));
                    $nextApproval->StatusEmail = 'Terkirim';
                    $nextApproval->save();
                } catch (\Exception $e) {
                    $nextApproval->StatusEmail = 'Gagal Kirim';
                    $nextApproval->save();
                }
            }
        } else {
            // JIKA TIDAK ADA approval selanjutnya: Update status utama
            if ($hta) {
                $hta->Status = 'Disetujui';
                $hta->save();
            }
        }

        return view('emails.setelah-approval', compact('penilai'))->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }

    public function reject($token)
    {
        $penilai = PenilaiHtaGpa::where('ApprovalToken', $token)->firstOrFail();
        if (!is_null($penilai->StatusAcc)) {
            return view('emails.setelah-approval', [
                'message' => 'Persetujuan sudah diproses sebelumnya.'
            ]);
        }
        $penilai->update([
            'StatusAcc' => 'N',
            'AccPada' => Carbon::now(),
        ]);

        return view('emails.setelah-approval', [
            'message' => 'Penilaian telah ditolak.'
        ]);
    }

    public function sebelumApprove($token)
    {
        $penilai = DokumenApproval::with(['getDokumenHTAGPA.getPengajuan'])
            ->where('ApprovalToken', $token)
            ->first();
        if (!$penilai) {
            return view('errors.proses-verifikasi');
        }
        $ListApproval = DokumenApproval::with(['getDokumenHTAGPA.getPengajuan'])
            ->where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->get();
        // Cek apakah sudah diapprove/direject sebelumnya
        if ($penilai->Status !== 'Pending') {
            return view('emails.setelah-approval', compact('penilai'))->with([
                'message' => 'Persetujuan sudah diproses sebelumnya dengan status: ' . $penilai->Status
            ]);
        }

        return view('emails.sebelum-approve', compact('penilai', 'ListApproval'));
    }

    public function submitJustifikasi(Request $request, $token)
    {
        $penilai = DokumenApproval::with('getDokumenHTAGPA')->where('ApprovalToken', $token)->first();
        if (!$penilai) {
            return view('errors.proses-verifikasi');
        }
        if (!$penilai || $penilai->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Approval sudah diproses sebelumnya.');
        }
        $duplicatePenilai = DokumenApproval::where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->where('UserId', $penilai->UserId)
            ->where('Status', 'Pending')
            ->get();

        foreach ($duplicatePenilai as $toApprove) {
            $toApprove->update([
                'Justifikasi' => $request->justifikasi,
                'Status' => 'Approved',
                'TanggalApprove' => Carbon::now(),
            ]);
        }
        $penilai = $penilai->fresh();
        $hta = null;
        $pengajuan = null;
        $kodePengajuan = null;

        if ($penilai->DokumenId ?? false) {
            $hta = HtaDanGpa::find($penilai->DokumenId);
            if ($hta) {
                $pengajuan = PengajuanPembelian::find($hta->IdPengajuan);
                $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($hta->Nomor ?? $hta->id);
            }
        }

        // PERMINTAAN
        $permintaan = PermintaanPembelian::with([
            'getDetail.getBarang.getMerk',
            'getDiajukanOleh',
            'getDetail.getBarang.getSatuan'
        ])->find($pengajuan->IdPermintaan ?? null);

        $ApprovalPermintaan = collect();
        if ($permintaan) {
            $approval3 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $permintaan->JenisForm)
                ->where('DokumenId', $permintaan->id)
                ->orderBy('Urutan', 'asc')
                ->get();

            // Generate QR code untuk setiap approval
            foreach ($ApprovalPermintaan as $item) {
                if ($item->Status == 'Approved') {
                    $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                        ->setSize(80)
                        ->setMargin(10);

                    $writer = new PngWriter();
                    $result = $writer->write($qrCode);

                    $item->qrCode = base64_encode($result->getString());
                }
            }
        }
        // Log aktivitas
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'HTA-GPA',
            'Keterangan' => ($penilai->Nama ?? '-') . ' telah menyetujui HTA/GPA',
            'UserCreate' => $penilai->Nama ?? '-',
        ]);

        $nextApproval = DokumenApproval::where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->where('Urutan', '>', $penilai->Urutan)
            ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->first();

        if ($nextApproval) {
            // $this->savePdfToStorage($pengajuan->id, $pengajuan->PengajuanItemId);
            $this->pdfGenerator->generateAll($pengajuan->id);
            if (!empty($nextApproval->Email)) {
                $parameter = MasterParameter::get();
                $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $penilai->JenisFormId)
                    ->where('DokumenId', $penilai->DokumenId)
                    ->orderBy('Urutan', 'asc')
                    ->get();
                $fileLampiran = [];
                if ($hta && ($hta->JenisForm == '2' || $hta->JenisForm == '16')) {
                    foreach ($hta->getDetailHta as $detail) {
                        if (!empty($detail->File)) {
                            $fileLampiran[] = $detail->File;
                        }
                    }
                }

                try {
                    Mail::to($nextApproval->Email)
                        ->bcc(env('MAIL_DEV_BCC'))
                        ->send(new NotifikasiPengajuanMail(
                            $pengajuan,
                            $hta,
                            $parameter,
                            $nextApproval,
                            $approval2,
                            $fileLampiran,
                            $ApprovalPermintaan,
                            $permintaan
                        ));
                    $nextApproval->StatusEmail = 'Terkirim';
                    $nextApproval->save();
                } catch (\Exception $e) {
                    $nextApproval->StatusEmail = 'Gagal Kirim';
                    $nextApproval->save();
                }
            }
        } else {
            if ($hta) {
                // $hta->Status = 'Final';
                // $hta->save();
            }
        }

        // $this->savePdfToStorage($pengajuan->id, $pengajuan->PengajuanItemId);
        $this->pdfGenerator->generateAll($pengajuan->id);
        return view('emails.setelah-approval', compact('penilai'))->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }

    private function savePdfToStorage($idPengajuan, $idPengajuanItem)
    {
        $data = PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getHtaGpa',
            'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getJenisPermintaan.getForm',
            'getHtaGpa.getPenilai1',
            'getHtaGpa.getPenilai2',
            'getHtaGpa.getPenilai3',
            'getHtaGpa.getPenilai4',
            'getHtaGpa.getPenilai5',
            'getHtaGpa.getPenilai',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);

        if (!$data || !$data->getHtaGpa) {
            return null;
        }

        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->getHtaGpa->JenisForm)
            ->where('DokumenId', $data->getHtaGpa->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        foreach ($approval2 as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken ?? '0'))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }

        $parameter = MasterParameter::get();

        // Generate PDF HTA-GPA
        $pdf = \PDF::loadView('hta-gpa.cetak-hta-gpa', compact('data', 'parameter', 'approval2'))
            ->setPaper('a4', 'landscape');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $htaGpaOutput = $pdf->output();

        // ==========================================
        // PERSIAPAN PATH
        // ==========================================
        $dirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
        $fullDirPath = storage_path('app/' . $dirPath);

        // Pastikan direktori ada
        if (!file_exists($fullDirPath)) {
            mkdir($fullDirPath, 0777, true);
        }

        // Simpan PDF HTA-GPA sementara
        $htaGpaTempPath = $fullDirPath . '/hta-gpa-temp-' . $idPengajuan . '.pdf';
        file_put_contents($htaGpaTempPath, $htaGpaOutput);

        $idPermintaan = $data->IdPermintaan ?? null;
        $permintaanFullPath = null;

        if ($idPermintaan) {
            $permintaanFileName = 'permintaan_' . $idPermintaan . '.pdf';
            $permintaanFullPath = storage_path('app/public/rekap-file/permintaan/' . $permintaanFileName);

            if (!file_exists($permintaanFullPath)) {
                $permintaanFullPath = null;
            }
        }

        // ==========================================
        // GABUNGKAN PDF (JIKA PERMINTAAN ADA)
        // ==========================================
        if ($permintaanFullPath) {
            try {
                $combinedPdf = new \setasign\Fpdi\Tcpdf\Fpdi();

                // A. Tambahkan halaman dari PDF Permintaan (halaman awal)
                $pageCount = $combinedPdf->setSourceFile($permintaanFullPath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $combinedPdf->importPage($i);
                    $size = $combinedPdf->getTemplateSize($tplIdx);
                    $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $combinedPdf->useTemplate($tplIdx);
                }

                // B. Tambahkan halaman dari PDF HTA-GPA (halaman berikutnya)
                $pageCount = $combinedPdf->setSourceFile($htaGpaTempPath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $combinedPdf->importPage($i);
                    $size = $combinedPdf->getTemplateSize($tplIdx);
                    $combinedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $combinedPdf->useTemplate($tplIdx);
                }

                // Output ke buffer (hindari error path TCPDF di Windows)
                $combinedContent = $combinedPdf->Output('', 'S');

                // Simpan hasil gabungan
                $pdfFileName = 'hta-gpa-' . $idPengajuan . '.pdf';
                $storagePath = $dirPath . '/' . $pdfFileName;
                Storage::put($storagePath, $combinedContent);

                // Hapus file temporary
                if (file_exists($htaGpaTempPath)) {
                    unlink($htaGpaTempPath);
                }

                return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
            } catch (\Exception $e) {
                Log::error('Error combining PDF: ' . $e->getMessage());

                // Fallback: jika gagal combine, simpan PDF HTA-GPA saja
                $pdfFileName = 'hta-gpa-' . $idPengajuan . '.pdf';
                $storagePath = $dirPath . '/' . $pdfFileName;
                Storage::put($storagePath, $htaGpaOutput);

                if (file_exists($htaGpaTempPath)) {
                    unlink($htaGpaTempPath);
                }

                return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
            }
        } else {
            $pdfFileName = 'hta-gpa-' . $idPengajuan . '.pdf';
            $storagePath = $dirPath . '/' . $pdfFileName;
            Storage::put($storagePath, $htaGpaOutput);

            if (file_exists($htaGpaTempPath)) {
                unlink($htaGpaTempPath);
            }

            return 'storage/rekap-file/pengajuan-' . $idPengajuan . '/' . $pdfFileName;
        }
    }
}
