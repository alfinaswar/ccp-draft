<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiketTrouble extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TiketTrouble';

   /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
   protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($tiket) {
            if (empty($tiket->KodeTiket)) {
                $tiket->KodeTiket = 'TBL-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -3));
            }
        });
    }
    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'Kode', 'KodePerusahaan');
    }
}
