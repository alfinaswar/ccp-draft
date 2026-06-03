<?php

namespace App\Http\Controllers;

use App\Models\MasterParameter;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterParameter::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('parameter.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
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
        return view('master.parameter.index');
    }

    public function create()
    {
        return view('master.parameter.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        MasterParameter::create([
            'Nama' => $request->Nama,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master parameter baru: ' . $request->Nama);
        }

        return redirect()->route('parameter.index')->with('success', 'Master parameter berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $parameter = MasterParameter::findOrFail($id);
        return view('master.parameter.edit', compact('parameter'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        $parameter = MasterParameter::findOrFail($id);
        $parameter->update([
            'Nama' => $request->Nama,
            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui master parameter: ' . $request->Nama);
        }

        return redirect()->route('parameter.index')->with('success', 'Master parameter berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $parameter = MasterParameter::find($id);

        if (!$parameter) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master parameter: ' . $parameter->Nama);
        }

        $parameter->delete();

        return response()->json(['status' => 200, 'message' => 'Master parameter berhasil dihapus']);
    }
}
