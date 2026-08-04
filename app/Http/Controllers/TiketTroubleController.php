<?php

namespace App\Http\Controllers;

use App\Models\TiketTrouble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TiketTroubleController extends Controller
{
    private function buatKodeTiket(): string
    {
        do {
            $kodeTiket = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (TiketTrouble::where('KodeTiket', $kodeTiket)->exists());

        return $kodeTiket;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fix for undefined method 'query()' on TiketTrouble::with():
            if (auth()->user()->roles && auth()->user()->roles->contains('name', 'Admin')) {
                $data = TiketTrouble::with('getPerusahaan');
            } else {
                $data = TiketTrouble::with('getPerusahaan')->where('KodePerusahaan', auth()->user()->KodePerusahaan);
            }

            if ($request->filled('cari')) {
                $q = $request->cari;
                $data->where(function($sub) use ($q) {
                    $sub->where('KodeTiket', 'like', "%{$q}%")
                        ->orWhere('Nama', 'like', "%{$q}%")
                        ->orWhere('NoHp', 'like', "%{$q}%")
                        ->orWhere('Judul', 'like', "%{$q}%")
                        ->orWhere('KodePerusahaan', 'like', "%{$q}%")
                        ->orWhere('DiresponOleh', 'like', "%{$q}%");
                });
            }
            if ($request->filled('status')) {
                $data->where('Status', $request->status);
            }
            if ($request->filled('prioritas')) {
                $data->where('Prioritas', $request->prioritas);
            }

            $data = $data->orderByDesc('created_at');

            return datatables()->of($data)
                ->addIndexColumn()
                // Tambah kolom Status dengan badge warna
                ->addColumn('Status', function ($row) {
                    $badgeClass = 'secondary';
                    switch (strtolower($row->Status)) {
                        case 'open':
                            $badgeClass = 'warning';
                            break;
                        case 'progress':
                        case 'in progress':
                            $badgeClass = 'info';
                            break;
                        case 'closed':
                        case 'done':
                        case 'selesai':
                            $badgeClass = 'success';
                            break;
                        case 'rejected':
                        case 'ditolak':
                            $badgeClass = 'danger';
                            break;
                        default:
                            $badgeClass = 'secondary';
                    }
                    return '<span class="badge bg-' . $badgeClass . '">' . e($row->Status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('ticket.show', $encryptedId) . '" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i> Lihat
                        </a>
                        <a href="' . route('ticket.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    ';
                })

                ->editColumn('KodePerusahaan', function ($row) {

                    if (method_exists($row, 'getPerusahaan') && $row->getPerusahaan && $row->getPerusahaan->Nama) {
                        return e($row->getPerusahaan->NamaLengkap);
                    }
                    return e($row->KodePerusahaan);
                })

                ->rawColumns(['Status', 'action'])
                ->make(true);
        }

        return view('tiket.index');
    }

    public function create()
    {
        return view('tiket.create');
    }
    public function show($id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('ticket.index')->with('error', 'ID tiket tidak valid.');
        }

        $tiketTrouble = TiketTrouble::findOrFail($decryptedId);

        return view('tiket.show', compact('tiketTrouble'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama' => 'required|string|max:255',
            'NoHp' => 'nullable|string|max:20',
            'Prioritas' => [
                'required',
                Rule::in(['Rendah', 'Sedang', 'Tinggi', 'Darurat'])
            ],
            'Judul' => 'required|string|max:255',
            'Deskripsi' => 'required|string',
            // KodePerusahaan diisi otomatis, tidak perlu validasi dari request
            'FilePendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
        ], [
            'Nama.required' => 'Nama wajib diisi.',
            'Prioritas.required' => 'Prioritas wajib dipilih.',
            'Judul.required' => 'Judul masalah wajib diisi.',
            'Deskripsi.required' => 'Deskripsi masalah wajib diisi.',
            'FilePendukung.mimes' => 'File pendukung harus berupa jpg, jpeg, png, pdf, doc, docx, xls, xlsx, atau zip.',
            'FilePendukung.max' => 'Ukuran file pendukung maksimal 5 MB.',
        ]);
        // dd($validated);
        $validated['KodeTiket'] = $this->buatKodeTiket();
        $validated['Status'] = 'Open';
        $validated['KodePerusahaan'] = auth()->user()->KodePerusahaan; // Set kode perusahaan dari user

        if ($request->hasFile('FilePendukung')) {
            $validated['FilePendukung'] = $request->file('FilePendukung')->store('tiket-trouble/file-pendukung', 'public');
        }

        TiketTrouble::create($validated);

        return redirect()
            ->route('ticket.index')
            ->with('sukses', 'Ticket trouble berhasil dibuat.');
    }

    public function edit(TiketTrouble $tiketTrouble)
    {
        return view('tiket.edit', compact('tiketTrouble'));
    }

    public function update(Request $request, TiketTrouble $tiketTrouble)
    {
        $validated = $request->validate([
            'Nama' => 'required|string|max:255',
            'NoHp' => 'nullable|string|max:20',
            'Prioritas' => [
                'required',
                Rule::in(['Rendah', 'Sedang', 'Tinggi', 'Darurat'])
            ],
            'Judul' => 'required|string|max:255',
            'Deskripsi' => 'required|string',
            'Status' => [
                'required',
                Rule::in(['Open', 'In Progress', 'Completed', 'Closed'])
            ],
            'KodePerusahaan' => 'nullable|string|max:255',
            'Respon' => 'nullable|string',
            'DiresponOleh' => 'nullable|string|max:255',
            'FilePendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
            'LampiranRespon' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
        ], [
            'Nama.required' => 'Nama wajib diisi.',
            'Prioritas.required' => 'Prioritas wajib dipilih.',
            'Judul.required' => 'Judul masalah wajib diisi.',
            'Deskripsi.required' => 'Deskripsi masalah wajib diisi.',
            'Status.required' => 'Status wajib dipilih.',
            'FilePendukung.mimes' => 'File pendukung harus berupa jpg, jpeg, png, pdf, doc, docx, xls, xlsx, atau zip.',
            'FilePendukung.max' => 'Ukuran file pendukung maksimal 5 MB.',
            'LampiranRespon.mimes' => 'Lampiran respon harus berupa jpg, jpeg, png, pdf, doc, docx, xls, xlsx, atau zip.',
            'LampiranRespon.max' => 'Ukuran lampiran respon maksimal 5 MB.',
        ]);

        if ($request->hasFile('FilePendukung')) {
            if ($tiketTrouble->FilePendukung) {
                Storage::disk('public')->delete($tiketTrouble->FilePendukung);
            }

            $validated['FilePendukung'] = $request->file('FilePendukung')->store('tiket-trouble/file-pendukung', 'public');
        } else {
            unset($validated['FilePendukung']);
        }

        if ($request->hasFile('LampiranRespon')) {
            if ($tiketTrouble->LampiranRespon) {
                Storage::disk('public')->delete($tiketTrouble->LampiranRespon);
            }

            $validated['LampiranRespon'] = $request->file('LampiranRespon')->store('tiket-trouble/lampiran-respon', 'public');
        } else {
            unset($validated['LampiranRespon']);
        }

        $tiketTrouble->update($validated);

        return redirect()
            ->route('tiket.index')
            ->with('sukses', 'Ticket trouble berhasil diperbarui.');
    }

    public function destroy(TiketTrouble $tiketTrouble)
    {
        $tiketTrouble->delete();

        return redirect()
            ->route('tiket.index')
            ->with('sukses', 'Ticket trouble berhasil dihapus.');
    }
}
