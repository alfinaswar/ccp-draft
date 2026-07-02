<?php

namespace App\Http\Controllers;

use App\Mail\NotifApprovalPresentasi;
use App\Mail\NotifFui;
use App\Models\AktivitasPengajuan;
use App\Models\DokumenApproval;
use App\Models\MasterBarang;
use App\Models\MasterDepartemen;
use App\Models\MasterForm;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use App\Models\Rekomendasi;
use App\Models\User;
use App\Models\UsulanInvestasi;
use App\Models\UsulanInvestasiDetail;
use Carbon\Carbon;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UsulanInvestasiController extends Controller
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
    public function create($IdPengajuan, $PengajuanItemId)
    {
        $IdPengajuan = decrypt($IdPengajuan);
        $PengajuanItemId = decrypt($PengajuanItemId);
        // dd($PengajuanItemId);
        $pengajuanItem = PengajuanItem::find($PengajuanItemId);
        if (!$pengajuanItem) {
            return redirect()->back()->withErrors(['Pengajuan item tidak ditemukan.']);
        }
        $IdBarang = $pengajuanItem->IdBarang;
        $data = PengajuanPembelian::with([
            'getVendor.getNamaVendor',
            'getVendor.getVendorDetail' => function ($query) use ($IdBarang) {
                $query->where('NamaBarang', $IdBarang);
            }
        ])->find($IdPengajuan);
        // dd($data);
        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($PengajuanItemId);
        $vendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $PengajuanItemId)
            ->whereNotNull('DisetujuiPada')
            ->first();
        // dd($vendorAcc);

        $namaUser = auth()->user()->name ?? 'User';
        if (is_null($vendorAcc) || is_null($vendorAcc->getRekomedasiDetail) || count($vendorAcc->getRekomedasiDetail) == 0) {
            return redirect()->back()->with('error', "Hai $namaUser, Maaf, CCP belum menentukan pilihan vendor.");
        }
        $Acc = $vendorAcc->getRekomedasiDetail[0]->IdVendor;
        $NamaBarangAcc = $vendorAcc->getRekomedasiDetail[0]->NamaPermintaan;
        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $IdPengajuan)->first();
        // dd(123);
        $departemen = MasterDepartemen::get();
        $user = User::get();
        $barang = MasterBarang::get();

        $usulan = UsulanInvestasi::where('IdPengajuan', $IdPengajuan)
            ->where('PengajuanItemId', $PengajuanItemId)
            ->first();
        // dd($usulan);
        $approval = null;
        if ($usulan !== null) {
            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $usulan->JenisForm)
                ->where('DokumenId', $usulan->id)
                ->orderBy('Urutan', 'asc')
                ->get();
        } else {
            $approval = null;
        }
        return view('form-usulan-investari.create', compact('barang', 'user', 'departemen', 'data', 'dataRekom', 'PengajuanItemId', 'approval', 'usulan'));
    }

    public function kirimUlangNotifikasi($id)
    {
        // dd($id);
        $usulan = UsulanInvestasi::with('getBarang', 'getPerusahaan')->find($id);
        // dd($usulan);
        $cekjenis = PengajuanPembelian::find($usulan->IdPengajuan);
        $barang = $cekjenis->getPengajuanItem[0]->id;
        if (!$usulan) {
            return back()->with('error', 'Permintaan pembelian tidak ditemukan.');
        }
        $VendorAcc = UsulanInvestasiDetail::with('getVendorDipilih')->where('idUsulan', $usulan->id)->where('Vendor', $usulan->VendorDipilih)->first();
        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $usulan->IdPengajuan)->first();
        // dd($dataRekom);
        $VendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $barang)
            ->first();
        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($barang);
        $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor;
        $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan;
        // dd($NamaBarangAcc);
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
        ])->find($usulan->IdPengajuan);
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->get();
        if ($approval->count() == 0) {
            return back()->with('error', 'Semua dokumen sudah di-approve, tidak ada notifikasi dikirim ulang.');
        }
        // dd($approval);
        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        foreach ($approval as $penilai) {
            try {
                if (!filter_var($penilai->Email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                if ($penilai->UserId == 81) {
                    continue;
                }

                if (empty($penilai->Email))
                    continue;
                Mail::to($penilai->Email)
                    ->send(new NotifFui(
                        $usulan,
                        $VendorAcc,
                        $penilai,
                        $approval2,
                        $dataRekom,
                        $data2
                    ));
                $penilai->StatusEmail = 'Terkirim';
                $penilai->save();
            } catch (\Exception $e) {
                Log::error('Email gagal: ' . $penilai->Email);
                Log::error($e->getMessage());
                $penilai->StatusEmail = 'Gagal Kirim';
                $penilai->save();
            }
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $cekjenis->KodePengajuan ?? null,
            'Jenis' => 'FUI',
            'Keterangan' => 'Pengiriman ulang notifikasi Form Usulan Investasi (FUI) untuk nomor pengajuan ' . ($cekjenis->KodePengajuan ?? '-') . ' telah dilakukan.',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($usulan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Kirim Ulang Email Usulan: ' . ($usulan->id ?? $usulan->id));
        }
        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim ulang.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'IdPengajuan' => 'required|integer',
            'PengajuanItemId' => 'required|integer',
            'Tanggal' => 'required|date',
            'Divisi' => 'nullable|integer',
            'NamaKadiv' => 'nullable|integer',
            'Kategori' => 'nullable|string',
            'Tanggal2' => 'nullable|date',
            'Divisi2' => 'nullable|integer',
            'NamaKadiv2' => 'nullable|integer',
            'Kategori2' => 'nullable|string',
            'Alasan' => 'nullable|string',
            'items' => 'required|array',
        ]);
        // dd($request->all());
        $cekjenis = PengajuanPembelian::find($request->IdPengajuan);
        $barang = $cekjenis->getPengajuanItem[0]->id;
        // Calculate total from itemAcc[0]['Total'] (string like "330.000.000"), numeric only
        $totalNumeric = null;
        if (isset($request->itemAcc[0]['Total'])) {
            $totalNumeric = preg_replace('/[^0-9]/', '', $request->itemAcc[0]['Total']);
        }
        $jenisForm = null;
        if ($cekjenis->Jenis == 1) {
            if ($totalNumeric !== null && $totalNumeric !== '') {
                if ($totalNumeric < 50000000) {
                    $jenisForm = '7';
                } elseif ($totalNumeric >= 50000000 && $totalNumeric <= 100000000) {
                    $jenisForm = '11';
                } elseif ($totalNumeric > 100000000) {
                    $jenisForm = '12';
                }
            }
        } else {
            if ($totalNumeric !== null && $totalNumeric !== '') {
                if ($totalNumeric < 50000000) {
                    $jenisForm = '14';
                } elseif ($totalNumeric >= 50000000 && $totalNumeric <= 100000000) {
                    $jenisForm = '15';
                } elseif ($totalNumeric > 100000000) {
                    $jenisForm = '13';
                }
            }
            // dd($jenisForm . 'else');
        }
        // dd($jenisForm . 'akhir');
        $usulan = UsulanInvestasi::with('getBarang', 'getPerusahaan')->updateOrCreate(
            [
                'IdPengajuan' => $request->IdPengajuan ?? null,
                'PengajuanItemId' => $request->PengajuanItemId ?? null,
            ],
            [
                'JenisForm' => $jenisForm,
                'IdVendor' => isset($request->itemAcc[0]['Vendor']) ? $request->itemAcc[0]['Vendor'] : null,
                'IdBarang' => isset($request->itemAcc[0]['NamaBarang']) ? $request->itemAcc[0]['NamaBarang'] : null,
                'Tanggal' => $request->Tanggal ?? null,
                'NamaKadiv' => $request->NamaKadiv ?? null,
                'Divisi' => $request->Divisi ?? null,
                'Kategori' => $request->Kategori ?? null,
                'Tanggal2' => $request->Tanggal2 ?? null,
                'NamaKadiv2' => $request->NamaKadiv2 ?? null,
                'Divisi2' => $request->Divisi2 ?? null,
                'Kategori2' => $request->Kategori2 ?? null,
                'Alasan' => $request->Alasan ?? null,
                'BiayaAkhir' => isset($request->items[0]['Total']) ? preg_replace('/[^0-9]/', '', $request->items[0]['Total']) : 0,
                'VendorDipilih' => isset($request->itemAcc[0]['Vendor']) ? $request->itemAcc[0]['Vendor'] : 0,
                'HargaDiskonPpn' => 0,
                'Total' => isset($request->items[0]['Total']) ? preg_replace('/[^0-9]/', '', $request->items[0]['Total']) : 0,
                'SudahRkap' => $request->SudahRkap ?? null,
                'SisaBudget' => isset($request->SisaBudget) ? preg_replace('/[^0-9]/', '', $request->SisaBudget) : null,
                'SudahRkap2' => $request->SudahRkap2 ?? null,
                'SisaBudget2' => isset($request->SisaBudget2) ? preg_replace('/[^0-9]/', '', $request->SisaBudget2) : null,
                'DiajukanOleh' => auth()->user()->id ?? null,
                'KodePerusahaan' => auth()->user()->kodeperusahaan ?? null,
                'DiajukanPada' => now(),
            ]
        );

        if (!empty($request->items) && is_array($request->items)) {
            UsulanInvestasiDetail::where('IdUsulan', $usulan->id ?? null)->delete();
            foreach ($request->items as $item) {
                UsulanInvestasiDetail::create([
                    'IdUsulan' => $usulan->id ?? null,
                    'NamaBarang' => $item['NamaBarang'] ?? null,
                    'Vendor' => isset($request->items[0]['Vendor']) ? $request->itemAcc[0]['Vendor'] : 0,
                    'Jumlah' => 1,
                    'Harga' => isset($item['Harga']) ? preg_replace('/[^0-9]/', '', $item['Harga']) : null,
                    'HargaNego' => isset($item['HargaNego']) ? preg_replace('/[^0-9]/', '', $item['HargaNego']) : null,
                    'Diskon' => 0,
                    'Ppn' => 0,
                    'Total' => isset($item['Total']) ? preg_replace('/[^0-9]/', '', $item['Total']) : null,
                    'UserCreate' => auth()->user()->name ?? null,
                    'UserUpdate' => null,
                ]);
            }
        }
        // dd($usulan->JenisForm);

        $Form = MasterForm::with([
            'getApproval' => function ($q) use ($usulan) {
                $q->where('KodePerusahaan', $usulan->KodePerusahaan);
            },
            'getApproval.getUser'
        ])
            ->where('id', $usulan->JenisForm)
            ->first();
        // dd($Form);
        foreach ($Form->getApproval as $approvalSetting) {
            $approval = DokumenApproval::updateOrCreate(
                [
                    'JenisFormId' => $usulan->JenisForm,
                    'DokumenId' => $usulan->id,
                    'Urutan' => $approvalSetting->Urutan ?? null,
                ],
                [
                    'JenisUser' => $approvalSetting->JenisUser ?? 'Master',
                    'DepartemenId' => $approvalSetting->Departemen ?? null,
                    'PerusahaanId' => $approvalSetting->KodePerusahaan,
                    'JabatanId' => $approvalSetting->JabatanId ?? null,
                    'UserId' => $approvalSetting->UserId ?? null,
                    'Nama' => $approvalSetting->getUser->name ?? null,
                    'Email' => $approvalSetting->getUser->email ?? null,
                    'Status' => 'Pending',
                    'TanggalApprove' => null,
                    'ApprovalToken' => str_replace('-', '', Str::uuid()),
                    'Catatan' => null,
                    'Ttd' => null,
                    'UserCreate' => auth()->user()->name,
                ]
            );
        }
        $approvalDocs = DokumenApproval::where([
            'JenisFormId' => $usulan->JenisForm,
            'DokumenId' => $usulan->id,
        ])->orderBy('Urutan', 'asc')->get();
        // dd($approvalDocs);
        $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        // dd($approvalDocs);
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

        // note: sudah simpan penilai ke dokumen approvals pada bagian atas, tidak perlu kirim email

        $idPengajuan = $request->IdPengajuan;
        $idPengajuanItem = $request->PengajuanItemId;

        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $idPengajuan)->first();
        $VendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $barang)
            ->first();
        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($barang);
        $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor ?? null;
        $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan ?? null;
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
        ])->find($request->IdPengajuan);

        // Tidak kirim email ke penilai di sini, hanya membuat dokumen approvals
        // $approval2 sudah berisi penilai, tidak perlu kirim email NotifFui di sini

        $pengajuan = PengajuanPembelian::find($idPengajuan);
        // dd($pengajuan);
        $this->savePdfToStorage($pengajuan->id, $pengajuan->id);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'FUI',
            'Keterangan' => 'Pembuatan Form Usulan Investasi (FUI) untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' sudah dibuat',
            'UserCreate' => auth()->user()->name,
        ]);
        activity('usulan_investasi')
            ->causedBy(auth()->user())
            ->performedOn($usulan)
            ->withProperties([
                'attributes' => $usulan->toArray()
            ])
            ->log('Memperbarui data Usulan Investasi dengan kode ' . ($cekjenis->NomorPengajuan ?? $usulan->id));
        return redirect()->back()->with('success', 'Usulan Investasi berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($IdPengajuan, $barang)
    {
        $usulan = UsulanInvestasi::with('getFuiDetail', 'getBarang', 'getVendor', 'getAccDirektur', 'getAccKadiv')
            ->where('IdPengajuan', $IdPengajuan)
            ->where('PengajuanItemId', $barang)
            ->first();
        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $IdPengajuan)->first();
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
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
        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($barang);
        $vendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $barang)
            ->first();
        // dd($vendorAcc);
        $Acc = $vendorAcc->getRekomedasiDetail[0]->IdVendor;
        $NamaBarangAcc = $vendorAcc->getRekomedasiDetail[0]->NamaPermintaan;
        // dd($NamaBarangAcc);
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
        ])->find($IdPengajuan);
        // dd($dataRekom);
        return view('form-usulan-investari.show', compact('usulan', 'approval', 'data2', 'dataRekom'));
    }

    public function print($IdPengajuan, $barang)
    {
        // dd($barang);
        $usulan = UsulanInvestasi::with('getFuiDetail.getVendor', 'getBarang', 'getVendor', 'getAccDirektur', 'getAccKadiv', 'getDepartemen', 'getDepartemen2', 'getNamaForm')
            ->where('IdPengajuan', $IdPengajuan)
            ->where('PengajuanItemId', $barang)
            ->first();
        // dd($usulan);
        $VendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $barang)
            ->first();
        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $IdPengajuan)->first();
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        // dd($approval);
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
        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($barang);
        $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor;
        $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan;
        // dd($NamaBarangAcc);
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
        ])->find($IdPengajuan);
        // dd($data2);
        $pdf = \PDF::loadView('form-usulan-investari.show-pdf', compact('usulan', 'VendorAcc', 'approval', 'data2', 'dataRekom'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,  // agar support gambar dari url/fil
                'defaultFont' => 'sans-serif',
            ])
            ->setPaper('a4', 'portrait');
        return $pdf->stream('Usulan_Investasi_' . $IdPengajuan . '_' . $barang . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($IdPengajuan, $barang)
    {
        $IdPengajuan = decrypt($IdPengajuan);
        $PengajuanItemId = decrypt($barang);
        $usulan = UsulanInvestasi::with('getFuiDetail', 'getBarang', 'getVendor', 'getAccDirektur', 'getAccKadiv')
            ->where('IdPengajuan', $IdPengajuan)
            ->where('PengajuanItemId', $PengajuanItemId)
            ->first();

        $user = user::where('kodeperusahaan', auth()->user()->kodeperusahaan);
        $departemen = MasterDepartemen::get();
        return view('form-usulan-investari.edit', compact('IdPengajuan', 'user', 'departemen', 'PengajuanItemId', 'usulan'));
    }

    public function approveKadiv(Request $request)
    {
        $usulan = UsulanInvestasi::find($request->id);
        if (!$usulan) {
            return redirect()->back()->with('error', 'Usulan Investasi tidak ditemukan.');
        }
        // dd($usulan);
        $usulan->KadivJangMed = auth()->user()->id;
        $usulan->KadivJangMedPada = now();
        $usulan->save();

        return redirect()->back()->with('success', 'Usulan Investasi telah disetujui oleh Kadiv.');
    }

    public function approveDirektur(Request $request)
    {
        $usulan = UsulanInvestasi::find($request->id);
        if (!$usulan) {
            return redirect()->back()->with('error', 'Usulan Investasi tidak ditemukan.');
        }
        $usulan->Direktur = auth()->user()->id;
        $usulan->DirekturPada = now();
        $usulan->save();

        return redirect()->back()->with('success', 'Usulan Investasi telah disetujui oleh Direktur.');
    }

    // public function approve($token)
    // {
    //     // dd($token);
    //     $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', $token)->first();
    //     if ($penilai->Status !== 'Pending') {
    //         return view('emails.setelah-approval', compact('penilai'))->with([
    //             'message' => 'Persetujuan sudah diproses sebelumnya.'
    //         ]);
    //     }

    //     $penilai->update([
    //         'Status' => 'Approved',
    //         'TanggalApprove' => Carbon::now(),
    //     ]);

    //     return view('emails.setelah-approval', compact('penilai'))->with([
    //         'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
    //     ]);
    // }
    public function approve($token)
    {
        $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', $token)->firstOrFail();

        if ($penilai->UserId == 81) {
            $usulan = UsulanInvestasi::find($penilai->DokumenId);
            $kodePengajuan = null;
            if ($usulan) {
                $pengajuan = PengajuanPembelian::find($usulan->IdPengajuan);
                if ($pengajuan) {
                    $kodePengajuan = $pengajuan->KodePengajuan;
                    $pengajuan->AccCeo = 'Y';
                    $pengajuan->Status = 'Disetujui CEO';
                    $pengajuan->TanggalAccCeo = Carbon::now();
                    $pengajuan->save();
                } else {
                    $kodePengajuan = $usulan->id ?? null;
                }
            }

            $penilai->update([
                'Status' => 'Approved',
                'TanggalApprove' => Carbon::now(),
            ]);

           AktivitasPengajuan::create([
                'KodePengajuan' => $kodePengajuan ?? null,
                'Jenis' => 'Persetujuan CEO',
                'Keterangan' => 'Arfan Awaloeddin (CEO) telah menyetujui Dokumen dengan Nomor Pengajuan: ' . ($kodePengajuan ?? '-'),
                'UserCreate' => 'Arfan Awaloeddin',
            ]);

            if (function_exists('activity')) {
                activity('approval_fui_ceo')
                    ->causedBy($penilai->UserId)
                    ->withProperties([
                        'approval_token' => $token,
                        'keterangan' => 'CEO Menyetujui Dokumen dengan Nomor pengajuan: ' . $kodePengajuan,
                    ])
                    ->log('CEO Menyetujui Dokumen dengan Nomor pengajuan: ' . $kodePengajuan);
            }
        }
        $penilai->update([
            'Status' => 'Approved',
            'TanggalApprove' => Carbon::now(),
        ]);

        $pengajuan = null;
        $kodePengajuan = null;
        if ($penilai->DokumenId ?? false) {
            $usulan = UsulanInvestasi::find($penilai->DokumenId);
            if ($usulan) {
                $pengajuan = PengajuanPembelian::find($usulan->IdPengajuan);
                $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($usulan->id ?? null);
            }
        }
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'FUI',
            'Keterangan' => ($penilai->Nama ?? '-') . ' telah menyetujui Form Usulan Investasi (FUI)',
            'UserCreate' => $penilai->Nama ?? '-',
        ]);

        $approvalSelanjutnya =DokumenApproval::where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->where('Urutan', '>', $penilai->Urutan)
            ->where('Status', 'Pending')
            ->orderBy('Urutan')
            ->first();

        if ($approvalSelanjutnya) {
            // Kirim notifikasi email ke approval selanjutnya (contoh trigger event/email)
            try {
                if ($approvalSelanjutnya->Email) {
                    Mail::to($approvalSelanjutnya->Email)
                        ->send(new NotifApprovalPresentasi(
                            UsulanInvestasi::find($approvalSelanjutnya->DokumenId),
                            $pengajuan,
                            $approvalSelanjutnya
                        ));
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi approval selanjutnya FUI: ' . $e->getMessage());
            }
        }

        return view('emails.setelah-approval', compact('penilai'))->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }

    public function reject($token)
    {
        $penilai = DokumenApproval::where('ApprovalToken', $token)->firstOrFail();
        if (!is_null($penilai->StatusAcc)) {
            return view('emails.setelah-approval', [
                'message' => 'Persetujuan sudah diproses sebelumnya.'
            ]);
        }
        $penilai->update([
            'Status' => 'Rejected',
            'TanggalApprove' => Carbon::now(),
        ]);

        return view('emails.setelah-approval', [
            'message' => 'Penilaian telah ditolak.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UsulanInvestasi $UsulanInvestasi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UsulanInvestasi $UsulanInvestasi)
    {
        //
    }

    private function savePdfToStorage($IdPengajuan, $barang)
    {
        // dd($IdPengajuan);
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
            ->where('IdPengajuan', $IdPengajuan)
            ->first();

        if (!$usulan) {
            return null;
        }
        // dd($usulan);
        $VendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $barang)
            ->first();

        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')
            ->where('IdPengajuan', $IdPengajuan)
            ->first();

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        // Generate QR Code untuk approval yang approved
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

        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($barang);
        $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor ?? null;
        $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan ?? null;

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
        ])->find($IdPengajuan);

        // Generate PDF FUI
        $pdf = \PDF::loadView('form-usulan-investari.show-pdf', compact('usulan', 'VendorAcc', 'approval', 'data2', 'dataRekom'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ])
            ->setPaper('a4', 'portrait');

        // ==========================================
        // 2. PERSIAPAN FOLDER & SIMPAN PDF FUI SEMENTARA
        // ==========================================
        $dirPath = 'public/rekap-file/pengajuan-' . $IdPengajuan;
        $fullDirPath = storage_path('app/' . $dirPath);
        if (!file_exists($fullDirPath)) {
            mkdir($fullDirPath, 0777, true);
        }

        // Simpan PDF FUI sementara
        $fuiTempPath = $fullDirPath . '/fui-temp-' . $IdPengajuan . '-' . $barang . '.pdf';
        file_put_contents($fuiTempPath, $pdf->output());

        // ==========================================
        // 3. GABUNGKAN PDF: FS + FUI (FUI DI AKHIR)
        // ==========================================
        $combinedPdf = new \setasign\Fpdi\Tcpdf\Fpdi();

        // Path PDF FS yang sudah ada di storage
        $fsPath = $fullDirPath . '/fs-' . $IdPengajuan . '.pdf';

        // Daftar file PDF yang akan digabungkan
        $pdfFilesToMerge = [];

        // 1. PDF FS (Halaman Awal) - jika ada
        if (file_exists($fsPath)) {
            $pdfFilesToMerge[] = $fsPath;
        }

        // 2. PDF FUI (Halaman Paling Akhir) - yang baru di-generate
        $pdfFilesToMerge[] = $fuiTempPath;

        // Proses penggabungan
        foreach ($pdfFilesToMerge as $pdfFile) {
            try {
                $pageCount = $combinedPdf->setSourceFile($pdfFile);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $combinedPdf->importPage($i);
                    $size = $combinedPdf->getTemplateSize($tplIdx);

                    $combinedPdf->AddPage(
                        $size['orientation'],
                        [$size['width'], $size['height']]
                    );
                    $combinedPdf->useTemplate($tplIdx);
                }
            } catch (\Exception $e) {
                \Log::error('Error merging PDF FUI: ' . $e->getMessage() . ' - File: ' . $pdfFile);
            }
        }

        // ==========================================
        // 4. SIMPAN HASIL COMBINE & CLEANUP
        // ==========================================
        $finalFileName = 'fui-' . $IdPengajuan . '.pdf';
        $finalPath = $fullDirPath . '/' . $finalFileName;
        $finalPath = str_replace('\\', '/', $finalPath);
        $combinedPdf->Output($finalPath, 'F');
        if (file_exists($fuiTempPath)) {
            unlink($fuiTempPath);
        }
        return 'storage/rekap-file/pengajuan-' . $IdPengajuan . '/' . $finalFileName;
    }
}
