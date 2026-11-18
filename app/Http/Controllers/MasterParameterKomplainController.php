<?php

namespace App\Http\Controllers;

use App\Models\MasterParameterKomplain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MasterParameterKomplainController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterParameterKomplain::orderBy('Jenis')->orderBy('Urutan');

            if ($request->filled('jenis')) {
                $data->where('Jenis', $request->jenis);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('JenisText', function ($row) {
                    return MasterParameterKomplain::JENIS[$row->Jenis] ?? $row->Jenis;
                })
                ->addColumn('AktifBadge', function ($row) {
                    return $row->Aktif === 'Y'
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);

                    return '
                        <a href="' . route('parameter-komplain.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
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

        return view('master.parameter-komplain.index', [
            'jenisList' => MasterParameterKomplain::JENIS,
        ]);
    }

    public function create()
    {
        return view('master.parameter-komplain.create', [
            'jenisList' => MasterParameterKomplain::JENIS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        MasterParameterKomplain::create([
            ...$validated,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()->causedBy(auth()->id())->withProperties(['ip' => request()->ip()])
                ->log('Menambah parameter komplain: ' . $request->Nama);
        }

        return redirect()->route('parameter-komplain.index')
            ->with('success', 'Parameter komplain berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $parameter = MasterParameterKomplain::findOrFail(decrypt($id));

        return view('master.parameter-komplain.edit', [
            'parameter' => $parameter,
            'jenisList' => MasterParameterKomplain::JENIS,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validasi($request);

        $parameter = MasterParameterKomplain::findOrFail(decrypt($id));
        $parameter->update([
            ...$validated,
            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()->causedBy(auth()->id())->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui parameter komplain: ' . $request->Nama);
        }

        return redirect()->route('parameter-komplain.index')
            ->with('success', 'Parameter komplain berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $parameter = MasterParameterKomplain::find(decrypt($id));

        if (!$parameter) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        $parameter->delete();

        return response()->json(['status' => 200, 'message' => 'Parameter komplain berhasil dihapus']);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'Jenis' => ['required', Rule::in(array_keys(MasterParameterKomplain::JENIS))],
            'Nama' => 'required|string|max:255',
            'Urutan' => 'nullable|integer|min:0',
            'Aktif' => ['required', Rule::in(['Y', 'N'])],
        ], [
            'Jenis.required' => 'Jenis parameter wajib dipilih.',
            'Nama.required' => 'Nama parameter wajib diisi.',
        ]);
    }
}
