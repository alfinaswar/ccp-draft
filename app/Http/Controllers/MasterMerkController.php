<?php

namespace App\Http\Controllers;

use App\Models\MasterMerk;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterMerkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterMerk::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('merk.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.merk.index');
    }

    public function create()
    {
        return view('master.merk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        MasterMerk::create([
            'Nama' => $request->Nama,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master merk baru: ' . $request->Nama);
        }

        return redirect()->route('merk.index')->with('success', 'Master merk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $merk = MasterMerk::findOrFail($id);
        return view('master.merk.edit', compact('merk'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([

            'Nama' => 'required|string|max:255',

        ]);

        $merk = MasterMerk::findOrFail($id);
        $merk->update([

            'Nama' => $request->Nama,

            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui master merk: ' . $request->Nama);
        }

        return redirect()->route('merk.index')->with('success', 'Master merk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $merk = MasterMerk::find($id);

        if (!$merk) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master merk: ' . $merk->Nama);
        }

        $merk->delete();

        return response()->json(['status' => 200, 'message' => 'Master merk berhasil dihapus']);
    }
}
