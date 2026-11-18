<?php

namespace App\Http\Controllers;

use App\Models\MasterPerusahaan;
use App\Models\MasterUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MasterUnitController extends Controller
{
    /**
     * Kode semu untuk unit bersama: unit tanpa KodePerusahaan,
     * tampil di form komplain semua RS.
     */
    private const BERSAMA = 'BERSAMA';

    public function index()
    {
        return view('master.unit.index', [
            'perusahaan' => MasterPerusahaan::orderBy('Nama')->get(),
            'jumlah' => MasterUnit::whereNotNull('KodePerusahaan')
                ->selectRaw('KodePerusahaan, count(*) as total')
                ->groupBy('KodePerusahaan')
                ->pluck('total', 'KodePerusahaan'),
            'jumlahBersama' => MasterUnit::whereNull('KodePerusahaan')->count(),
        ]);
    }

    public function perusahaan(Request $request, $kode)
    {
        if ($kode === self::BERSAMA) {
            $nama = 'Unit Tanpa RS (belum dipetakan)';
        } else {
            $nama = MasterPerusahaan::where('Kode', $kode)->value('Nama');
            abort_if(!$nama, 404);
        }

        if ($request->ajax()) {
            $data = MasterUnit::orderBy('nama_unit');

            if ($kode === self::BERSAMA) {
                $data->whereNull('KodePerusahaan');
            } else {
                $data->where('KodePerusahaan', $kode);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('AktifBadge', function ($row) {
                    return $row->Aktif === 'Y'
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) use ($kode) {
                    $encryptedId = encrypt($row->id);

                    return '
                        <a href="' . route('unit.edit', ['id' => $encryptedId, 'perusahaan' => $kode]) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['AktifBadge', 'action'])
                ->make(true);
        }

        return view('master.unit.perusahaan', [
            'kode' => $kode,
            'nama' => $nama,
        ]);
    }

    public function create(Request $request)
    {
        return view('master.unit.create', [
            'perusahaan' => MasterPerusahaan::orderBy('Nama')->get(),
            'unitList' => MasterUnit::orderBy('nama_unit')->get(),
            'kembali' => $request->query('perusahaan', self::BERSAMA),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        MasterUnit::create([
            ...$validated,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()->causedBy(auth()->id())->withProperties(['ip' => request()->ip()])
                ->log('Menambah master unit: ' . $request->nama_unit);
        }

        return redirect()->to($this->tujuanKembali($request))
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        $unit = MasterUnit::findOrFail(decrypt($id));

        return view('master.unit.edit', [
            'data' => $unit,
            'perusahaan' => MasterPerusahaan::orderBy('Nama')->get(),
            'unitList' => MasterUnit::where('id', '!=', $unit->id)->orderBy('nama_unit')->get(),
            'kembali' => $request->query('perusahaan')
                ?: ($unit->KodePerusahaan ?? self::BERSAMA),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validasi($request);

        $unit = MasterUnit::findOrFail(decrypt($id));

        if (($validated['parent_id'] ?? null) && (int) $validated['parent_id'] === $unit->id) {
            return back()->withInput()->withErrors(['parent_id' => 'Unit tidak dapat menjadi induk dirinya sendiri.']);
        }

        $unit->update([
            ...$validated,
            'parent_id' => $validated['parent_id'] ?? null,
            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()->causedBy(auth()->id())->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui master unit: ' . $request->nama_unit);
        }

        return redirect()->to($this->tujuanKembali($request))
            ->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = MasterUnit::find(decrypt($id));

        if (!$unit) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        $unit->delete();

        return response()->json(['status' => 200, 'message' => 'Unit berhasil dihapus']);
    }

    /**
     * Kembali ke daftar unit perusahaan asal, bukan ke daftar perusahaan.
     */
    private function tujuanKembali(Request $request): string
    {
        $kembali = $request->input('kembali');

        return $kembali
            ? route('unit.perusahaan', $kembali)
            : route('unit.index');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_unit' => 'required|string|max:255',
            'kode_unit' => 'nullable|string|max:20',
            'KodePerusahaan' => 'nullable|exists:master_perusahaans,Kode',
            'parent_id' => 'nullable|exists:master_units,id',
            'Aktif' => ['required', Rule::in(['Y', 'N'])],
        ], [
            'nama_unit.required' => 'Nama unit wajib diisi.',
            'KodePerusahaan.exists' => 'Perusahaan tidak ditemukan.',
        ]);
    }
}
