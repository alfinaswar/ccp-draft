<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenApproval extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dokumen_approvals';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'UserId', 'id');
    }
    public function getJabatan()
    {
        return $this->belongsTo(MasterJabatan::class, 'JabatanId', 'id');
    }
    public function getDepartemen()
    {
        return $this->belongsTo(MasterDepartemen::class, 'DepartemenId', 'id');
    }
}
