<?php

namespace App\Http\Controllers;

use App\Models\MasterPerusahaan;
use App\Models\Rkap;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RkapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterPerusahaan::with('getRkap')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->Kode);
                    return '
                        <a href="' . route('rkap.history', $encryptedId) . '" class="btn btn-sm btn-success">
                            <i class="fa fa-history"></i> History RKAP
                        </a>
                    ';
                })
                ->addColumn('NominalRkap', function ($row) {
                    $latestRkap = $row->getRkap->sortByDesc('Tahun')->first();
                    $nominalMedis = ($latestRkap && isset($latestRkap->NominalRkap))
                        ? 'Rp ' . number_format((float) $latestRkap->NominalRkap, 0, ',', '.')
                        : '-';
                    $nominalUmum = ($latestRkap && isset($latestRkap->NominalRkapUmum))
                        ? 'Rp ' . number_format((float) $latestRkap->NominalRkapUmum, 0, ',', '.')
                        : '-';
                    return "{$nominalMedis}<br><hr style='margin:2px 0;'>{$nominalUmum}";
                })
                ->addColumn('SisaSkap', function ($row) {
                    $latestRkap = $row->getRkap->sortByDesc('Tahun')->first();
                    $sisaMedis = ($latestRkap && isset($latestRkap->SisaRkap))
                        ? 'Rp ' . number_format((float) $latestRkap->SisaRkap, 0, ',', '.')
                        : '-';
                    $sisaUmum = ($latestRkap && isset($latestRkap->SisaRkapUmum))
                        ? 'Rp ' . number_format((float) $latestRkap->SisaRkapUmum, 0, ',', '.')
                        : '-';
                    return "{$sisaMedis}<br><hr style='margin:2px 0;'>{$sisaUmum}";
                })

                ->rawColumns(['action', 'NominalRkap', 'SisaSkap'])
                ->make(true);
        }
        return view('perencanaan-dan-anggaran.rkap.index');
    }

    public function create($id)
    {
        $id = decrypt($id);
        // dd($id);
        return view('perencanaan-dan-anggaran.rkap.create', compact('id'));
    }

    public function store(Request $request, $id)
    {
        $id = decrypt($id);
        $request->validate([
            'Tahun' => 'required|string|max:10',
            'NominalRkap' => 'nullable|string',
            'NominalRkapUmum' => 'nullable|string',
        ]);

        $rkap = Rkap::create([
            'PerusahaanId' => $id,
            'Tahun' => $request->Tahun,
            'NominalRkap' => preg_replace('/\D/', '', $request->NominalRkap),
            'NominalRkapUmum' => preg_replace('/\D/', '', $request->NominalRkapUmum),
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah data RKAP baru, PerusahaanId: ' . $request->PerusahaanId . ', Tahun: ' . $request->Tahun . ', Jenis: ' . $request->Jenis);
        }

        return redirect()->back()->with('success', 'Data RKAP berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $rkap = Rkap::findOrFail($id);
        return view('rkap.edit', compact('rkap'));
    }

    public function update(Request $request)
    {
        $id = decrypt($request->id);
        // dd($id);
        $request->validate([
            'Tahun' => 'required|string|max:10',
            'NominalRkap' => 'nullable|string',
            'NominalRkapUmum' => 'nullable|string',
        ]);

        $rkap = Rkap::findOrFail($id);

        $rkap->Tahun = $request->Tahun;
        $rkap->NominalRkap = preg_replace('/\D/', '', $request->NominalRkap);
        $rkap->NominalRkapUmum = preg_replace('/\D/', '', $request->NominalRkapUmum);
        $rkap->UserUpdate = auth()->user()->name;
        $rkap->save();

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui data RKAP, RKAP ID: ' . $id . ', Tahun: ' . $request->Tahun);
        }

        return redirect()->route('rkap.index')->with('success', 'Data RKAP berhasil diperbarui.');
    }
    public function history(Request $request, $id)
    {
        $id = decrypt($id);
        if ($request->ajax()) {
            $data = Rkap::with('getPerusahaan')->where('PerusahaanId', $id)->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    $nominalMedis = $row->NominalRkap ?? 0;
                    $nominalUmum = $row->NominalRkapUmum ?? 0;

                    return '<button class="btn btn-sm btn-warning btn-edit-rkap"
                data-bs-toggle="modal"
                data-bs-target="#modalEditRkap"
                data-id="' . $encryptedId . '"
                data-tahun="' . $row->Tahun . '"
                data-nominalmedis="' . $nominalMedis . '"
                data-nominalumum="' . $nominalUmum . '">
                <i class="bi bi-pencil"></i> Edit
            </button>';
                })
                ->addColumn('NominalRkap', function ($row) {
                    $nominalMedis = ($row->NominalRkap !== null)
                        ? 'Rp ' . number_format((float) $row->NominalRkap, 0, ',', '.')
                        : 'Rp 0';
                    $nominalUmum = ($row->NominalRkapUmum !== null)
                        ? 'Rp ' . number_format((float) $row->NominalRkapUmum, 0, ',', '.')
                        : 'Rp 0';

                    return "<div ><span>{$nominalMedis}</span><br><hr style='margin:2px 0;height:1px;border:none;border-top:1px solid #eee;'><span>{$nominalUmum}</span></div>";
                })
                ->addColumn('SisaRkap', function ($row) {
                    $sisaMedis = ($row->SisaRkap !== null)
                        ? 'Rp ' . number_format((float) $row->SisaRkap, 0, ',', '.')
                        : 'Rp 0';
                    $sisaUmum = ($row->SisaRkapUmum !== null)
                        ? 'Rp ' . number_format((float) $row->SisaRkapUmum, 0, ',', '.')
                        : 'Rp 0';

                    return "<div ><span>{$sisaMedis}</span><br><hr style='margin:2px 0;height:1px;border:none;border-top:1px solid #eee;'><span>{$sisaUmum}</span></div>";
                })

                ->rawColumns(['action', 'NominalRkap', 'SisaRkap'])
                ->make(true);
        }
        return view('perencanaan-dan-anggaran.rkap.history');
    }
    public function destroy($id)
    {
        $id = decrypt($id);
        $rkap = Rkap::find($id);

        if (!$rkap) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus data RKAP, PerusahaanId: ' . $rkap->PerusahaanId . ', Tahun: ' . $rkap->Tahun . ', Jenis: ' . $rkap->Jenis);
        }

        $rkap->delete();

        return response()->json(['status' => 200, 'message' => 'Data RKAP berhasil dihapus']);
    }
}
