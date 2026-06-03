<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekomendasiDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rekomendasi_details';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the user associated with the RekomendasiDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getNamaVendor()
    {
        return $this->hasOne(MasterVendor::class, 'id', 'IdVendor');
    }

    public function getBarang()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'NamaPermintaan');
    }

    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'Kode', 'KodePerusahaan');
    }

    public function getNegara()
    {
        return $this->hasOne(Negara::class, 'Kode', 'NegaraProduksi');
    }
}
