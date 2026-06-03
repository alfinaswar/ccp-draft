<?php

namespace App\Http\Controllers;

use App\Models\MasterForm;
use App\Models\MasterParameter;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterForm::where('KodePerusahaan');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('nama-form.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->addColumn('Parameter', function ($row) {
                    $parameterLabels = [];
                    if (!empty($row->Parameter) && is_array($row->Parameter)) {
                        $paramIds = $row->Parameter;
                    } else {
                        $paramIds = [];
                        if (!empty($row->Parameter)) {
                            $decoded = json_decode($row->Parameter, true);
                            if (is_array($decoded)) {
                                $paramIds = $decoded;
                            }
                        }
                    }
                    if (!empty($paramIds)) {
                        $params = MasterParameter::whereIn('id', $paramIds)->get();
                        foreach ($params as $p) {
                            $parameterLabels[] = '<span class="badge bg-primary">' . e($p->Nama) . '</span>';
                        }
                    }
                    return implode(' ', $parameterLabels);
                })
                ->rawColumns(['action', 'Parameter'])
                ->make(true);
        }
        return view('master.form.index');
    }

    public function create()
    {
        $parameterList = MasterParameter::get();
        return view('master.form.create', compact('parameterList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NamaForm' => 'required|string|max:255',
        ]);

        MasterForm::create([
            'Nama' => $request->NamaForm,
            'Parameter' => $request->parameter_id,
            'UserCreate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambah master form baru: ' . $request->Nama);
        }

        return redirect()->route('nama-form.index')->with('success', 'Master form berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $form = MasterForm::findOrFail($id);
        $parameterList = MasterParameter::get();
        return view('master.form.edit', compact('form', 'parameterList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NamaForm' => 'required|string|max:255',
        ]);

        $form = MasterForm::findOrFail($id);
        $form->update([
            'Nama' => $request->NamaForm,
            'Parameter' => $request->parameter_id,
            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Memperbarui master form: ' . $request->Nama);
        }

        return redirect()->route('nama-form.index')->with('success', 'Master form berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $form = MasterForm::find($id);

        if (!$form) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master form: ' . $form->Nama);
        }

        $form->delete();

        return response()->json(['status' => 200, 'message' => 'Master form berhasil dihapus']);
    }
}
