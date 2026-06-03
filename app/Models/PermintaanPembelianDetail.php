<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPembelianDetail extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permintaan_pembelian_details';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    /**
     * Get the user associated with the PermintaanPembelianDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getBarang()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'NamaBarang');
    }

    public function getSatuan()
    {
        return $this->hasOne(MasterSatuan::class, 'id', 'Satuan');
    }
}
