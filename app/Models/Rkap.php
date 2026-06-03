<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rkap extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'rkaps';
    protected $guarded = ['id'];
    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'id', 'PerusahaanId');
    }
}
