<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterDepartemen;
use App\Models\MasterJabatan;
use App\Models\MasterPerusahaan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        if (auth()->user()->hasRole('Admin')) {
            $data = User::with('getPerusahaan')->orderBy('id', 'DESC')->get();
        } else {
            $userPerusahaan = auth()->user()->kodeperusahaan;
            $data = User::with('getPerusahaan')
                ->where('kodeperusahaan', $userPerusahaan)
                ->orderBy('id', 'DESC')
                ->get();
        }
        $perusahaan = MasterPerusahaan::get();
        return view('users.index', compact('data', 'perusahaan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $jabatan = MasterJabatan::get();
        // dd($jabatan);
        $departemen = MasterDepartemen::get();
        $perusahaan = MasterPerusahaan::get();
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles', 'perusahaan', 'jabatan', 'departemen'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required',
            'kodeperusahaan' => 'required',
            // 'departemen' => 'required',
            'foto' => 'nullable',
            'tandatangan' => 'nullable',
        ]);

        $input = $request->all();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('upload/foto', $filename, 'public');
            $input['foto'] = $filename;
        }
        if ($request->hasFile('tandatangan')) {
            $file = $request->file('tandatangan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('upload/tandatangan', $filename, 'public');
            $input['tandatangan'] = $filename;
        }
        $input['password'] = Hash::make($input['password']);
        $input['UserCreate'] = auth()->user()->name;
        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Membuat user baru: ' . $user->name . ' (' . $user->email . ')');

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $id = decrypt($id);
        $user = User::find($id);
        $jabatan = MasterJabatan::get();
        $departemen = MasterDepartemen::get();
        $perusahaan = MasterPerusahaan::get();
        return view('users.show', compact('user', 'jabatan', 'departemen', 'perusahaan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $jabatan = MasterJabatan::get();
        $departemen = MasterDepartemen::get();
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $perusahaan = MasterPerusahaan::get();
        return view('users.edit', compact('jabatan', 'user', 'roles', 'userRole', 'perusahaan', 'departemen'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'jabatan' => 'required',
            'email' => 'required',
            'roles' => 'required',
            'kodeperusahaan' => 'required',
            // 'departemen' => 'required',
            'foto' => 'nullable',
            'tandatangan' => 'nullable',
        ]);

        $input = $request->all();
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('upload/foto', $filename, 'public');
            $input['foto'] = $filename;
        }
        if ($request->hasFile('tandatangan')) {
            $file = $request->file('tandatangan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('upload/tandatangan', $filename, 'public');
            $input['tandatangan'] = $filename;
        }
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }
        $input['UserUpdate'] = auth()->user()->name;
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully');
    }
    public function Profile(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'departemen' => 'nullable',
            'jabatan' => 'required',
            'email' => 'required|email',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tandatangan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6',
        ]);

        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $input = $request->all();

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists('upload/foto/' . $user->foto)) {
                Storage::disk('public')->delete('upload/foto/' . $user->foto);
            }
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('upload/foto', $filename, 'public');
            $input['foto'] = $filename;
        } else {
            // Supaya tidak mengosongkan foto jika tidak ada upload baru
            unset($input['foto']);
        }

        // Upload tandatangan jika ada
        if ($request->hasFile('tandatangan')) {
            // Hapus tandatangan lama jika ada
            if ($user->tandatangan && Storage::disk('public')->exists('upload/tandatangan/' . $user->tandatangan)) {
                Storage::disk('public')->delete('upload/tandatangan/' . $user->tandatangan);
            }
            $file = $request->file('tandatangan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('upload/tandatangan', $filename, 'public');
            $input['tandatangan'] = $filename;
        } else {
            // Supaya tidak mengosongkan jika tidak ada upload baru
            unset($input['tandatangan']);
        }

        // Update password jika ada input password
        if (!empty($input['password'])) {
            $input['password'] = \Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['UserUpdate'] = auth()->user()->name;

        $user->update($input);

        return redirect()
            ->back()
            ->with('success', 'Data Berhasil Di Update');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();
        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
