<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LembarDisposisi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lembar_disposisis';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get all of the comments for the LembarDisposisi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getDetail()
    {
        return $this->hasMany(LembarDisposisiApproval::class, 'IdLembarDisposisi', 'id');
    }

    public function getBarang()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'NamaBarang');
    }

    public function getVendor()
    {
        return $this->hasOne(MasterVendor::class, 'id', 'RencanaVendor');
    }
    public function getPengajuan()
    {
        return $this->hasOne(PengajuanPembelian::class, 'id', 'IdPengajuan');
    }
}
