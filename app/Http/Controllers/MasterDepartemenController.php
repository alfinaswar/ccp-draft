<?php

namespace App\Http\Controllers;

use App\Models\MasterDepartemen;
use App\Models\MasterPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterDepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterDepartemen::with('getPerusahaan')
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('KodeRs', function ($row) {
                    return optional($row->getPerusahaan)->Nama;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('departemen.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $perusahaan = MasterPerusahaan::where('Kategori', 'ABGROUP')->get();
        return view('master.departemen.index', compact('perusahaan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.departemen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        do {
            $count = MasterDepartemen::count() + 1;
            $kodeDepartemen = 'DEP' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $exists = MasterDepartemen::where('KodeDepartemen', $kodeDepartemen)->exists();
            if ($exists) {
                MasterDepartemen::whereRaw('1=1')->update(['id' => \DB::raw('id')]); // force reload
            }
        } while ($exists);

        $departemen = MasterDepartemen::create([
            'KodeDepartemen' => $kodeDepartemen,
            'Nama' => $request->Nama,
            // 'KodePerusahaan' => auth()->user()->kodeperusahaan,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master departemen baru: ' . $request->Nama);
        }
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $departemen = MasterDepartemen::findOrFail($id);
        return view('master.departemen.edit', compact('departemen'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);
        $departemen = MasterDepartemen::findOrFail($id);
        $departemen->update($request->all());

        $oldDepartemen = $departemen->getOriginal();

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'old' => $oldDepartemen,
                    'new' => $departemen->getAttributes(),
                ])
                ->log('Mengupdate master departemen: ' . $request->Nama);
        }
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sinkron(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $kodeRS = $request->Kode ?? auth()->user()->kodeperusahaan;
        $dbMap = MasterPerusahaan::where('Kode', $kodeRS)->first()->Koneksi;
        $selectdb = $dbMap ?? null;
        if (!$selectdb) {
            return response()->json(['success' => false, 'message' => 'Kode RS tidak valid'], 400);
        }
        try {
            $dataItem = DB::connection($selectdb)
                ->table('departemen')
                ->where('NA', 'N')
                ->get();
            foreach ($dataItem as $item) {
                MasterDepartemen::updateOrCreate(
                    [
                        'IdDepartemen' => $item->DepartemenID,
                        'KodePerusahaan' => $kodeRS,
                    ],
                    [
                        'KodeDepartemen' => $item->KodeDepartemen ?? null,
                        'Nama' => $item->Nama ?? null,
                        'UserCreate' => auth()->user()->name ?? null,
                        'UserUpdate' => auth()->user()->name ?? null,
                        'UserDelete' => null,
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Sinkronisasi berhasil!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterDepartemen  $MasterDepartemen
     * @return \Illuminate\Http\Response
     */
    public function show(MasterDepartemen $MasterDepartemen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MasterDepartemen  $MasterDepartemen
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterDepartemen  $MasterDepartemen
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MasterDepartemen  $MasterDepartemen
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $departemen = MasterDepartemen::find($id);

        if (!$departemen) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master departemen: ' . $departemen->Nama);
        }

        $departemen->delete();

        return response()->json(['status' => 200, 'message' => 'Master departemen berhasil dihapus']);
    }
}
