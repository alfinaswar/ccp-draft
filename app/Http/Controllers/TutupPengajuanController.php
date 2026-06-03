<?php

namespace App\Http\Controllers;

use App\Models\AturanPengajuan;
use App\Models\AturanPengajuanPresentasi;
use App\Models\TutupPengajuan;
use Illuminate\Http\Request;

class TutupPengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tutup = TutupPengajuan::first();
        $hariBuka = AturanPengajuan::get();
        $hariPresentasi = AturanPengajuanPresentasi::get();
        return view('pengaturan.pengajuan', compact('tutup', 'hariBuka', 'hariPresentasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTanggal(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'TanggalMulai' => 'required|date',
            'TanggalSelesai' => 'required|date|after_or_equal:TanggalMulai',
            'Keterangan' => 'nullable|string',
        ]);

        // Hapus data lama jika ada
        $old = TutupPengajuan::first();
        if ($old) {
            $old->delete();
        }

        // Simpan data baru
        $tutup = TutupPengajuan::create([
            'Nama' => $request->Nama,
            'TanggalMulai' => $request->TanggalMulai,
            'TanggalSelesai' => $request->TanggalSelesai,
            'Keterangan' => $request->Keterangan,
            'isAktif' => 'N',
            'UserCreate' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Pengaturan Tutup Pengajuan berhasil disimpan.');
    }
    public function storeHari(Request $request)
    {

        $mapKodeHari = [
            'Senin' => 'SEN',
            'Selasa' => 'SEL',
            'Rabu' => 'RAB',
            'Kamis' => 'KAM',
            'Jumat' => 'JUM',
            'Sabtu' => 'SAB',
            'Minggu' => 'MIN',
        ];

        foreach ($request->hari as $dataHari) {
            $namaHari = $dataHari['NamaHari'] ?? null;
            if (!$namaHari || !array_key_exists($namaHari, $mapKodeHari)) {
                continue;
            }
            $kodeHari = $mapKodeHari[$namaHari];
            $jamMulai = $dataHari['JamMulai'] ?? null;
            $jamSelesai = $dataHari['JamSelesai'] ?? null;
            $isAktif = array_key_exists('isAktif', $dataHari) ? $dataHari['isAktif'] : 'N';
            $old = AturanPengajuan::where('KodeHari', $kodeHari)->first();
            if ($old) {
                $old->JamMulai = $jamMulai;
                $old->JamSelesai = $jamSelesai;
                $old->isAktif = $isAktif;
                $old->UserUpdate = auth()->user()->name;
                $old->save();
            } else {
                AturanPengajuan::create([
                    'KodeHari' => $kodeHari,
                    'NamaHari' => $namaHari,
                    'JamMulai' => $jamMulai,
                    'JamSelesai' => $jamSelesai,
                    'isAktif' => $isAktif,
                    'UserCreate' => auth()->user()->name,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pengaturan Hari Pengajuan berhasil disimpan.');
    }
    public function storePresentasi(Request $request)
    {

        $mapKodeHari = [
            'Senin' => 'SEN',
            'Selasa' => 'SEL',
            'Rabu' => 'RAB',
            'Kamis' => 'KAM',
            'Jumat' => 'JUM',
            'Sabtu' => 'SAB',
            'Minggu' => 'MIN',
        ];

        foreach ($request->hari as $dataHari) {
            $namaHari = $dataHari['NamaHari'] ?? null;
            if (!$namaHari || !array_key_exists($namaHari, $mapKodeHari)) {
                continue;
            }
            $kodeHari = $mapKodeHari[$namaHari];
            $jamMulai = $dataHari['JamMulai'] ?? null;
            $jamSelesai = $dataHari['JamSelesai'] ?? null;
            $isAktif = array_key_exists('isAktif', $dataHari) ? $dataHari['isAktif'] : 'N';
            $old = AturanPengajuanPresentasi::where('KodeHari', $kodeHari)->first();
            if ($old) {
                $old->JamMulai = $jamMulai;
                $old->JamSelesai = $jamSelesai;
                $old->isAktif = $isAktif;
                $old->UserUpdate = auth()->user()->name;
                $old->save();
            } else {
                AturanPengajuanPresentasi::create([
                    'KodeHari' => $kodeHari,
                    'NamaHari' => $namaHari,
                    'JamMulai' => $jamMulai,
                    'JamSelesai' => $jamSelesai,
                    'isAktif' => $isAktif,
                    'UserCreate' => auth()->user()->name,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pengaturan Hari Pengajuan berhasil disimpan.');
    }
    /**
     * Display the specified resource.
     */
    public function show(TutupPengajuan $tutupPengajuan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TutupPengajuan $tutupPengajuan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'isAktif' => 'required|in:Y,N'
        ]);

        // Cari dulu datanya
        $tutupPengajuan = TutupPengajuan::find($id);

        if (!$tutupPengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        $tutupPengajuan->isAktif = $validated['isAktif'];
        $tutupPengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status aktif berhasil diperbarui.',
            'isAktif' => $tutupPengajuan->isAktif,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TutupPengajuan $tutupPengajuan)
    {
        //
    }
}
