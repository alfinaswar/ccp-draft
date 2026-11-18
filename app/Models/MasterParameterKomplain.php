<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterParameterKomplain extends Model
{
    use HasFactory, SoftDeletes;

    public const JENIS = [
        'ParameterEvaluasi' => 'Parameter Evaluasi (C)',
        'DataPendukung' => 'Data Pendukung (D)',
        'LangkahAwal' => 'Langkah Awal (E)',
        'TindakanCcp' => 'Tindakan CCP (F)',
    ];

    // Nama yang memunculkan kolom isian bebas di samping checkbox
    public const LAINNYA = 'Lainnya';

    protected $table = 'master_parameter_komplains';
    protected $guarded = ['id'];

    /**
     * Daftar nama parameter aktif untuk satu jenis, urut sesuai kolom Urutan.
     */
    public static function daftar(string $jenis): array
    {
        return static::where('Jenis', $jenis)
            ->where('Aktif', 'Y')
            ->orderBy('Urutan')
            ->orderBy('Nama')
            ->pluck('Nama')
            ->all();
    }
}
