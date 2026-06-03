<?php

namespace App\Http\Controllers;

use App\Models\MasterPerusahaan;
use Illuminate\Http\Request;
use Laraindo\RupiahFormat;
use Yajra\DataTables\Facades\DataTables;

class MasterPerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterPerusahaan::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('perusahaan.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->editColumn('NominalRap', function ($row) {
                    return RupiahFormat::currency($row->NominalRkap);
                })
                ->editColumn('SisaSkap', function ($row) {
                    return RupiahFormat::currency($row->SisaSkap);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.perusahaan.index');
    }

    public function create()
    {
        return view('master.perusahaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Kode' => 'required|string|max:255',
            'Nama' => 'required|string|max:255',
            'NamaLengkap' => 'required|string|max:255',
            'Deskripsi' => 'nullable|string',
            'Kategori' => 'required',
            'NominalRkap' => 'required',

        ]);

        MasterPerusahaan::create([
            'Kode' => $request->Kode,
            'Nama' => $request->Nama,
            'NamaLengkap' => $request->NamaLengkap,
            'Deskripsi' => $request->Deskripsi,
            'Kategori' => $request->Kategori,
            'NominalRkap' => preg_replace('/\D/', '', $request->NominalRkap),
            'UserCreate' => auth()->user()->name,
        ]);


        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master perusahaan baru: ' . $request->Nama);
        }

        return redirect()->route('perusahaan.index')->with('success', 'Master perusahaan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $perusahaan = MasterPerusahaan::findOrFail($id);
        return view('master.perusahaan.edit', compact('perusahaan'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $request->validate([
            'Kode' => 'required|string|max:255',
            'Nama' => 'required|string|max:255',
            'NamaLengkap' => 'required|string|max:255',
            'Deskripsi' => 'nullable|string',
            'NominalRkap' => 'nullable|string',
            'Kategori' => 'required|string|max:255',

        ]);

        $perusahaan = MasterPerusahaan::findOrFail($id);
        $perusahaan->update([
            'Kode' => $request->Kode,
            'Nama' => $request->Nama,
            'NamaLengkap' => $request->NamaLengkap,
            'Deskripsi' => $request->Deskripsi,
            'Kategori' => $request->Kategori,
            'NominalRkap' => preg_replace('/\D/', '', $request->NominalRkap),
            'UserUpdate' => auth()->user()->name
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui master perusahaan: ' . $request->Nama);
        }

        return redirect()->route('perusahaan.index')->with('success', 'Master perusahaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $perusahaan = MasterPerusahaan::find($id);

        if (!$perusahaan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master perusahaan: ' . $perusahaan->Nama);
        }

        $perusahaan->delete();

        return response()->json(['status' => 200, 'message' => 'Master perusahaan berhasil dihapus']);
    }
}
