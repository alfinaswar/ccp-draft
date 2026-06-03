<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'list_vendors';
    protected $guarded = ['id'];

    public function getPengajuanPembelian()
    {
        return $this->belongsTo(PengajuanPembelian::class, 'IdPengajuan', 'id');
    }

    public function getVendor()
    {
        return $this->belongsTo(MasterVendor::class, 'NamaVendor', 'Nama');
    }

    public function getNamaVendor()
    {
        return $this->hasOne(MasterVendor::class, 'id', 'NamaVendor');
    }

    public function getVendorDetail()
    {
        return $this->hasMany(ListVendorDetail::class, 'IdListVendor', 'id');
    }

    public function getHtaGpa()
    {
        return $this->hasOne(HtaDanGpaDetail::class, 'IdVendor', 'NamaVendor');
    }

    public function getRekomendasi()
    {
        return $this->hasOne(RekomendasiDetail::class, 'IdVendor', 'NamaVendor');
    }
}
