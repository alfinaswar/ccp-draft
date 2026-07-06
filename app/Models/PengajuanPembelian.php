<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanPembelian extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengajuan_pembelians';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $attributes = [
        'Versi' => 'v2',
    ];

    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'Kode', 'KodePerusahaan');
    }

    public function getJenisPermintaan()
    {
        return $this->hasOne(MasterJenisPengajuan::class, 'id', 'Jenis');
    }

    public function getVendor()
    {
        return $this->hasMany(ListVendor::class, 'IdPengajuan', 'id');
    }

    public function getVendorDetail()
    {
        return $this->hasMany(ListVendorDetail::class, 'IdPengajuan', 'id');
    }

    public function getPengajuanItem()
    {
        return $this->hasMany(PengajuanItem::class, 'IdPengajuan', 'id');
    }

    public function getHtaGpa()
    {
        return $this->hasOne(HtaDanGpa::class, 'IdPengajuan', 'id');
    }

    public function getFUI()
    {
        return $this->hasOne(UsulanInvestasi::class, 'IdPengajuan', 'id');
    }

    public function getDisposisi()
    {
        return $this->hasOne(LembarDisposisi::class, 'IdPengajuan', 'id');
    }

    public function getFS()
    {
        return $this->hasOne(FeasibilityStudy::class, 'IdPengajuan', 'id');
    }

    public function getRekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'IdPengajuan', 'id');
    }

    public function getDepartemen()
    {
        return $this->hasOne(MasterDepartemen::class, 'id', 'DepartemenId');
    }

    public function getPermintaan()
    {
        return $this->hasOne(PermintaanPembelian::class, 'id', 'IdPermintaan');
    }

    public function getRekomendasiCcp()
    {
        return $this->hasOne(Rekomendasi::class, 'IdPengajuan', 'id');
    }

    public function getTracking()
    {
        return $this->hasMany(AktivitasPengajuan::class, 'KodePengajuan', 'KodePengajuan');
    }
}
