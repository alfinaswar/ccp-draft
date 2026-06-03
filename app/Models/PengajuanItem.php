<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengajuan_items';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the user associated with the PengajuanItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getBarang()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'IdBarang');
    }

    public function getHtaGpa()
    {
        return $this->hasOne(HtaDanGpa::class, 'PengajuanItemId', 'id');
    }
    public function getFui()
    {
        return $this->hasOne(UsulanInvestasi::class, 'PengajuanItemId', 'id');
    }
    public function getDisposisi()
    {
        return $this->hasOne(LembarDisposisi::class, 'PengajuanItemId', 'id');
    }
    public function getFs()
    {
        return $this->hasOne(FeasibilityStudy::class, 'PengajuanItemId', 'id');
    }

    public function getRekomendasi()
    {
        return $this->hasOne(Rekomendasi::class, 'PengajuanItemId', 'id');
    }
    public function getPengajuanPembelian()
    {
        return $this->hasOne(PengajuanPembelian::class, 'id', 'IdPengajuan');
    }
}
