<?php

namespace App\Http\Controllers;

use App\Models\MasterForm;
use App\Models\MasterJenisPengajuan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterJenisPengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterJenisPengajuan::with('getForm')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('jenis-pengajuan.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->addColumn('Form', function ($row) {
                    return $row->getForm ? $row->getForm->Nama ?? '-' : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.jenis-pengajuan.index');
    }

    public function create()
    {
        $daftarForm = MasterForm::get();
        return view('master.jenis-pengajuan.create', compact('daftarForm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'Form' => 'nullable|string|max:200',
        ]);

        MasterJenisPengajuan::create([
            'Nama' => $request->Nama,
            'Form' => $request->Form,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master jenis pengajuan baru: ' . $request->Nama);
        }

        return redirect()->route('jenis-pengajuan.index')->with('success', 'Master jenis pengajuan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $jenisPengajuan = MasterJenisPengajuan::findOrFail($id);
        return view('master.jenis-pengajuan.edit', compact('jenisPengajuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'Form' => 'nullable|string|max:200',
        ]);

        $jenisPengajuan = MasterJenisPengajuan::findOrFail($id);
        $jenisPengajuan->update([
            'Nama' => $request->Nama,
            'Form' => $request->Form,
            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui master jenis pengajuan: ' . $request->Nama);
        }

        return redirect()->route('jenis-pengajuan.index')->with('success', 'Master jenis pengajuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $jenisPengajuan = MasterJenisPengajuan::find($id);

        if (!$jenisPengajuan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master jenis pengajuan: ' . $jenisPengajuan->Nama);
        }

        $jenisPengajuan->delete();

        return response()->json(['status' => 200, 'message' => 'Master jenis pengajuan berhasil dihapus']);
    }
}
