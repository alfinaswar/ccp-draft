<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterJenisPengajuan extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_jenis_pengajuans';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the user associated with the MasterJenisPengajuan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getForm()
    {
        return $this->hasOne(MasterForm::class, 'id', 'Form');
    }
}
