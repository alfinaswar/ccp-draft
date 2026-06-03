<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rekomendasi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rekomendasis';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get all of the comments for the Rekomendasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getRekomedasiDetail()
    {
        return $this->hasMany(RekomendasiDetail::class, 'IdRekomendasi', 'id');
    }

    public function getUserNego()
    {
        return $this->hasOne(User::class, 'id', 'UserNego');
    }

    public function getDisetujuiOleh()
    {
        return $this->hasOne(User::class, 'id', 'DisetujuiOleh');
    }
    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'Kode', 'KodePerusahaan');
    }
    public function getPengajuan()
    {
        return $this->hasOne(PengajuanPembelian::class, 'id', 'IdPengajuan');
    }
    public function getNamaVendor()
    {
        return $this->hasOne(MasterVendor::class, 'id', 'IdVendor');
    }
}
