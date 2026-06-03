<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsulanInvestasiDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulan_investasi_details';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getNamaBarang()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'NamaBarang');
    }

    public function getVendor()
    {
        return $this->hasOne(MasterVendor::class, 'id', 'Vendor');
    }

    public function getVendorDipilih()
    {
        return $this->hasOne(MasterVendor::class, 'id', 'Vendor');
    }
}
