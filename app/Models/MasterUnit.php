<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_units';
    protected $guarded = ['id'];

    public function getParent()
    {
        return $this->belongsTo(MasterUnit::class, 'parent_id', 'id');
    }

    public function getPerusahaan()
    {
        return $this->belongsTo(MasterPerusahaan::class, 'KodePerusahaan', 'Kode');
    }
}
