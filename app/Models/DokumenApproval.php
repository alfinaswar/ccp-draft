<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dokumen_approvals';
    protected $guarded = ['id'];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'UserId', 'id');
    }

    public function getJabatan()
    {
        return $this->belongsTo(MasterJabatan::class, 'JabatanId', 'id');
    }

    public function getDepartemen()
    {
        return $this->belongsTo(MasterDepartemen::class, 'DepartemenId', 'id');
    }

    // ✅ Relasi ke HTA/GPA
    public function getDokumenHTAGPA()
    {
        return $this->belongsTo(HtaDanGpa::class, 'DokumenId', 'id');
    }

    public function getDokumenUsulanInvestasi()
    {
        return $this->belongsTo(UsulanInvestasi::class, 'DokumenId', 'id');
    }

    // ✅ Helper: Ambil dokumen yang sesuai berdasarkan JenisFormId
    public function getDokumenRelatedAttribute()
    {
        if (in_array($this->JenisFormId, [1, 2, 16])) {
            return $this->getDokumenHTAGPA;
        }
        if (in_array($this->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            return $this->getDokumenUsulanInvestasi;
        }
        return null;
    }
}
