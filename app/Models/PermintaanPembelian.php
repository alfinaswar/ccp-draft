<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanPembelian extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permintaan_pembelians';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the user associated with the PermintaanPembelian
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getPerusahaan()
    {
        return $this->hasOne(MasterPerusahaan::class, 'Kode', 'KodePerusahaan');
    }

    public function getDepartemen()
    {
        return $this->hasOne(MasterDepartemen::class, 'id', 'Departemen');
    }

    public function getDiajukanOleh()
    {
        return $this->hasOne(User::class, 'id', 'DiajukanOleh');
    }

    public function getDetail()
    {
        return $this->hasMany(PermintaanPembelianDetail::class, 'IdPermintaan', 'id');
    }

    public function getPengajuanPembelian()
    {
        return $this->hasOne(PengajuanPembelian::class, 'IdPermintaan', 'id');
    }

    public function getJenisPermintaan()
    {
        return $this->hasOne(MasterJenisPengajuan::class, 'id', 'Jenis');
    }

    public function getKepalaDivisi()
    {
        return $this->hasOne(User::class, 'id', 'KepalaDivisi_Oleh');
    }

    public function getAccPenunjangMedis()
    {
        return $this->hasOne(User::class, 'id', 'Penunjang_Oleh');
    }

    /**
     * Relasi: Mendapatkan user yang menyetujui Direktur
     */
    public function getAccDirektur()
    {
        return $this->hasOne(User::class, 'id', 'Direktur_Oleh');
    }

    /**
     * Relasi: Mendapatkan user yang menyetujui/konfirmasi SMI/Logistik
     */
    public function getSmi()
    {
        return $this->hasOne(User::class, 'id', 'Logistik_Oleh');
    }
    public function getNotifApproval()
    {
        return $this->hasMany(DokumenApproval::class, 'JenisFormId', 'JenisForm');
    }

}
