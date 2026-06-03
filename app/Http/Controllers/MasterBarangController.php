<?php

namespace App\Http\Controllers;

use App\Models\ListVendorDetail;
use App\Models\MasterBarang;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterMerk;
use App\Models\MasterSatuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterBarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MasterBarang::with('getSatuan', 'getMerk', 'getJenis')->latest();
            $jenisId = $request->input('jenis');
            if (!empty($jenisId)) {
                $query->where('Jenis', $jenisId);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('barang.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->editColumn('Satuan', function ($row) {
                    return optional($row->getSatuan)->NamaSatuan ?? '-';
                })
                ->editColumn('Merek', function ($row) {
                    return optional($row->getMerk)->Nama ?? '-';
                })
                ->editColumn('Jenis', function ($row) {
                    return optional($row->getJenis)->Nama ?? '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $jenis = MasterJenisPengajuan::get();
        return view('master.barang.index', compact('jenis'));
    }

    public function create()
    {
        $satuan = MasterSatuan::get();
        $merekList = MasterMerk::get();
        $jenis = MasterJenisPengajuan::get();
        return view('master.barang.create', compact('satuan', 'merekList', 'jenis'));
    }

    public function store(Request $request)
    {
        $rules = [
            'Nama' => 'required|string|max:255',
            'Jenis' => 'required|string|max:190',
            'Satuan' => 'required|string|max:190',
        ];
        if ($request->Jenis == '1' || $request->Jenis == '2') {
            $rules['Merek'] = 'required|string|max:190';
            $rules['Tipe'] = 'nullable|string|max:190';
        } elseif ($request->Jenis == '3') {
            $rules['Merek'] = 'nullable|string|max:190';
            $rules['Tipe'] = 'nullable|string|max:190';
        }
        $validatedData = $request->validate($rules);
        MasterBarang::create([
            'KodeBarang' => $this->generateKode($request->Jenis),
            'Nama' => $validatedData['Nama'],
            'Jenis' => $validatedData['Jenis'],
            'Satuan' => $validatedData['Satuan'],
            'Merek' => $validatedData['Merek'] ?? null,
            'Tipe' => $validatedData['Tipe'] ?? null,
            'KodePerusahaan' => auth()->user()->kodeperusahaan,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master barang baru: ' . $request->Nama);
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    private function generateKode($jenis)
    {
        $bulan = date('m');
        $tahun = date('y');
        $jenis = strtoupper($jenis);
        if ($jenis === '1') {
            $prefix = "M{$tahun}{$bulan}";
        } elseif ($jenis === '2') {
            $prefix = "U{$tahun}{$bulan}";
        } elseif ($jenis === '3') {
            $prefix = "P{$tahun}{$bulan}";
        } else {
            $prefix = "X{$tahun}{$bulan}";
        }
        $count = MasterBarang::withTrashed()
            ->where('KodeBarang', 'like', $prefix . '%')
            ->count();

        $nextNumber = $count + 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function edit($id)
    {
        $barang = MasterBarang::findOrFail($id);
        $satuan = MasterSatuan::get();
        $merekList = MasterMerk::get();
        $jenis = MasterJenisPengajuan::get();
        return view('master.barang.edit', compact('barang', 'satuan', 'merekList', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        $barang = MasterBarang::findOrFail($id);

        $rules = [
            'Nama' => 'required|string|max:255',
            'Jenis' => 'required|string|max:190',
            'Satuan' => 'required|string|max:190',
        ];
        if ($request->Jenis == '1' || $request->Jenis == '2') {
            $rules['Merek'] = 'required|string|max:190';
            $rules['Tipe'] = 'nullable|string|max:190';
        } elseif ($request->Jenis == '3') {
            $rules['Merek'] = 'nullable|string|max:190';
            $rules['Tipe'] = 'nullable|string|max:190';
        }
        $validatedData = $request->validate($rules);

        $barang->update([
            'Nama' => $request->Nama,
            'Jenis' => $request->Jenis,
            'Satuan' => $request->Satuan,
            'Merek' => $request->has('Merek') ? $request->Merek : null,
            'Tipe' => $request->has('Tipe') ? $request->Tipe : null,
            'KodePerusahaan' => $request->KodePerusahaan,
            'UserUpdate' => auth()->user()->name,
        ]);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($barang)
            ->withProperties(['ip' => request()->ip()])
            ->log('Memperbarui master barang: ' . $request->Nama);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = MasterBarang::find($id);

        if (!$barang) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        $digunakanDiPengajuan = ListVendorDetail::where('NamaBarang', $barang->id)
            ->exists();

        if ($digunakanDiPengajuan) {
            return response()->json([
                'status' => 400,
                'message' => 'Barang tidak dapat dihapus karena sudah digunakan di pengajuan.'
            ]);
        }

        $barang->UserDelete = auth()->user()->name;
        $barang->save();

        $barang->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($barang)
            ->withProperties(['ip' => request()->ip()])
            ->log('Menghapus master barang: ' . $barang->Nama);

        return response()->json(['status' => 200, 'message' => 'Barang berhasil dihapus']);
    }
}
