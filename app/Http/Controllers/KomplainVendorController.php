<?php

namespace App\Http\Controllers;

use App\Mail\NotifKomplainVendor;
use App\Models\DokumenApproval;
use App\Models\KomplainVendor;
use App\Models\MasterApproval;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterParameterKomplain;
use App\Models\MasterPerusahaan;
use App\Models\MasterUnit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KomplainVendorController extends Controller
{
    private function tentukanStatus(?DokumenApproval $approvalBerikutnya, KomplainVendor $komplain): string
    {
        if ($approvalBerikutnya) {
            $namaJabatan = strtolower($approvalBerikutnya->NamaJabatan ?? '');
            $urutan = (int) $approvalBerikutnya->Urutan;

            if (str_contains($namaJabatan, 'divisi') || str_contains($namaJabatan, 'kadiv') || $urutan === 2) {
                return 'Menunggu TTD Kepala Divisi';
            }

            if ((str_contains($namaJabatan, 'group head') || str_contains($namaJabatan, 'gh'))
                && !str_contains($namaJabatan, 'ccp')
                || $urutan === 3) {
                return 'Menunggu TTD Group Head';
            }

            if (str_contains($namaJabatan, 'logistik') || str_contains($namaJabatan, 'logum') || str_contains($namaJabatan, 'smi') || $urutan === 4) {
                return 'Menunggu Tindak Lanjut Logistik Umum';
            }

            if (str_contains($namaJabatan, 'ccp') || $urutan >= 5) {
                return 'Menunggu Tindakan CCP';
            }
        }

        if (empty($komplain->LangkahAwal)) {
            return 'Menunggu Tindak Lanjut Logistik Umum';
        }

        if (empty($komplain->TindakanCcp)) {
            return 'Menunggu Tindakan CCP';
        }

        return 'Selesai';
    }

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

        // Urutan 1 selalu Pelapor dan langsung Auto Approve
        DokumenApproval::create([
            'JenisUser' => 'Manual',
            'JenisFormId' => $formId,
            'DokumenId' => $komplain->id,
            'PerusahaanId' => $komplain->KodePerusahaan,
            'JabatanId' => $komplain->JabatanId,
            'NamaJabatan' => 'Pelapor',
            'DepartemenId' => null,
            'UserId' => auth()->id(),
            'Nama' => $komplain->NamaPelapor,
            'Email' => auth()->user()?->email,
            'Urutan' => 1,
            'Status' => 'Approved',
            'TanggalApprove' => now(),
            'Catatan' => 'Auto approve saat pengajuan formulir komplain',
            'Ttd' => auth()->user()?->tandatangan,
            'ApprovalToken' => str_replace('-', '', (string) Str::uuid()),
        ]);

        $perusahaan = is_numeric($komplain->KodePerusahaan)
            ? MasterPerusahaan::find($komplain->KodePerusahaan)
            : MasterPerusahaan::where('Kode', $komplain->KodePerusahaan)->first();

        $kodeRs = $perusahaan?->Kode;

        $setting = MasterApproval::where('JenisForm', $formId)
            ->where(function ($q) use ($komplain, $kodeRs) {
                $q->where('KodePerusahaan', (string) $komplain->KodePerusahaan);
                if ($kodeRs) {
                    $q->orWhere('KodePerusahaan', (string) $kodeRs);
                }
            })
            ->where('Aktif', 'Y')
            ->orderBy('Urutan')
            ->get();

        if ($setting->isEmpty()) {
            $setting = collect([
                (object) ['Urutan' => 2, 'UserId' => User::role('Kadiv')->value('id'), 'NamaJabatan' => 'Kepala Divisi', 'JabatanId' => null, 'DepartemenId' => null],
                (object) ['Urutan' => 3, 'UserId' => User::role('Group Head')->value('id'), 'NamaJabatan' => 'Group Head', 'JabatanId' => null, 'DepartemenId' => null],
            ]);
        } else {
            $pertama = $setting->first();
            $barisPertamaAdalahPelapor = (int) $pertama->Urutan === 1
                && (empty($pertama->UserId) || stripos((string) ($pertama->NamaJabatan ?? ''), 'pelapor') !== false);

            if ($barisPertamaAdalahPelapor) {
                $setting = $setting->slice(1)->values();
            } elseif ((int) $pertama->Urutan === 1) {
                // Geser urutan Master Approval (+1) karena Urutan 1 sudah diisi Pelapor
                $setting = $setting->map(function ($item) {
                    $clone = clone $item;
                    $clone->Urutan = (int) $item->Urutan + 1;
                    return $clone;
                });
            }
        }

        foreach ($setting as $s) {
            $user = $s->UserId ? User::find($s->UserId) : null;

            // shortcut: bila approver tidak ditemukan, baris dilewati sehingga tahap itu terlewati.
            // Atur urutan pejabat approver di menu Master Approval.
            if (!$user) {
                continue;
            }

            DokumenApproval::create([
                'JenisUser' => 'Master',
                'JenisFormId' => $formId,
                'DokumenId' => $komplain->id,
                'PerusahaanId' => $komplain->KodePerusahaan,
                'JabatanId' => $s->JabatanId ?? $user->jabatan,
                'NamaJabatan' => $s->NamaJabatan ?? null,
                'DepartemenId' => $s->DepartemenId ?? $user->departemen,
                'UserId' => $user->id,
                'Nama' => $user->name,
                'Email' => $user->email,
                'Urutan' => $s->Urutan,
                'Status' => 'Pending',
                'ApprovalToken' => str_replace('-', '', (string) Str::uuid()),
            ]);
        }
    }

    private function kirimNotifikasiApproval(KomplainVendor $komplain, ?DokumenApproval $targetApproval): void
    {
        if (!$targetApproval) {
            return;
        }

        $emailTujuan = $targetApproval->Email ?: $targetApproval->getUser?->email;
        if (!$emailTujuan) {
            return;
        }

        $daftarApproval = DokumenApproval::with('getUser', 'getJabatan')
            ->where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->orderBy('Urutan')
            ->get();

        try {
            Mail::to($emailTujuan)->send(new NotifKomplainVendor($komplain, $targetApproval, $daftarApproval));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email notifikasi komplain vendor: ' . $e->getMessage(), [
                'komplain_id' => $komplain->id,
                'approval_id' => $targetApproval->id,
                'email' => $emailTujuan,
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
            'perusahaan' => MasterPerusahaan::orderByRaw('COALESCE(NamaLengkap, Nama) ASC')->get(),
            'unit' => MasterUnit::where('Aktif', 'Y')->orderBy('nama_unit')->get(),
            'jabatan' => MasterJabatan::orderBy('Nama')->get(),
        ]);
    }

    public function simpan(Request $request)
    {
        $parameterEvaluasi = MasterParameterKomplain::daftar('ParameterEvaluasi');

        $request->validate([
            'KodePerusahaan' => 'required|exists:master_perusahaans,id',
            'Tanggal' => 'required|date',
            'NamaPelapor' => 'required|string|max:255',
            'JabatanId' => 'required|exists:master_jabatans,id',
            'UnitId' => [
                'required',
                Rule::exists('master_units', 'id')->where(function ($q) use ($request) {
                    $perusahaan = MasterPerusahaan::find($request->KodePerusahaan);

                    $q->where('Aktif', 'Y')
                        ->where('KodePerusahaan', $perusahaan?->Kode);
                }),
            ],
            'NamaBarang' => 'required|string|max:255',
            'JenisKategori' => 'required|string|max:255',
            'Merk' => 'required|string|max:255',
            'Distributor' => 'required|string|max:255',
            'ParameterEvaluasi' => 'required|array|min:1',
            'ParameterEvaluasi.*' => ['string', Rule::in($parameterEvaluasi)],
            'UraianSingkat' => 'required|string',
            'Lampiran' => 'nullable|array|max:10',
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
            'Lampiran.max' => 'Maksimal 10 file pendukung.',
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

        $perusahaan = MasterPerusahaan::findOrFail($request->KodePerusahaan);
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
            'NamaRS' => $perusahaan->NamaLengkap ?? $perusahaan->Nama,
            'UnitId' => $unit->id,
            'UnitKerja' => $unit->nama_unit,
            'JabatanId' => $jabatan->id,
            'Jabatan' => $jabatan->Nama,
            'Status' => 'Menunggu TTD Kepala Divisi',
            'UserCreate' => $request->NamaPelapor,
        ]);

        $this->buatApproval($komplain);

        $approverBerikutnya = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan')
            ->first();

        $komplain->update(['Status' => $this->tentukanStatus($approverBerikutnya, $komplain)]);

        if ($approverBerikutnya) {
            $this->kirimNotifikasiApproval($komplain, $approverBerikutnya);
        }

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
            'bisaApproval' => $this->boleh(['Kadiv', 'Group Head', 'GH CCP'])
                || $approval->where('UserId', (string) auth()->id())->where('Status', 'Pending')->isNotEmpty(),
            'bisaLogum' => $this->boleh(['Logum', 'SMI']),
            'bisaCcp' => $this->boleh(['CCP', 'GH CCP']),
        ]);
    }

    public function cetak($id)
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

        $pdf = Pdf::loadView('komplain.pdf', [
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
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Komplain-Vendor-' . $komplain->KodeKomplain . '.pdf');
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

        // Jika tahap Logistik Umum / SMI (atau form mengirimkan LangkahAwal)
        if ($request->has('LangkahAwal')) {
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
            ]);
        }

        // Jika tahap CCP (atau form mengirimkan TindakanCcp, KeteranganCcp, atau LampiranCcp)
        if ($request->has('TindakanCcp') || $request->hasFile('LampiranCcp') || $request->filled('KeteranganCcp')) {
            $request->validate([
                'TindakanCcp' => 'required|array|min:1',
                'TindakanCcp.*' => ['string', Rule::in(MasterParameterKomplain::daftar('TindakanCcp'))],
                'KeteranganCcp' => 'nullable|string',
                'LampiranCcp' => 'nullable|array|max:10',
                'LampiranCcp.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx|max:10240',
            ], [
                'TindakanCcp.required' => 'Tindakan CCP wajib dipilih minimal satu.',
                'LampiranCcp.max' => 'Maksimal 10 file pendukung investigasi CCP.',
                'LampiranCcp.*.mimes' => 'Format file pendukung harus berupa gambar (jpg, png, webp) atau dokumen (pdf, doc, docx, xls, xlsx).',
                'LampiranCcp.*.max' => 'Ukuran tiap file maksimal 10 MB.',
            ]);

            if (in_array(MasterParameterKomplain::LAINNYA, $request->TindakanCcp, true)
                && !$request->filled('TindakanCcpLainnya')) {
                return back()->withInput()->withErrors(['TindakanCcpLainnya' => 'Isi keterangan tindakan "Lainnya".']);
            }

            $lampiranCcpBaru = $this->simpanLampiranCcp($request);
            $lampiranCcpLama = (array) ($komplain->LampiranCcp ?? []);
            $lampiranCcpGabung = array_merge($lampiranCcpLama, $lampiranCcpBaru);

            $komplain->update([
                'TindakanCcp' => $request->TindakanCcp,
                'TindakanCcpLainnya' => $request->TindakanCcpLainnya,
                'KeteranganCcp' => $request->filled('KeteranganCcp') ? $this->bersihkanHtml($request->KeteranganCcp) : $komplain->KeteranganCcp,
                'LampiranCcp' => !empty($lampiranCcpGabung) ? $lampiranCcpGabung : null,
                'CcpOleh' => auth()->user()->name,
                'TanggalCcp' => now(),
            ]);
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
            'Status' => $this->tentukanStatus($berikutnya, $komplain->fresh()),
        ]);

        if ($berikutnya) {
            $this->kirimNotifikasiApproval($komplain->fresh(), $berikutnya);
        }

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
        ]);

        // Jika Logistik Umum terdaftar di DokumenApproval, tandai Approved
        $approvalLogum = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Status', 'Pending')
            ->where(function ($q) {
                $q->where('UserId', auth()->id())
                    ->orWhere('Urutan', 4);
            })
            ->first();

        if ($approvalLogum) {
            $approvalLogum->update([
                'Status' => 'Approved',
                'TanggalApprove' => now(),
                'Ttd' => auth()->user()->tandatangan,
            ]);
        }

        $berikutnya = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan')
            ->first();

        $komplain->update([
            'Status' => $this->tentukanStatus($berikutnya, $komplain->fresh()),
        ]);

        if ($berikutnya) {
            $this->kirimNotifikasiApproval($komplain->fresh(), $berikutnya);
        }

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
            'KeteranganCcp' => 'nullable|string',
            'LampiranCcp' => 'nullable|array|max:10',
            'LampiranCcp.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'TindakanCcp.required' => 'Tindakan CCP wajib dipilih minimal satu.',
            'LampiranCcp.max' => 'Maksimal 10 file pendukung investigasi CCP.',
            'LampiranCcp.*.mimes' => 'Format file pendukung harus berupa gambar (jpg, png, webp) atau dokumen (pdf, doc, docx, xls, xlsx).',
            'LampiranCcp.*.max' => 'Ukuran tiap file maksimal 10 MB.',
        ]);

        if (in_array(MasterParameterKomplain::LAINNYA, $request->TindakanCcp, true)
            && !$request->filled('TindakanCcpLainnya')) {
            return back()->withInput()->withErrors(['TindakanCcpLainnya' => 'Isi keterangan tindakan "Lainnya".']);
        }

        $lampiranCcpBaru = $this->simpanLampiranCcp($request);
        $lampiranCcpLama = (array) ($komplain->LampiranCcp ?? []);
        $lampiranCcpGabung = array_merge($lampiranCcpLama, $lampiranCcpBaru);

        $komplain->update([
            'TindakanCcp' => $request->TindakanCcp,
            'TindakanCcpLainnya' => $request->TindakanCcpLainnya,
            'KeteranganCcp' => $request->filled('KeteranganCcp') ? $this->bersihkanHtml($request->KeteranganCcp) : $komplain->KeteranganCcp,
            'LampiranCcp' => !empty($lampiranCcpGabung) ? $lampiranCcpGabung : null,
            'CcpOleh' => auth()->user()->name,
            'TanggalCcp' => now(),
        ]);

        // Jika CCP terdaftar di DokumenApproval, tandai Approved
        $approvalCcp = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Status', 'Pending')
            ->where(function ($q) {
                $q->where('UserId', auth()->id())
                    ->orWhereIn('Urutan', [5, 6]);
            })
            ->first();

        if ($approvalCcp) {
            $approvalCcp->update([
                'Status' => 'Approved',
                'TanggalApprove' => now(),
                'Ttd' => auth()->user()->tandatangan,
            ]);
        }

        $berikutnya = DokumenApproval::where('JenisFormId', $this->formId())
            ->where('DokumenId', $komplain->id)
            ->where('Status', 'Pending')
            ->orderBy('Urutan')
            ->first();

        $komplain->update([
            'Status' => $this->tentukanStatus($berikutnya, $komplain->fresh()),
        ]);

        if ($berikutnya) {
            $this->kirimNotifikasiApproval($komplain->fresh(), $berikutnya);
        }

        $this->catat('Mengisi tindak lanjut CCP', $komplain);

        return back()->with('success', 'Tindakan CCP tersimpan.');
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

    /**
     * @return array<int, array{path: string, nama: string, tipe: string, ukuran: int}>
     */
    private function simpanLampiranCcp(Request $request): array
    {
        $hasil = [];

        foreach ($request->file('LampiranCcp', []) as $file) {
            $path = $file->store('komplain-vendor/ccp', 'public');

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
