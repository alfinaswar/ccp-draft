<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeasibilityStudy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feasibility_studies';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getFsDetail()
    {
        return $this->hasMany(FeasibilityStudyDetail::class, 'IdFs', 'id');
    }

    public function getBarang()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'NamaBarang');
    }

    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'Kode', 'KodePerusahaan');
    }

    public function getPengajuan()
    {
        return $this->hasOne(PengajuanPembelian::class, 'id', 'IdPengajuan');
    }
}
