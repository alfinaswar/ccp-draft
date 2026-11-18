<?php

namespace App\Http\Controllers;

use App\Models\DokumenApproval;
use App\Models\KomplainVendor;
use App\Models\MasterApproval;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterParameterKomplain;
use App\Models\MasterPerusahaan;
use App\Models\MasterUnit;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KomplainVendorController extends Controller
{
    private function formId(): ?int
    {
        return MasterForm::where('Nama', 'Komplain Vendor')->value('id');
    }

    private function boleh(array $roles): bool
    {
        return auth()->user()->hasAnyRole(array_merge($roles, ['Admin']));
    }

    /**
     * Gabungkan daftar master dengan nilai tersimpan, supaya parameter yang sudah
     * dihapus dari master tetap tampil pada laporan lama.
     */
    private function gabungTersimpan(array $master, $tersimpan): array
    {
        return array_values(array_unique(array_merge($master, (array) ($tersimpan ?? []))));
    }

    private function generateNomorKomplain(): string
    {
        // shortcut: nomor urut dihitung dari jumlah baris bulan ini, bisa bentrok bila submit bersamaan.
        // Upgrade ke lock/sequence tabel bila volume komplain tinggi.
        $jumlah = KomplainVendor::withTrashed()
            ->whereYear('Tanggal', date('Y'))
            ->whereMonth('Tanggal', date('m'))
            ->count();

        return 'KV-' . date('ym') . str_pad($jumlah + 1, 4, '0', STR_PAD_LEFT);
    }

    private function buatApproval(KomplainVendor $komplain): void
    {
        $formId = $this->formId();
        if (!$formId) {
            return;
        }

        $sudahAda = DokumenApproval::where('JenisFormId', $formId)
            ->where('DokumenId', $komplain->id)
            ->exists();

        if ($sudahAda) {
            return;
        }

        $setting = MasterApproval::where('JenisForm', $formId)
            ->where('KodePerusahaan', $komplain->KodePerusahaan)
            ->where('Aktif', 'Y')
            ->orderBy('Urutan')
            ->get();

        if ($setting->isEmpty()) {
            $setting = collect([
                (object) ['Urutan' => 1, 'UserId' => User::role('Kadiv')->value('id')],
                (object) ['Urutan' => 2, 'UserId' => User::role('Group Head')->value('id')],
            ]);
        }

        foreach ($setting as $s) {
            $user = User::find($s->UserId);

            // shortcut: bila approver tidak ditemukan, baris dilewati sehingga tahap itu terlewati.
            // Atur urutan Kepala Divisi dan Group Head di menu Master Approval.
            if (!$user) {
                continue;
            }

            DokumenApproval::create([
                'JenisUser' => 'Master',
                'JenisFormId' => $formId,
                'DokumenId' => $komplain->id,
                'PerusahaanId' => $komplain->KodePerusahaan,
                'JabatanId' => $user->jabatan,
                'DepartemenId' => $user->departemen,
                'UserId' => $s->UserId,
                'Nama' => $user->name,
                'Email' => $user->email,
                'Urutan' => $s->Urutan,
                'Status' => 'Pending',
                'ApprovalToken' => str_replace('-', '', (string) Str::uuid()),
            ]);
        }
    }

    private function catat(string $pesan, KomplainVendor $komplain): void
    {
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->id())
                ->performedOn($komplain)
                ->withProperties(['ip' => request()->ip(), 'status' => $komplain->Status])
                ->log($pesan . ' (' . $komplain->KodeKomplain . ')');
        }
    }

    /**
     * Form publik: tidak memerlukan login.
     */
    public function form()
    {
        return view('komplain.form', [
            'parameterEvaluasi' => MasterParameterKomplain::daftar('ParameterEvaluasi'),
            'perusahaan' => MasterPerusahaan::orderBy('Nama')->get(),
            'unit' => MasterUnit::where('Aktif', 'Y')->orderBy('nama_unit')->get(),
            'jabatan' => MasterJabatan::orderBy('Nama')->get(),
        ]);
    }

    public function simpan(Request $request)
    {
        $parameterEvaluasi = MasterParameterKomplain::daftar('ParameterEvaluasi');

        $request->validate([
            'KodePerusahaan' => 'required|exists:master_perusahaans,Kode',
            'Tanggal' => 'required|date',
            'NamaPelapor' => 'required|string|max:255',
            'JabatanId' => 'required|exists:master_jabatans,id',
            'UnitId' => [
                'required',
                Rule::exists('master_units', 'id')->where(function ($q) use ($request) {
                    $q->where('Aktif', 'Y')
                        ->where('KodePerusahaan', $request->KodePerusahaan);
                }),
            ],
            'NamaBarang' => 'required|string|max:255',
            'JenisKategori' => 'required|string|max:255',
            'Merk' => 'required|string|max:255',
            'Distributor' => 'required|string|max:255',
            'ParameterEvaluasi' => 'required|array|min:1',
            'ParameterEvaluasi.*' => ['string', Rule::in($parameterEvaluasi)],
            'UraianSingkat' => 'required|string',
            'Lampiran' => 'nullable|array|max:5',
            'Lampiran.*' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'KodePerusahaan.required' => 'Nama RS wajib dipilih.',
            'NamaPelapor.required' => 'Nama pelapor wajib diisi.',
            'JabatanId.required' => 'Jabatan wajib dipilih.',
            'UnitId.required' => 'Unit kerja wajib dipilih.',
            'UnitId.exists' => 'Unit kerja tidak tersedia untuk RS yang dipilih.',
            'NamaBarang.required' => 'Nama barang wajib diisi.',
            'JenisKategori.required' => 'Jenis/kategori wajib diisi.',
            'Merk.required' => 'Merk wajib diisi.',
            'Distributor.required' => 'Nama distributor/penyedia wajib diisi.',
            'ParameterEvaluasi.required' => 'Pilih minimal satu parameter evaluasi.',
            'UraianSingkat.required' => 'Uraian singkat wajib diisi.',
            'Lampiran.max' => 'Maksimal 5 file pendukung.',
            'Lampiran.*.mimes' => 'File pendukung harus berupa foto (jpg, png, webp) atau PDF.',
            'Lampiran.*.max' => 'Ukuran tiap file maksimal 5 MB.',
        ]);

        $uraian = $this->bersihkanHtml($request->UraianSingkat);

        if ($uraian === '') {
            return back()->withInput()->withErrors(['UraianSingkat' => 'Uraian singkat wajib diisi.']);
        }

        if (in_array(MasterParameterKomplain::LAINNYA, $request->input('ParameterEvaluasi', []), true)
            && !$request->filled('ParameterLainnya')) {
            return back()->withInput()->withErrors(['ParameterLainnya' => 'Isi keterangan parameter "Lainnya".']);
        }

        $perusahaan = MasterPerusahaan::where('Kode', $request->KodePerusahaan)->first();
        $unit = MasterUnit::find($request->UnitId);
        $jabatan = MasterJabatan::find($request->JabatanId);

        $komplain = KomplainVendor::create([
            ...$request->only([
                'Tanggal',
                'NamaPelapor',
                'NamaBarang', 'JenisKategori', 'Merk', 'Distributor',
                'ParameterEvaluasi', 'ParameterLainnya',
            ]),
            'UraianSingkat' => $uraian,
            'Lampiran' => $this->simpanLampiran($request),
            'KodeKomplain' => $this->generateNomorKomplain(),
            'KodePerusahaan' => $perusahaan->Kode,
            'NamaRS' => $perusahaan->Nama,
            'UnitId' => $unit->id,
            'UnitKerja' => $unit->nama_unit,
            'JabatanId' => $jabatan->id,
            'Jabatan' => $jabatan->Nama,
            'Status' => 'Menunggu TTD Kepala Divisi',
            'UserCreate' => $request->NamaPelapor,
        ]);

        $this->buatApproval($komplain);

        return redirect()->route('komplain.terima-kasih', $komplain->KodeKomplain);
    }

    public function terimaKasih($kode)
    {
        $komplain = KomplainVendor::where('KodeKomplain', $kode)->firstOrFail();

        return view('komplain.terima-kasih', compact('komplain'));
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = KomplainVendor::query();

            if ($request->filled('status')) {
                $data->where('Status', $request->status);
            }
            if ($request->filled('cari')) {
                $q = $request->cari;
                $data->where(function ($sub) use ($q) {
                    $sub->where('KodeKomplain', 'like', "%{$q}%")
                        ->orWhere('NamaBarang', 'like', "%{$q}%")
                        ->orWhere('Distributor', 'like', "%{$q}%")
                        ->orWhere('NamaPelapor', 'like', "%{$q}%");
                });
            }

            return DataTables::of($data->latest())
                ->addIndexColumn()
                ->addColumn('ParameterText', function ($row) {
                    return e(implode(', ', $row->ParameterEvaluasi ?? []));
                })
                ->addColumn('StatusBadge', function ($row) {
                    $warna = $row->Status === 'Selesai' ? 'success' : 'warning';

                    return '<span class="badge bg-' . $warna . '">' . e($row->Status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('komplain.show', encrypt($row->id)) . '"
                                class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> Lihat</a>';
                })
                ->rawColumns(['StatusBadge', 'action'])
                ->make(true);
        }

        return view('komplain.index', ['statusList' => KomplainVendor::STATUS_LIST]);
    }

    public function show($id)
    {
        $komplain = KomplainVendor::findOrFail($this->decryptId($id));

        $approval = DokumenApproval::with('getUser', 'getJabatan')
            ->where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->orderBy('Urutan')
            ->get();

        foreach ($approval as $item) {
            if ($item->Status == 'Approved' && $item->ApprovalToken) {
                $qr = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(300)
                    ->setMargin(10);

                $item->qrCode = base64_encode((new PngWriter())->write($qr)->getString());
            }
        }

        return view('komplain.show', [
            'komplain' => $komplain,
            'approval' => $approval,
            'parameterEvaluasi' => $this->gabungTersimpan(
                MasterParameterKomplain::daftar('ParameterEvaluasi'),
                $komplain->ParameterEvaluasi
            ),
            'dataPendukung' => $this->gabungTersimpan(
                MasterParameterKomplain::daftar('DataPendukung'),
                $komplain->DataPendukung
            ),
            'langkahAwalList' => $this->gabungTersimpan(
                MasterParameterKomplain::daftar('LangkahAwal'),
                $komplain->LangkahAwal
            ),
            'tindakanCcpList' => $this->gabungTersimpan(
                MasterParameterKomplain::daftar('TindakanCcp'),
                $komplain->TindakanCcp
            ),
            'bisaApproval' => $this->boleh(['Kadiv', 'Group Head', 'GH CCP']),
            'bisaLogum' => $this->boleh(['Logum', 'SMI']),
            'bisaCcp' => $this->boleh(['CCP', 'GH CCP']),
        ]);
    }

    public function approve(Request $request, $id)
    {
        $komplain = KomplainVendor::findOrFail($this->decryptId($id));

        $approval = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('UserId', auth()->id())
            ->where('Status', 'Pending')
            ->orderBy('Urutan')
            ->first();

        if (!$approval) {
            return back()->with('error', 'Anda bukan approver pada tahap ini.');
        }

        $sebelumnya = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Urutan', '<', $approval->Urutan)
            ->where('Status', '!=', 'Approved')
            ->exists();

        if ($sebelumnya) {
            return back()->with('error', 'Approval sebelumnya belum selesai.');
        }

        $approval->update([
            'Status' => 'Approved',
            'TanggalApprove' => now(),
            'Catatan' => $request->Catatan,
            'Ttd' => auth()->user()->tandatangan,
        ]);

        $berikutnya = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan')
            ->first();

        $komplain->update([
            'Status' => !$berikutnya
                ? 'Menunggu Tindak Lanjut Logistik Umum'
                : ($berikutnya->Urutan == 1 ? 'Menunggu TTD Kepala Divisi' : 'Menunggu TTD Group Head'),
        ]);

        $this->catat('Menandatangani komplain pada urutan ' . $approval->Urutan, $komplain);

        return back()->with('success', 'Tanda tangan tersimpan.');
    }

    /**
     * E. Tindak Lanjut Logistik Umum
     */
    public function logistikUmum(Request $request, $id)
    {
        $komplain = KomplainVendor::findOrFail($this->decryptId($id));

        if (!$this->boleh(['Logum', 'SMI'])) {
            return back()->with('error', 'Hanya Logistik Umum yang dapat mengisi bagian ini.');
        }

        if ($komplain->Status !== 'Menunggu Tindak Lanjut Logistik Umum') {
            return back()->with('error', 'Laporan tidak berada pada tahap tindak lanjut logistik umum.');
        }

        $request->validate([
            'LangkahAwal' => 'required|array|min:1',
            'LangkahAwal.*' => ['string', Rule::in(MasterParameterKomplain::daftar('LangkahAwal'))],
        ], [
            'LangkahAwal.required' => 'Langkah awal wajib dipilih minimal satu.',
        ]);

        if (in_array(MasterParameterKomplain::LAINNYA, $request->LangkahAwal, true)
            && !$request->filled('LangkahAwalLainnya')) {
            return back()->withInput()->withErrors(['LangkahAwalLainnya' => 'Isi keterangan langkah awal "Lainnya".']);
        }

        $komplain->update([
            'LangkahAwal' => $request->LangkahAwal,
            'LangkahAwalLainnya' => $request->LangkahAwalLainnya,
            'LogistikUmumOleh' => auth()->user()->name,
            'TanggalLogistikUmum' => now(),
            'Status' => 'Menunggu Tindakan CCP',
        ]);

        $this->catat('Mengisi tindak lanjut logistik umum', $komplain);

        return back()->with('success', 'Tindak lanjut logistik umum tersimpan.');
    }

    /**
     * F. Tindak Lanjut CCP
     */
    public function tindakanCcp(Request $request, $id)
    {
        $komplain = KomplainVendor::findOrFail($this->decryptId($id));

        if (!$this->boleh(['CCP', 'GH CCP'])) {
            return back()->with('error', 'Hanya CCP yang dapat mengisi bagian ini.');
        }

        if ($komplain->Status !== 'Menunggu Tindakan CCP') {
            return back()->with('error', 'Laporan tidak berada pada tahap tindakan CCP.');
        }

        $request->validate([
            'TindakanCcp' => 'required|array|min:1',
            'TindakanCcp.*' => ['string', Rule::in(MasterParameterKomplain::daftar('TindakanCcp'))],
        ], [
            'TindakanCcp.required' => 'Tindakan CCP wajib dipilih minimal satu.',
        ]);

        if (in_array(MasterParameterKomplain::LAINNYA, $request->TindakanCcp, true)
            && !$request->filled('TindakanCcpLainnya')) {
            return back()->withInput()->withErrors(['TindakanCcpLainnya' => 'Isi keterangan tindakan "Lainnya".']);
        }

        $komplain->update([
            'TindakanCcp' => $request->TindakanCcp,
            'TindakanCcpLainnya' => $request->TindakanCcpLainnya,
            'CcpOleh' => auth()->user()->name,
            'TanggalCcp' => now(),
            'Status' => 'Selesai',
        ]);

        $this->catat('Mengisi tindak lanjut CCP', $komplain);

        return back()->with('success', 'Tindakan CCP tersimpan. Laporan selesai.');
    }

    private function decryptId($id): int
    {
        try {
            return (int) decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    /**
     * Uraian singkat berasal dari form publik tanpa login, jadi HTML dari editor
     * dibersihkan: hanya tag format dasar, semua atribut dibuang.
     */
    private function bersihkanHtml(?string $html): string
    {
        $html = preg_replace('/<(p|br|strong|b|em|i|u|ul|ol|li)\b[^>]*>/i', '<$1>', $html ?? '');
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li>');

        return trim(preg_replace('/<p>\s*(&nbsp;)?\s*<\/p>/i', '', $html) ?? '');
    }

    /**
     * @return array<int, array{path: string, nama: string, tipe: string, ukuran: int}>
     */
    private function simpanLampiran(Request $request): array
    {
        $hasil = [];

        foreach ($request->file('Lampiran', []) as $file) {
            $path = $file->store('komplain-vendor/lampiran', 'public');

            $hasil[] = [
                'path' => $path,
                'nama' => $file->getClientOriginalName(),
                'tipe' => $file->getMimeType(),
                'ukuran' => $file->getSize(),
            ];
        }

        return $hasil;
    }
}
