<?php

namespace App\Http\Controllers;

use App\Models\MasterJabatan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterJabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterJabatan::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('jabatan.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.jabatan.index');
    }

    public function create()
    {
        return view('master.jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255'
        ]);

        MasterJabatan::create([
            'Nama' => $request->Nama,
            'UserCreate' => auth()->user()->name,
        ]);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jabatan = MasterJabatan::findOrFail($id);
        return view('master.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required|string|max:255'
        ]);

        $jabatan = MasterJabatan::findOrFail($id);
        $jabatan->update([
            'Nama' => $request->Nama,
            'UserUpdate' => auth()->user()->name
        ]);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jabatan = MasterJabatan::find($id);

        if (!$jabatan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        $jabatan->delete();
        return response()->json(['status' => 200, 'message' => 'Jabatan berhasil dihapus']);
    }
}
