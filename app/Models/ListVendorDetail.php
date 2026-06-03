<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListVendorDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'list_vendor_details';
    protected $guarded = ['id'];

    public function getPengajuanPembelian()
    {
        return $this->belongsTo(PengajuanPembelian::class, 'IdPengajuan', 'id');
    }

    public function getListVendor()
    {
        return $this->belongsTo(ListVendor::class, 'IdListVendor', 'id');
    }
    public function getNamaBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'NamaBarang', 'id');
    }
}
