<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPerusahaan extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_perusahaans';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    public function getFormPerusahaan()
    {
        return $this->hasMany(MasterForm::class, 'KodePerusahaan', 'Kode');
    }
    public function getRkap()
    {
        return $this->hasMany(Rkap::class, 'PerusahaanId', 'Kode');
    }
}
