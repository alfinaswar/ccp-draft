<?php

namespace App\Http\Controllers;

use App\Mail\NotifikasiApproval;
use App\Mail\NotifikasiPermintaanPembelian;
use App\Models\DokumenApproval;
use App\Models\MasterBarang;
use App\Models\MasterDepartemen;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterPerusahaan;
use App\Models\MasterSatuan;
use App\Models\PermintaanPembelian;
use App\Models\PermintaanPembelianDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

use Yajra\DataTables\Facades\DataTables;

class PermintaanPembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->ajax()) {
            $user = auth()->user();
            if ($user->hasRole('Admin') || $user->hasRole('CEO') || $user->hasRole('Group Head') || $user->hasRole('CCP')) {
                $query = PermintaanPembelian::with([
                    'getJenisPermintaan',
                    'getDetail',
                    'getPerusahaan',
                    'getDepartemen',
                    'getDiajukanOleh',
                    'getNotifApproval' => function ($q) use ($userId) {
                        $q->where('UserId', $userId)
                            ->where('Status', 'Pending');
                    }
                ])
                    ->orderBy('id', 'desc');
                if ($request->filled('jenis')) {
                    $query->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $query->where('Status', $request->status);
                }
                if ($request->filled('rs')) {
                    $query->where('KodePerusahaan', $request->rs);
                }
            } elseif ($user->hasRole('SMI')) {
                $query = PermintaanPembelian::with([
                    'getJenisPermintaan',
                    'getDetail',
                    'getPerusahaan',
                    'getDepartemen',
                    'getDiajukanOleh',
                    'getNotifApproval' => function ($q) use ($userId) {
                        $q->where('UserId', $userId)
                            ->where('Status', 'Pending');
                    }
                ])
                    ->where('Jenis', 1)
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->orderBy('id', 'desc');
                if ($request->filled('status')) {
                    $query->where('Status', $request->status);
                }
            } elseif ($user->hasRole('LOGUM')) {
                $query = PermintaanPembelian::with([
                    'getJenisPermintaan',
                    'getDetail',
                    'getPerusahaan',
                    'getDepartemen',
                    'getDiajukanOleh',
                    'getNotifApproval' => function ($q) use ($userId) {
                        $q->where('UserId', $userId)
                            ->where('Status', 'Pending');
                    }
                ])
                    ->where('Jenis', '!=', 1)
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->orderBy('id', 'desc');

                if ($request->filled('status')) {
                    $query->where('Status', $request->status);
                }
            } else {
                $query = PermintaanPembelian::with([
                    'getJenisPermintaan',
                    'getDetail',
                    'getPerusahaan',
                    'getDepartemen',
                    'getDiajukanOleh',
                    'getNotifApproval' => function ($q) use ($userId) {
                        $q->where('UserId', $userId)
                            ->where('Status', 'Pending');
                    }
                ])
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->orderBy('id', 'desc');
                if ($request->filled('jenis')) {
                    $query->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $query->where('Status', $request->status);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('Departemen', function ($row) {
                    return optional($row->getDepartemen)->Nama;
                })
                ->editColumn('DiajukanOleh', function ($row) {
                    return optional($row->getDiajukanOleh)->name;
                })
                ->addColumn('NamaBarang', function ($row) {
                    if ($row->getDetail && $row->getDetail->count() > 0) {
                        $namaMerkTipe = $row->getDetail->map(function ($detail) {
                            $barang = $detail->getBarang;
                            $nama = optional($barang)->Nama;
                            $merk = optional($barang && $barang->getMerk)->Nama ?? null;
                            $tipe = optional($barang)->Tipe ?? null;

                            $parts = [];
                            if ($nama) {
                                $parts[] = $nama;
                            }
                            if ($merk) {
                                $parts[] = $merk;
                            }
                            if ($tipe) {
                                $parts[] = $tipe;
                            }
                            if (!empty($parts)) {
                                return implode(' / ', $parts);
                            }
                            return null;
                        })->filter()->toArray();

                        return implode(', ', $namaMerkTipe);
                    }
                    return '-';
                })
                ->addColumn('NomorPermintaan', function ($row) {
                    $encryptedId = encrypt($row->id);
                    $link = '<a href="' . route('pp.show', ['id' => $encryptedId]) . '" style="color: #007bff; font-weight: bold;">' . e($row->NomorPermintaan) . '</a>';
                    $button = '';

                    if (auth()->user()->can('permintaan-kirim-email') && $row->Status != 'Telah Disetujui') {
                        $button = '<br><a href="' . route('pp.edit', $encryptedId) . '" class="btn btn-sm btn-warning mt-1">
                            <i class="fa fa-paper-plane"></i> Kirim Permintaan
                        </a>';
                    }


                    return $link . $button;
                })
                ->editColumn('KodePerusahaan', function ($row) {
                    return optional($row->getPerusahaan)->Nama;
                })
                ->addColumn('LokasiPenempatan', function ($row) {
                    if ($row->getDetail && $row->getDetail->count() > 0) {
                        $lokasi = $row->getDetail->first()->RencanaPenempatan ?? null;
                        return $lokasi ? $lokasi : '-';
                    }
                    return '-';
                })


                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    $actions = '';


                    if (auth()->user()->can('permintaan-hapus')) {
                        $actions .= '<button class="btn btn-sm btn-danger btn-delete me-1" data-id="' . $encryptedId . '">
                        <i class="fa fa-trash"></i> Hapus
                    </button>';
                    }

                    $actions .= '<a href="' . route('pp.print', $encryptedId) . '" class="btn btn-sm btn-success" target="_blank">
                    <i class="fa fa-print"></i> Print
                </a>';

                    return $actions;
                })
                ->editColumn('Jenis', function ($row) {
                    return optional($row->getJenisPermintaan)->Nama;
                })
                ->addColumn('Status', function ($row) use ($userId) {
                    $status = e($row->Status);
                    $statusUpdate = $row->StatusUpdate ? \Carbon\Carbon::parse($row->StatusUpdate)->format('d-m-Y H:i') : '-';

                    $needApproval = '';
                    $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                        ->where('JenisFormId', $row->JenisForm)
                        ->where('DokumenId', $row->id)
                        ->where('UserId', $userId)
                        ->where('Status', 'Pending')
                        // ->orderBy('Urutan', 'asc')
                        ->select('JenisFormId', 'DokumenId', 'Status')
                        ->first();
                    if (!empty($approval)) {
                        $needApproval = '<br><span class="badge bg-warning text-white mt-1">Persetujuan Anda Diperlukan</span>';
                    }
                    return '<div><span class="badge bg-info">' . $status . '</span><br><small class="text-muted">Update: ' . $statusUpdate . '</small>' . $needApproval . '</div>';
                })
                ->rawColumns(['action', 'NomorPermintaan', 'Status', 'NamaBarang', 'LokasiPenempatan'])
                ->make(true);
        }

        $perusahaan = MasterPerusahaan::get();
        $jenisPermintaan = MasterJenisPengajuan::get();
        $departemen = MasterDepartemen::get();
        return view('form.permintaan-pembelian.index', compact('perusahaan', 'jenisPermintaan', 'departemen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = MasterBarang::with('getMerk', 'getSatuan', 'getJenis')->get();
        $departemen = MasterDepartemen::get();
        $satuan = MasterSatuan::get();
        $jenisPengajuan = MasterJenisPengajuan::get();
        return view('form.permintaan-pembelian.create', compact('jenisPengajuan', 'barang', 'departemen', 'satuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Tanggal' => 'required|date',
            'Departemen' => 'required',
            'Jenis' => 'required',
            'Tujuan' => 'required',
            'NamaBarang' => 'required|array|min:1',
            'Jumlah' => 'required|array|min:1',
            'Satuan' => 'required|array|min:1',
            'RencanaPenempatan' => 'required|array|min:1',
            'Keterangan' => 'nullable|array',
            'NamaBarang.*' => 'required|string|max:255',
            'Jumlah.*' => 'required|numeric|min:1',
            'Satuan.*' => 'required|string|max:100',
            'RencanaPenempatan.*' => 'nullable|string|max:255',
            'Keterangan.*' => 'nullable|string|max:500',
        ]);
        // settingan jenis form yang dipakai
        if ($request->Jenis == 1) {
            $JenisForm = 5;
        } elseif ($request->Jenis == 2) {
            $JenisForm = 3;
        } else {
            $JenisForm = 4;
        }
        $nomorAkhir = $this->generateNomorPermintaan();
        $permintaan = PermintaanPembelian::create([
            'JenisForm' => $JenisForm,
            'NomorPermintaan' => $nomorAkhir,
            'Jenis' => $request->Jenis,
            'Tujuan' => $request->Tujuan,
            'Tanggal' => $request->Tanggal,
            'Departemen' => $request->Departemen,
            'Status' => 'Draft',
            'StatusUpdate' => now(),
            'KodePerusahaan' => auth()->user()->kodeperusahaan,
            'DiajukanOleh' => auth()->user()->id,
            'DiajukanPada' => now(),
            'UserCreate' => auth()->user()->name,
        ]);

        foreach ($request->NamaBarang as $key => $item) {
            PermintaanPembelianDetail::create([
                'IdPermintaan' => $permintaan->id,
                'NamaBarang' => $item,
                'Jumlah' => $request->Jumlah[$key],
                'Satuan' => $request->Jumlah[$key],
                'RencanaPenempatan' => $request->RencanaPenempatan[$key],
                'Keterangan' => $request->Keterangan[$key],
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
            ]);
        }
        $Form = MasterForm::with([
            'getApproval' => function ($q) use ($permintaan) {
                $q->where('KodePerusahaan', $permintaan->KodePerusahaan);
            },
            'getApproval.getUser'
        ])
            ->where('id', $permintaan->JenisForm)
            ->first();
        // dd($Form);
        foreach ($Form->getApproval as $approvalSetting) {
            DokumenApproval::updateOrCreate(
                [
                    'JenisFormId' => $permintaan->JenisForm,
                    'DokumenId' => $permintaan->id,
                    'Urutan' => $approvalSetting->Urutan ?? null,
                ],
                [
                    'JenisUser' => $approvalSetting->JenisUser ?? 'Master',
                    'DepartemenId' => $approvalSetting->DepartemenId,
                    'PerusahaanId' => $approvalSetting->KodePerusahaan,
                    'JabatanId' => $approvalSetting->JabatanId ?? null,
                    'UserId' => $approvalSetting->UserId ?? null,
                    'Nama' => $approvalSetting->getUser->name ?? null,
                    'Status' => 'Pending',
                    'TanggalApprove' => null,
                    'ApprovalToken' => str_replace('-', '', Str::uuid()->toString()),
                    'Catatan' => null,
                    'Ttd' => null,
                    'UserCreate' => auth()->user()->name,
                ]
            );
        }
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Permintaan Pembelian baru dibuat: ' . $nomorAkhir);
        }

        return redirect()->route('pp.index')->with('success', 'Permintaan pembelian berhasil disimpan.');
    }

    private function generateNomorPermintaan()
    {
        $prefix = 'PP-';
        $bulan = date('m');
        $tahun = date('y');
        $kodePerusahaan = auth()->user()->kodeperusahaan;

        $jumlah = PermintaanPembelian::withTrashed()
            ->where('KodePerusahaan', $kodePerusahaan)
            ->whereMonth('Tanggal', $bulan)
            ->whereYear('Tanggal', '20' . $tahun)
            ->count();

        $nextNumber = $jumlah + 1;

        $nomorAkhir = $prefix . $tahun . $bulan . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $nomorAkhir;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = decrypt($id);
        $data = PermintaanPembelian::with('getJenisPermintaan', 'getDetail.getBarang', 'getDetail.getSatuan', 'getDiajukanOleh')->find($id);

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

        return view('form.permintaan-pembelian.show', compact('data', 'approval'));
    }

    /**
     * Handle approval action for Permintaan Pembelian.
     */
    public function approveEmail($token)
    {
        $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', $token)->first();

        // Early return jika token tidak ditemukan
        if (!$penilai) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $penilai->update([
            'Status' => 'Approved',
            'TanggalApprove' => Carbon::now(),
        ]);

        // Cari approver selanjutnya
        $nextApproval = DokumenApproval::where('DokumenId', $penilai->DokumenId)
            ->where('JenisFormId', $penilai->JenisFormId)
            ->where('Urutan', '>', $penilai->Urutan)
            ->orderBy('Urutan', 'asc')
            ->first();

        // Ambil data permintaan beserta relasinya (diperlukan untuk Mailable)
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDetail.getBarang')->find($penilai->DokumenId);

        if ($nextApproval) {
            // JIKA ADA approver selanjutnya: Kirim email notifikasi
            try {
                Mail::to($nextApproval->Email)->send(
                    new NotifikasiPermintaanPembelian($permintaan, $nextApproval)
                );
                $nextApproval->StatusEmail = 'Terkirim';
                $nextApproval->save();
            } catch (\Exception $e) {
                $nextApproval->StatusEmail = 'Gagal Kirim';
                $nextApproval->save();
            }
        } else {
            // JIKA TIDAK ADA approver selanjutnya: Update status utama permintaan
            if ($permintaan) {
                $permintaan->Status = 'Telah Disetujui';
                $permintaan->save();
            }
        }

        // Log aktivitas user saat approval via email
        if (function_exists('activity')) {
            activity()
                ->causedBy('1')
                ->withProperties([
                    'ip' => request()->ip(),
                    'token' => $token,
                    'approval_id' => $penilai->id ?? null,
                ])
                ->log('Approval permintaan pembelian via email untuk nomor: ' . ($permintaan->NomorPermintaan ?? $permintaan->NomorPengajuan ?? '-'));
        }

        return view('emails.setelah-approval', compact('penilai'))->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }
    public function approve(Request $request)
    {
        $userId = $request->input('UserId');
        $dokumenId = $request->input('DokumenId');
        $jenisFormId = $request->input('JenisForm');

        if (!$userId || !$dokumenId || !$jenisFormId) {
            return back()->with('error', 'Parameter approval tidak lengkap.');
        }

        $data = PermintaanPembelian::with('getDetail.getBarang', 'getPerusahaan')->find($dokumenId);
        if (!$data) {
            return back()->with('error', 'Data Permintaan Pembelian tidak ditemukan.');
        }
        $approvalList = DokumenApproval::where('DokumenId', $dokumenId)
            ->where('JenisFormId', $jenisFormId)
            ->orderBy('Urutan', 'asc')
            ->get();
        $dokumenApproval = $approvalList->where('UserId', $userId)->first();

        if (!$dokumenApproval) {
            return back()->with('error', 'Persetujuan tidak tersedia untuk pengguna yang dipilih.');
        }

        if (auth()->id() != $dokumenApproval->UserId) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui dokumen ini.');
        }

        $myUrutan = $dokumenApproval->Urutan;
        $cekApproveSebelumnya = $approvalList->where('Urutan', '<', $myUrutan)->where('Status', '!=', 'Approved')->count();
        if ($cekApproveSebelumnya > 0) {
            return back()->with('error', 'Anda belum bisa menyetujui dokumen ini. Approval pada urutan sebelumnya harus dilakukan terlebih dahulu.');
        }
        $user = User::find($dokumenApproval->UserId);
        if ($user && !empty($user->tandatangan)) {
            $dokumenApproval->Ttd = $user->tandatangan;
        }
        $dokumenApproval->Status = 'Approved';
        $dokumenApproval->TanggalApprove = now();
        $dokumenApproval->save();

        $nextApproval = $approvalList->where('Urutan', '>', $myUrutan)->sortBy('Urutan')->first();
        if ($nextApproval) {
            if (!empty($nextApproval->Email)) {
                Mail::to($nextApproval->Email)
                    ->send(new NotifikasiApproval($data, $nextApproval));
            }
        } else {
            $data->Status = 'Telah Disetujui';
            $data->save();
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($data)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menyetujui permintaan pembelian: ' . ($data->NomorPengajuan ?? $data->id));
        }

        return redirect()->route('pp.show', encrypt($data->id))->with('success', 'Permintaan pembelian berhasil disetujui.');
    }

    public function print($id)
    {
        $id = decrypt($id);
        $data = PermintaanPembelian::with([
            'getDetail.getBarang.getMerk',
            'getDiajukanOleh',
            'getDetail.getBarang.getSatuan'
        ])->find($id);

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->JenisForm)
            ->where('DokumenId', $data->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        // Generate QR code untuk setiap approval
        foreach ($approval as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(80)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }

        $pdf = Pdf::loadView('form.permintaan-pembelian.cetak-permintaan', compact('data', 'approval'));
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream('permintaan-pembelian-' . $data->NomorPengajuan . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $barang = MasterBarang::with('getMerk', 'getSatuan')->get();
        $departemen = MasterDepartemen::get();
        $jabatan = MasterJabatan::get();
        $satuan = MasterSatuan::get();
        $data = PermintaanPembelian::with('getDetail.getBarang')->find($id);
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->JenisForm)
            ->where('DokumenId', $data->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        $jenisPengajuan = MasterJenisPengajuan::get();
        $user = User::with('getJabatan', 'getDepartemen')->get();
        return view('form.permintaan-pembelian.edit', compact('barang', 'departemen', 'satuan', 'data', 'jenisPengajuan', 'approval', 'user', 'jabatan'));
    }
    public function kirimUlangNotifikasi($id)
    {
        $permintaan = PermintaanPembelian::with('getDetail.getBarang')->find($id);
        if (!$permintaan) {
            return back()->with('error', 'Permintaan pembelian tidak ditemukan.');
        }
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $permintaan->JenisForm)
            ->where('DokumenId', $permintaan->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->get();
        if ($approval->count() == 0) {
            return back()->with('error', 'Semua dokumen sudah di-approve, tidak ada notifikasi dikirim ulang.');
        }
        foreach ($approval as $app) {
            try {
                Mail::to($app->Email)->send(
                    new NotifikasiPermintaanPembelian(
                        $permintaan,
                        $app
                    )
                );
                $app->StatusEmail = 'Terkirim';
                $app->save();
            } catch (\Exception $e) {
                $app->StatusEmail = 'Gagal Kirim';
                $app->save();

            }
        }
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Kirim Ulang Email Permintaan Pembelian: ' . ($permintaan->NomorPengajuan ?? $permintaan->id));
        }
        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim ulang.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($id);
        $request->validate([
            'Tanggal' => 'required|date',
            'Departemen' => 'required',
            'Jenis' => 'required',
            'Tujuan' => 'required',
            'NamaBarang' => 'required|array|min:1',
            'Jumlah' => 'required|array|min:1',
            'Satuan' => 'required|array|min:1',
            'RencanaPenempatan' => 'required|array|min:1',
            'Keterangan' => 'nullable|array',
            'NamaBarang.*' => 'required|string|max:255',
            'Jumlah.*' => 'required|numeric|min:1',
            // 'Satuan.*' => 'required|string|max:100',
            'RencanaPenempatan.*' => 'nullable|string|max:255',
            'Keterangan.*' => 'nullable|string|max:500',
        ]);

        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDetail.getBarang')->find($id);
        // dd($permintaan);
        $permintaan->update([
            'Tanggal' => $request->Tanggal,
            'Departemen' => $request->Departemen,
            'Jenis' => $request->Jenis,
            'Tujuan' => $request->Tujuan,
            'Status' => 'Sudah Diajukan',
            'UserUpdate' => auth()->user()->name ?? null,
        ]);

        PermintaanPembelianDetail::where('IdPermintaan', $permintaan->id)->delete();

        foreach ($request->NamaBarang as $key => $item) {
            PermintaanPembelianDetail::create([
                'IdPermintaan' => $permintaan->id,
                'Jenis' => $request->Jenis,
                'NamaBarang' => $item,
                'Jumlah' => $request->Jumlah[$key],
                'Satuan' => $request->Satuan[$key],
                'RencanaPenempatan' => $request->RencanaPenempatan[$key],
                'Keterangan' => $request->Keterangan[$key] ?? null,
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
            ]);
        }

        $approvalDocs = DokumenApproval::where([
            'JenisFormId' => $permintaan->JenisForm,
            'DokumenId' => $permintaan->id,
        ])->orderBy('Urutan', 'asc')->get();

        // 1. Update semua status approval terlebih dahulu
        foreach ($approvalDocs as $key => $approval) {
            $userIdRaw = $request->UserId[$key] ?? null;
            $userIdParts = explode('|', $userIdRaw, 2);
            $userId = trim($userIdParts[0] ?? '');
            $namaUser = trim($userIdParts[1] ?? '');

            // Logika penentuan status
            if ($userId === (string) (auth()->user()->id)) {
                $status = 'Approved';
                $tanggalApprove = now();
                if ($key === 1) {
                    $permintaan->update(['Status' => 'Telah Disetujui']);
                }
            } elseif ($approval->Status === 'Approved') {
                // PENTING: Pertahankan status Approved jika sebelumnya sudah di-approve
                // Agar saat urutan 2 update, urutan 1 tidak kembali menjadi Pending
                $status = 'Approved';
                $tanggalApprove = $approval->TanggalApprove;
            } else {
                $status = 'Pending';
                $tanggalApprove = null;
            }

            $updateData = [
                'JabatanId' => $request->JabatanId[$key],
                'DepartemenId' => $request->DepartemenId[$key],
                'UserId' => $userId,
                'Nama' => $namaUser,
                'Email' => $request->Email[$key],
                'Urutan' => $approval->Urutan,
                'Status' => $status,
                'TanggalApprove' => $tanggalApprove,
                'UserUpdate' => auth()->user()->name,
            ];

            if ($userId !== (string) ($approval->UserId)) {
                $updateData['ApprovalToken'] = str_replace('-', '', Str::uuid()->toString());
            }

            $approval->update($updateData);
        }

        // 2. Kirim email HANYA ke approver pertama yang masih berstatus 'Pending'
        $nextApprover = $approvalDocs->firstWhere('Status', 'Pending');

        if ($nextApprover) {
            try {
                Mail::to($nextApprover->Email)->send(
                    new NotifikasiPermintaanPembelian(
                        $permintaan,
                        $nextApprover
                    )
                );
                $nextApprover->StatusEmail = 'Terkirim';
                $nextApprover->save();
            } catch (\Exception $e) {
                $nextApprover->StatusEmail = 'Gagal Kirim';
                $nextApprover->save();
            }
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui permintaan pembelian: Nomor ' . $permintaan->NomorPengajuan . ' (ID ' . $permintaan->id . ')');
        }

        return redirect()->route('pp.index')->with('success', 'Permintaan pembelian berhasil diperbarui.');
    }

    /**
     * Proses ACC (approval) Kepala Divisi pada permintaan pembelian
     */
    public function accKepalaDivisi($id)
    {
        $permintaan = PermintaanPembelian::findOrFail($id);
        $permintaan->update([
            'Status' => 'Disetujui Oleh Kepala Divisi',
            'StatusUpdate' => now(),
            'KepalaDivisi_Status' => 'Y',
            'KepalaDivisi_Pada' => now(),
            'KepalaDivisi_Oleh' => auth()->user()->id,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Permintaan pembelian diketahui Kepala Divisi: Nomor ' . $permintaan->NomorPengajuan . ' (ID ' . $permintaan->id . ')');
        }

        return redirect()->back()->with('success', 'Permintaan pembelian telah diketahui oleh Kepala Divisi.');
    }

    /**
     * Proses ACC (approval) Kepala Divisi Penunjang Medis/Umum pada permintaan pembelian
     */
    public function accKepalaDivisiPenunjang($id)
    {
        $permintaan = PermintaanPembelian::findOrFail($id);

        if (!filled($permintaan->KepalaDivisi_Status) || in_array($permintaan->KepalaDivisi_Status, ['N', 'P'])) {
            return redirect()->back()->with('error', 'Tidak Bisa Disetujui Karena Belum Disetujui Oleh Kadiv Bagian Terkait');
        }

        $permintaan->update([
            'Status' => 'Disetujui Oleh Kepala Divisi Penunjang Medis / Umum',
            'StatusUpdate' => now(),
            'Penunjang_Oleh' => auth()->user()->id,
            'Penunjang_Pada' => now(),
            'Penunjang_Status' => 'Y',
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Permintaan pembelian disetujui Kepala Divisi Penunjang: Nomor ' . $permintaan->NomorPengajuan . ' (ID ' . $permintaan->id . ')');
        }

        return redirect()->back()->with('success', 'Permintaan pembelian telah diketahui oleh Kepala Divisi.');
    }

    /**
     * Proses ACC (approval) Direktur pada permintaan pembelian
     */
    public function accDirektur($id)
    {
        $permintaan = PermintaanPembelian::with('getJenisPermintaan')->findOrFail($id);

        if (!filled($permintaan->Penunjang_Status) || in_array($permintaan->Penunjang_Status, ['N', 'P'])) {
            return redirect()->back()->with('error', 'Tidak Bisa Disetujui Karena Belum Disetujui Oleh Kadiv Penunjang ' . $permintaan->getJenisPermintaan->Nama);
        }
        $permintaan->update([
            'Status' => 'Disetujui Oleh Direktur',
            'StatusUpdate' => now(),
            'Direktur_Status' => 'Y',
            'Direktur_Pada' => now(),
            'Direktur_Oleh' => auth()->user()->id,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Permintaan pembelian disetujui Direktur: Nomor ' . $permintaan->NomorPengajuan . ' (ID ' . $permintaan->id . ')');
        }

        return redirect()->back()->with('success', 'Permintaan pembelian telah disetujui oleh Direktur.');
    }

    /**
     * Proses ACC (approval) SMI/Logistik pada permintaan pembelian
     */
    public function accSmi($id)
    {
        $permintaan = PermintaanPembelian::findOrFail($id);
        if (!filled($permintaan->Direktur_Status) || in_array($permintaan->Direktur_Status, ['N', 'P'])) {
            return redirect()->back()->with('error', 'Tidak Bisa Disetujui Karena Belum Disetujui Oleh Direktur');
        }
        $permintaan->update([
            'Status' => 'Telah Diterima SMI',
            'StatusUpdate' => now(),
            'Logistik_Oleh' => auth()->user()->id,
            'Logistik_Status' => 'Y',
            'Logistik_Pada' => now(),
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Permintaan pembelian disetujui/dikonfirmasi SMI/Logistik: Nomor ' . $permintaan->NomorPengajuan . ' (ID ' . $permintaan->id . ')');
        }

        return redirect()->back()->with('success', 'Permintaan pembelian telah dikonfirmasi/logistik oleh SMI.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $permintaan = PermintaanPembelian::find($id);

        if (!$permintaan) {
            return response()->json(['status' => 404, 'message' => 'Permintaan pembelian tidak ditemukan.']);
        }
        $pengajuanExist = \App\Models\PengajuanPembelian::where('IdPermintaan', $permintaan->id)->exists();
        if ($pengajuanExist) {
            return response()->json([
                'status' => 403,
                'message' => 'Permintaan pembelian tidak dapat dihapus karena sudah terkait pada Pengajuan Pembelian.'
            ]);
        }
        PermintaanPembelianDetail::where('IdPermintaan', $permintaan->id)->delete();
        $permintaan->delete();

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($permintaan)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus permintaan pembelian: Nomor ' . $permintaan->NomorPengajuan . ' (ID ' . $permintaan->id . ')');
        }

        return response()->json(['status' => 200, 'message' => 'Permintaan pembelian berhasil dihapus.']);
    }
}
