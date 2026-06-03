<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPengajuan;
use Illuminate\Http\Request;

class AktivitasPengajuanController extends Controller
{
    public function store(Request $request)
    {
        $aktivitas = AktivitasPengajuan::create();
    }
}
