<?php

namespace App\Http\Controllers;

use App\Mail\NotifikasiDisposisiMail;
use App\Models\AktivitasPengajuan;
use App\Models\DokumenApproval;
use App\Models\LembarDisposisi;
use App\Models\LembarDisposisiApproval;
use App\Models\MasterBarang;
use App\Models\MasterDepartemen;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterVendor;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use App\Models\Rekomendasi;
use App\Models\User;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class LembarDisposisiController extends Controller
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
        $getNamaBarang = PengajuanItem::with('getBarang')->where('id', $idPengajuanItem)->first();
        $data = PengajuanItem::with([
            'getRekomendasi.getRekomedasiDetail' => function ($query) {
                $query->where('Rekomendasi', 1)->with('getNamaVendor');
            },
            'getBarang',
            'getPengajuanPembelian.getPermintaan.getDetail' => function ($query) use ($getNamaBarang) {
                $query->where('NamaBarang', $getNamaBarang->IdBarang);
            },
            'getPengajuanPembelian.getPermintaan.getDiajukanOleh'
        ])->where('id', $idPengajuanItem)->first();
        $cekjenis = PengajuanPembelian::where('id', $idPengajuan)->first();
        if ($cekjenis->Jenis == 1) {
            $JenisForm = 9;
        } else {
            $JenisForm = 10;
        }
        $ttd = MasterForm::with([
            'getApproval' => function ($q) use ($data) {
                $q->where('KodePerusahaan', $data->KodePerusahaan);
            },
            'getApproval.getUser'
        ])
            ->where('id', $JenisForm)
            ->first();
        $user = User::get();
        $jabatan = MasterJabatan::get();
        $departemen = MasterDepartemen::get();
        // dd($data);
        return view('lembar-disposisi.create', compact('data', 'user', 'jabatan', 'departemen', 'idPengajuan', 'idPengajuanItem', 'ttd'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $idPengajuan = $request->IdPengajuan;
        // dd($idPengajuan);
        $idPengajuanItem = encrypt($request->PengajuanItemId);
        $validatedHeader = $request->validate([
            'NamaBarang' => 'required|string',
            'IdPengajuan' => 'required',
            'PengajuanItemId' => 'required',
            'Harga' => 'required|string',
            // 'Email' => 'required|string',
            'RencanaVendor' => 'required|string',
            'TujuanPenempatan' => 'required|string',
            'FormPermintaan' => 'required|string',
        ]);

        $cekjenis = PengajuanPembelian::where('id', $idPengajuan)->first();
        if ($cekjenis->Jenis == 1) {
            $JenisForm = 9;
        } else {
            $JenisForm = 10;
        }
        // dd($JenisForm);
        $lembarDisposisi = LembarDisposisi::updateOrCreate(
            [
                'IdPengajuan' => $validatedHeader['IdPengajuan'],
                'PengajuanItemId' => $validatedHeader['PengajuanItemId'],
                'JenisForm' => $JenisForm,
            ],
            [
                'NamaBarang' => $validatedHeader['NamaBarang'],
                'Harga' => preg_replace('/\D/', '', $validatedHeader['Harga']),
                'RencanaVendor' => $validatedHeader['RencanaVendor'],
                'TujuanPenempatan' => $validatedHeader['TujuanPenempatan'],
                'FormPermintaan' => $validatedHeader['FormPermintaan'],
            ]
        );
        // dd($lembarDisposisi);
        if (LembarDisposisiApproval::where('IdLembarDisposisi', $lembarDisposisi->id)->exists()) {
            LembarDisposisiApproval::where('IdLembarDisposisi', $lembarDisposisi->id)->delete();
        }
        $MasterVendor = MasterVendor::find($lembarDisposisi->RencanaVendor);
        $MasterBarang = MasterBarang::with('getMerk')->find($lembarDisposisi->NamaBarang);
        // Ambil data Rekomendasi
        $rekomendasi = Rekomendasi::with('getRekomedasiDetail.getPerusahaan', 'getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNegara')->where('PengajuanItemId', $lembarDisposisi->PengajuanItemId)->first();
        // dd($rekomendasi);
        if ($rekomendasi->UserNego !== null) {
            $qrCode = QrCode::create($rekomendasi->id)
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $rekomendasi->qrCodeNego = base64_encode($result->getString());
        }

        if ($rekomendasi->DisetujuiOleh !== null) {
            $qrCode = QrCode::create($rekomendasi->id ?? '')
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $rekomendasi->qrCodeApprove = base64_encode($result->getString());
        }
        $approvalDispo = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $lembarDisposisi->JenisForm)
            ->where('DokumenId', $lembarDisposisi->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        foreach ($approvalDispo as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }
        if ($request->has('IdUser') && is_array($request->IdUser)) {
            foreach ($request->IdUser as $i => $idUser) {
                // Kecuali user id 81 //Pak Arfan

                $user = User::find($idUser);
                $namaUser = $user ? $user->name : null;
                $email = $user ? $user->email : null;
                $approvalToken = str_replace('-', '', Str::uuid()->toString());

                $approval = DokumenApproval::updateOrCreate(
                    [
                        'JenisFormId' => $lembarDisposisi->JenisForm,
                        'DokumenId' => $lembarDisposisi->id,
                        'Urutan' => $i + 1 ?? null,
                    ],
                    [
                        'JenisUser' => 'Master',
                        'PerusahaanId' => auth()->user()->kodeperusahaan,
                        'DepartemenId' => $request->Departemen[$i] ?? null,
                        'JabatanId' => $request->Jabatan[$i] ?? null,
                        'UserId' => $request->IdUser[$i] ?? null,
                        'Nama' => $namaUser ?? null,
                        'Email' => $email ?? null,
                        'Status' => 'Pending',
                        'TanggalApprove' => null,
                        'ApprovalToken' => $approvalToken,
                        'Catatan' => null,
                        'Ttd' => null,
                        'UserCreate' => auth()->user()->name,
                    ]
                );
                // Function Kirim Email Matiin
                // Jangan kirim ke user id 81 dan 2
                if ($idUser != 81 && $idUser != 2) {
                    Mail::to($request->Email[$i])
                        ->send(new NotifikasiDisposisiMail($lembarDisposisi, $approvalDispo, $approval, $MasterVendor, $MasterBarang, $rekomendasi, $cekjenis));
                }


            }
        }

        $pengajuan = PengajuanPembelian::find($lembarDisposisi->IdPengajuan ?? null);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($lembarDisposisi->KodePengajuan ?? null);

        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Disposisi',
            'Keterangan' => 'Membuat Lembar Disposisi untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' sudah dibuat dan dikirim ke email',
            'UserCreate' => auth()->user()->name,
        ]);
        return redirect()->back()->with('success', 'Lembar Disposisi berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function kirimUlangNotifikasi($id)
    {
        $lembarDisposisi = LembarDisposisi::find($id);
        if (!$lembarDisposisi) {
            return back()->with('error', 'Permintaan pembelian tidak ditemukan.');
        }
        // dd($dipo);
        $approvalcek = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $lembarDisposisi->JenisForm)
            ->where('DokumenId', $lembarDisposisi->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->get();
        if ($approvalcek->count() == 0) {
            return back()->with('error', 'Semua dokumen sudah di-approve, tidak ada notifikasi dikirim ulang.');
        }
        // dd($approval);
        $rekomendasi = Rekomendasi::with('getRekomedasiDetail.getPerusahaan', 'getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNegara')->where('PengajuanItemId', $lembarDisposisi->PengajuanItemId)->first();
        // dd($rekomendasi);
        if ($rekomendasi->UserNego !== null) {
            $qrCode = QrCode::create($rekomendasi->id)
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $rekomendasi->qrCodeNego = base64_encode($result->getString());
        }

        if ($rekomendasi->DisetujuiOleh !== null) {
            $qrCode = QrCode::create($rekomendasi->id ?? '')
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $rekomendasi->qrCodeApprove = base64_encode($result->getString());
        }
        $approvalDispo = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $lembarDisposisi->JenisForm)
            ->where('DokumenId', $lembarDisposisi->id)
            ->orderBy('Urutan', 'asc')
            ->get();
        foreach ($approvalDispo as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }
        $MasterVendor = MasterVendor::find($lembarDisposisi->RencanaVendor);
        $MasterBarang = MasterBarang::with('getMerk')->find($lembarDisposisi->NamaBarang);
        if ($approvalcek->count() == 0) {
            return back()->with('error', 'Semua dokumen sudah di-approve, tidak ada notifikasi dikirim ulang.');
        }
        foreach ($approvalcek as $approval) {
            // Kecuali user id 81
            if ($approval->UserId == 81 || $approval->UserId == 2) {
                continue;
            }

            try {
                Mail::to($approval->Email)
                    ->send(new NotifikasiDisposisiMail($lembarDisposisi, $approvalDispo, $approval, $MasterVendor, $MasterBarang, $rekomendasi));
                $approval->StatusEmail = 'Terkirim';
                $approval->save();
            } catch (\Exception $e) {
                // dd($e);
                $approval->StatusEmail = 'Gagal Kirim';
                $approval->save();
            }
        }
        $pengajuan = PengajuanPembelian::find($lembarDisposisi->IdPengajuan ?? null);
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Disposisi',
            'Keterangan' => 'Kirim Ulang Notifikasi Lembar Disposisi untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' sudah dikirim ke email.',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->performedOn($lembarDisposisi)
                ->withProperties(['ip' => request()->ip()])
                ->log('Kirim Ulang Email Permintaan Pembelian: ' . ($permintaan->NomorPengajuan ?? $lembarDisposisi->id));
        }
        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim ulang.');
    }

    public function show($idPengajuan, $idPengajuanItem)
    {
        $data = LembarDisposisi::with('getDetail', 'getBarang', 'getPengajuan')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();

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

        return view('lembar-disposisi.show', compact('data', 'approval'));
    }

    public function print($idPengajuan, $idPengajuanItem)
    {
        $lembarDisposisi = LembarDisposisi::with(['getDetail', 'getBarang'])
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();

        if (!$lembarDisposisi) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $lembarDisposisi->JenisForm)
            ->where('DokumenId', $lembarDisposisi->id)
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

        // Siapkan data untuk PDF
        $data = [
            'lembarDisposisi' => $lembarDisposisi,
            'namaBarang' => $lembarDisposisi->getBarang->Nama,
            'harga' => $lembarDisposisi->Harga,
            'rencanaVendor' => $lembarDisposisi->getVendor->Nama,
            'tujuanPenempatan' => $lembarDisposisi->TujuanPenempatan,
            'formPermintaan' => $lembarDisposisi->FormPermintaanUser,
            'approval' => $approval,
        ];
        $cekjenis = PengajuanPembelian::where('id', $idPengajuan)->first();
        // dd($approval);
        if ($cekjenis->Jenis == '1') {
            $pdf = \PDF::loadView('lembar-disposisi.cetak-pdf', compact('data'));
            $pdf->setPaper('A4', 'portrait');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
        } else {
            $pdf = \PDF::loadView('lembar-disposisi.cetak-umum-pdf', compact('data'));
            $pdf->setPaper('A4', 'portrait');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
        }

        return $pdf->stream('lembar-disposisi-' . $idPengajuan . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($idPengajuan, $idPengajuanItem)
    {
        $idPengajuan = decrypt($idPengajuan);
        $idPengajuanItem = decrypt($idPengajuanItem);
        $getNamaBarang = PengajuanItem::with('getBarang')->where('id', $idPengajuanItem)->first();
        $data = PengajuanItem::with([
            'getRekomendasi.getRekomedasiDetail' => function ($query) {
                $query->where('Rekomendasi', 1)->with('getNamaVendor');
            },
            'getBarang',
            'getPengajuanPembelian.getPermintaan.getDetail' => function ($query) use ($getNamaBarang) {
                $query->where('NamaBarang', $getNamaBarang->IdBarang);
            },
            'getPengajuanPembelian.getPermintaan.getDiajukanOleh'
        ])->where('id', $idPengajuanItem)->first();
        $cekjenis = PengajuanPembelian::where('id', $idPengajuan)->first();
        if ($cekjenis->Jenis == 1) {
            $JenisForm = 9;
        } else {
            $JenisForm = 10;
        }
        $ttd = MasterForm::with([
            'getApproval' => function ($q) use ($data) {
                $q->where('KodePerusahaan', $data->KodePerusahaan);
            },
            'getApproval.getUser'
        ])
            ->where('id', $JenisForm)
            ->first();
        $user = User::get();
        $jabatan = MasterJabatan::get();
        $departemen = MasterDepartemen::get();
        // Approval
        $dispo = LembarDisposisi::where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();
        // dd($dispo);
        $approval = null;
        if ($dispo !== null) {
            $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                ->where('JenisFormId', $dispo->JenisForm)
                ->where('DokumenId', $dispo->id)
                ->orderBy('Urutan', 'asc')
                ->get();
        } else {
            $approval = null;
        }
        return view('lembar-disposisi.edit', compact('data', 'user', 'jabatan', 'departemen', 'idPengajuan', 'idPengajuanItem', 'ttd', 'approval', 'dispo'));
    }

    public function approve($token)
    {
        $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', $token)->firstOrFail();
        // dd($penilai);
        $penilai->update([
            'Status' => 'Approved',
            'TanggalApprove' => Carbon::now(),
        ]);

        if ($penilai->Status === 'Approved') {
            $pengajuan = null;
            $kodePengajuan = null;
            if ($penilai->DokumenId ?? false) {
                $lembarDisposisi = LembarDisposisi::find($penilai->DokumenId);
                if ($lembarDisposisi) {
                    $pengajuan = PengajuanPembelian::find($lembarDisposisi->IdPengajuan);
                    $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : ($lembarDisposisi->KodePengajuan ?? $lembarDisposisi->id);
                }
            }
            AktivitasPengajuan::create([
                'KodePengajuan' => $kodePengajuan,
                'Jenis' => 'Disposisi',
                'Keterangan' => ($penilai->Nama ?? '-') . ' telah menyetujui Lembar Disposisi',
                'UserCreate' => $penilai->Nama ?? '-',
            ]);
        }
        try {
            if (($penilai->UserId ?? null) == 81) {
                $KodePengajuan = $kodePengajuan ?? null;
                if ($KodePengajuan) {
                    $cariPengajuan = PengajuanPembelian::where('KodePengajuan', $KodePengajuan)->first();
                    if ($cariPengajuan) {
                        $cariPengajuan->AccCeo = 'Y';
                        $cariPengajuan->Status = 'Disetujui CEO';
                        $cariPengajuan->TanggalAccCeo = Carbon::now();
                        $cariPengajuan->save();
                    }
                }
            }
        } catch (\Throwable $e) {

        }

        return view('emails.setelah-approval', compact('penilai'))->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LembarDisposisi $lembarDisposisi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LembarDisposisi $lembarDisposisi)
    {
        //
    }
}
