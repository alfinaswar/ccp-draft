<?php

namespace App\Http\Controllers;

use App\Models\KonversiItem;
use App\Models\MasterBarang;
use App\Models\MasterMerk;
use App\Models\MasterSatuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterSatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterSatuan::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('satuan.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.satuan.index');
    }

    public function create()
    {
        $satuan = MasterSatuan::get();
        $merk = MasterMerk::get();
        return view('master.satuan.create', compact('satuan', 'merk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NamaSatuan' => 'required|string|max:255'
        ]);

        MasterSatuan::create([
            'NamaSatuan' => $request->NamaSatuan,
            'UserCreate' => auth()->user()->name,
        ]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $satuan = MasterSatuan::findOrFail($id);
        return view('master.satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NamaSatuan' => 'required|string|max:255'
        ]);

        $satuan = MasterSatuan::findOrFail($id);
        $satuan->update([
            'NamaSatuan' => $request->NamaSatuan,
            'UserUpdate' => auth()->user()->name
        ]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $satuan = MasterSatuan::find($id);

        if (!$satuan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }
        $terpakaiDiProduk = MasterBarang::where('Satuan', $id)->exists();

        if ($terpakaiDiProduk) {
            return response()->json([
                'status' => 403,
                'message' => 'Satuan tidak dapat dihapus karena masih digunakan di master produk.'
            ]);
        }

        $satuan->delete();
        return response()->json(['status' => 200, 'message' => 'Satuan berhasil dihapus']);
    }
}
