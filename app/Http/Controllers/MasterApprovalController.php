<?php

namespace App\Http\Controllers;

use App\Models\DokumenApproval;
use App\Models\MasterApproval;
use App\Models\MasterDepartemen;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterPerusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Laraindo\RupiahFormat;
use Yajra\DataTables\DataTables;

class MasterApprovalController extends Controller
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
                    $encryptedId = encrypt($row->Kode);
                    return '
                        <a href="' . route('master-approval.create', $encryptedId) . '" class="btn btn-sm btn-success">
                            <i class="fa fa-pen"></i> Atur TTD
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.pengaturan-ttd.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $id)
    {
        $id = decrypt($id);
        // dd($id);
        $perusahaan = MasterPerusahaan::where('Kode', $id)->first();
        $form = MasterForm::with([
            'getApproval' => function ($query) use ($id) {
                $query->where('KodePerusahaan', $id)->orderBy('Urutan', 'asc');

            }
        ])->get();
        // dd($form);
        return view('master.pengaturan-ttd.create', compact('form', 'perusahaan'));
    }

    public function aturTtd($id, $KodePerusahaan)
    {
        $id = decrypt($id);
        $KodePerusahaan = decrypt($KodePerusahaan);
        // dd($KodePerusahaan);
        $form = MasterForm::with([
            'getApproval' => function ($query) use ($KodePerusahaan) {
                $query->where('KodePerusahaan', $KodePerusahaan)->orderBy('Urutan', 'asc');

            }
        ])->where('id', $id)->first();
        // dd($form);
        $user = User::with('getJabatan', 'getDepartemen')->get();
        $jabatan = MasterJabatan::get();
        $departemen = MasterDepartemen::get();
        return view('master.pengaturan-ttd.atur-ttd', compact('KodePerusahaan', 'form', 'user', 'jabatan', 'departemen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'KodePerusahaan' => 'required',
            'JenisForm' => 'required',
            'Urutan' => 'required',
        ]);

        $data = MasterApproval::where('KodePerusahaan', $request->KodePerusahaan)
            ->where('JenisForm', $request->JenisForm)
            ->delete();
        // dd($data);

        $urutanList = is_array($request->Urutan) ? $request->Urutan : [$request->Urutan];
        $userIdList = is_array($request->UserId) ? $request->UserId : [$request->UserId];
        $jabatanIdList = is_array($request->JabatanId) ? $request->JabatanId : [$request->JabatanId ?? null];
        $departemenIdList = is_array($request->DepartemenId) ? $request->DepartemenId : [$request->DepartemenId ?? null];
        $wajibList = is_array($request->Wajib) ? $request->Wajib : [$request->Wajib ?? null];
        // Proses NamaJabatan jika disertakan dalam request
        $namaJabatanList = is_array($request->NamaJabatan) ? $request->NamaJabatan : [$request->NamaJabatan ?? null];


        foreach ($urutanList as $key => $urutan) {
            MasterApproval::create([
                'Urutan' => $urutan,
                'KodePerusahaan' => $request->KodePerusahaan,
                'JenisForm' => $request->JenisForm,
                'UserId' => $userIdList[$key] ?? null,
                'JabatanId' => $jabatanIdList[$key] ?? null,
                'NamaJabatan' => $namaJabatanList[$key] ?? null,
                'DepartemenId' => $departemenIdList[$key] ?? null,
                'Wajib' => $wajibList[$key] ?? null,
                'UserCreate' => auth()->id(),
            ]);
        }

        return redirect()
            ->route('master-approval.create', encrypt($request->KodePerusahaan))
            ->with('success', 'Approval berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterApproval $masterApproval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterApproval $masterApproval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterApproval $masterApproval)
    {
        //
    }
    public function validasi($id)
    {
        $approval = DokumenApproval::with('getUser')->where('ApprovalToken', $id)
            ->where('Status', 'Approved')
            ->first();

        return !$approval
            ? view('approval.validasi', compact('approval'))
            : view('approval.validasi-valid', compact('approval'));
    }

    public function destroy(MasterApproval $masterApproval)
    {
        //
    }
}
