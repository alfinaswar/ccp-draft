<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBarang extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_barangs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the user associated with the MasterBarang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getSatuan()
    {
        return $this->hasOne(MasterSatuan::class, 'id', 'Satuan');
    }

    public function getMerk()
    {
        return $this->hasOne(MasterMerk::class, 'id', 'Merek');
    }

    public function getJenis()
    {
        return $this->hasOne(MasterJenisPengajuan::class, 'id', 'Jenis');
    }
}
