<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomplainVendor extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_LIST = [
        'Menunggu TTD Kepala Divisi',
        'Menunggu TTD Group Head',
        'Menunggu Tindak Lanjut Logistik Umum',
        'Menunggu Tindakan CCP',
        'Selesai',
    ];

    protected $table = 'komplain_vendors';
    protected $guarded = ['id'];

    protected $casts = [
        'ParameterEvaluasi' => 'array',
        'DataPendukung' => 'array',
        'Lampiran' => 'array',
        'LangkahAwal' => 'array',
        'TindakanCcp' => 'array',
    ];

    public function getApproval()
    {
        return $this->hasMany(DokumenApproval::class, 'DokumenId', 'id')
            ->orderBy('Urutan', 'asc');
    }
}
