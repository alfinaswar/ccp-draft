<?php

namespace Tests\Feature;

use App\Models\DokumenApproval;
use App\Models\KomplainVendor;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterParameterKomplain;
use App\Models\MasterPerusahaan;
use App\Models\MasterUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Self-check alur form komplain vendor sesuai formulir cetak.
 * Form publik tanpa login, tindak lanjut internal memakai akun.
 */
class KomplainFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function buatUser(string $role, string $label): User
    {
        $user = User::create([
            'name' => $label,
            'email' => Str::lower(Str::random(6)) . '@example.test',
            'password' => bcrypt('password'),
            'kodeperusahaan' => 'KV-TEST',
        ]);

        $user->assignRole($role);

        return $user;
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'KodePerusahaan' => 'KV-TEST',
            'Tanggal' => now()->toDateString(),
            'NamaPelapor' => 'Pelapor Test',
            'JabatanId' => $this->jabatan->id,
            'UnitId' => $this->unit->id,
            'NamaBarang' => 'Sarung Tangan Steril',
            'JenisKategori' => 'BMHP',
            'Merk' => 'Merek Test',
            'Distributor' => 'Distributor Test',
            'ParameterEvaluasi' => ['Cacat Produksi', 'Lainnya'],
            'ParameterLainnya' => 'Kemasan penyok',
            'UraianSingkat' => 'Barang diterima dalam kondisi cacat.',
        ], $override);
    }

    private MasterPerusahaan $perusahaan;
    private MasterJabatan $jabatan;
    private MasterUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->perusahaan = MasterPerusahaan::firstOrCreate(
            ['Kode' => 'KV-TEST'],
            ['Nama' => 'RS Test Komplain']
        );
        $this->jabatan = MasterJabatan::create(['Nama' => 'Jabatan Test']);
        $this->unit = MasterUnit::create([
            'nama_unit' => 'Unit Test',
            'kode_unit' => 'UT',
            'KodePerusahaan' => 'KV-TEST',
            'Aktif' => 'Y',
        ]);

        // parameter mengikuti formulir cetak
        $awal = [
            'ParameterEvaluasi' => ['Cacat Produksi', 'Lainnya'],
            'DataPendukung' => ['Foto', 'Lainnya'],
            'LangkahAwal' => ['Dilaporkan ke Distributor/Penyedia', 'Lainnya'],
            'TindakanCcp' => ['Diganti BMHP', 'Lainnya'],
        ];

        foreach ($awal as $jenis => $daftar) {
            foreach ($daftar as $i => $nama) {
                MasterParameterKomplain::firstOrCreate(
                    ['Jenis' => $jenis, 'Nama' => $nama],
                    ['Urutan' => $i + 1, 'Aktif' => 'Y']
                );
            }
        }
    }

    public function test_form_publik_dapat_dibuka_tanpa_login()
    {
        $this->get(route('komplain.form'))->assertOk()->assertSee('Formulir Evaluasi BMHP Umum');
    }

    public function test_alur_komplain_dari_publik_sampai_selesai()
    {
        MasterForm::firstOrCreate(['Nama' => 'Komplain Vendor'], ['UserCreate' => 'Test']);

        $kadiv = $this->buatUser('Kadiv', 'Kadiv Test');
        $gh = $this->buatUser('Group Head', 'GH Test');
        $logum = $this->buatUser('Logum', 'Logum Test');
        $ccp = $this->buatUser('CCP', 'CCP Test');

        // 1. Publik mengirim formulir tanpa login
        $this->post(route('komplain.simpan'), $this->payload())
            ->assertRedirect();

        $komplain = KomplainVendor::latest('id')->firstOrFail();
        $this->assertSame('Menunggu TTD Kepala Divisi', $komplain->Status);
        $this->assertSame(['Cacat Produksi', 'Lainnya'], $komplain->ParameterEvaluasi);

        // Nama RS, unit, dan jabatan diambil dari master
        $this->assertSame('RS Test Komplain', $komplain->NamaRS);
        $this->assertSame('KV-TEST', $komplain->KodePerusahaan);
        $this->assertSame('Unit Test', $komplain->UnitKerja);
        $this->assertSame('Jabatan Test', $komplain->Jabatan);
        $this->get(route('komplain.terima-kasih', $komplain->KodeKomplain))
            ->assertOk()
            ->assertSee($komplain->KodeKomplain);

        // Tentukan penandatangan, meniru konfigurasi Master Approval
        $formId = MasterForm::where('Nama', 'Komplain Vendor')->value('id');
        $approval = DokumenApproval::where('JenisFormId', $formId)
            ->where('DokumenId', $komplain->id)
            ->orderBy('Urutan')
            ->get();
        $this->assertCount(2, $approval);
        $approval[0]->update(['UserId' => $kadiv->id]);
        $approval[1]->update(['UserId' => $gh->id]);

        // 2. Kepala Divisi menandatangani
        $this->actingAs($kadiv)
            ->post(route('komplain.approve', encrypt($komplain->id)))
            ->assertSessionMissing('error');
        $this->assertSame('Menunggu TTD Group Head', $komplain->fresh()->Status);

        // 3. Group Head menandatangani
        $this->actingAs($gh)
            ->post(route('komplain.approve', encrypt($komplain->id)))
            ->assertSessionMissing('error');
        $this->assertSame('Menunggu Tindak Lanjut Logistik Umum', $komplain->fresh()->Status);

        // 4. Logistik Umum mengisi langkah awal
        $this->actingAs($logum)->post(route('komplain.logistik-umum', encrypt($komplain->id)), [
            'LangkahAwal' => ['Dilaporkan ke Distributor/Penyedia'],
        ])->assertSessionMissing('error');
        $this->assertSame('Menunggu Tindakan CCP', $komplain->fresh()->Status);

        // 5. CCP mengisi tindakan
        $this->actingAs($ccp)->post(route('komplain.tindakan-ccp', encrypt($komplain->id)), [
            'TindakanCcp' => ['Diganti BMHP', 'Investigasi Kualitas'],
        ])->assertSessionMissing('error');

        $komplain->refresh();
        $this->assertSame('Selesai', $komplain->Status);
        $this->assertSame(['Diganti BMHP', 'Investigasi Kualitas'], $komplain->TindakanCcp);

        $this->actingAs($ccp)
            ->get(route('komplain.show', encrypt($komplain->id)))
            ->assertOk()
            ->assertSee('Diganti BMHP')
            ->assertSee('table-bordered')
            ->assertDontSee('kv-sheet');

        // parameter yang dihapus dari master tetap tampil di laporan lama
        MasterParameterKomplain::where('Jenis', 'TindakanCcp')
            ->whereIn('Nama', ['Diganti BMHP', 'Investigasi Kualitas'])
            ->delete();

        $this->actingAs($ccp)
            ->get(route('komplain.show', encrypt($komplain->id)))
            ->assertOk()
            ->assertSee('Diganti BMHP')
            ->assertSee('Investigasi Kualitas');
    }

    public function test_parameter_lainnya_wajib_diisi_keterangan()
    {
        $this->post(route('komplain.simpan'), $this->payload(['ParameterLainnya' => null]))
            ->assertSessionHasErrors('ParameterLainnya');
    }

    public function test_unit_dan_jabatan_harus_dari_master()
    {
        $this->post(route('komplain.simpan'), $this->payload([
            'UnitId' => 999999,
            'JabatanId' => 999999,
            'KodePerusahaan' => 'TIDAK-ADA',
        ]))->assertSessionHasErrors(['UnitId', 'JabatanId', 'KodePerusahaan']);
    }

    public function test_unit_milik_rs_lain_ditolak()
    {
        MasterPerusahaan::firstOrCreate(['Kode' => 'RS-LAIN'], ['Nama' => 'RS Lain']);

        $unitLain = MasterUnit::create([
            'nama_unit' => 'Unit RS Lain',
            'KodePerusahaan' => 'RS-LAIN',
            'Aktif' => 'Y',
        ]);

        // unit milik RS lain tidak boleh dipakai
        $this->post(route('komplain.simpan'), $this->payload(['UnitId' => $unitLain->id]))
            ->assertSessionHasErrors('UnitId');

        // unit tanpa RS juga tidak boleh dipakai
        $unitBersama = MasterUnit::create([
            'nama_unit' => 'Unit Tanpa RS',
            'KodePerusahaan' => null,
            'Aktif' => 'Y',
        ]);

        $this->post(route('komplain.simpan'), $this->payload(['UnitId' => $unitBersama->id]))
            ->assertSessionHasErrors('UnitId');
    }

    public function test_form_unit_ikut_rs_yang_dipilih()
    {
        $this->get(route('komplain.form'))->assertOk();

        // data-perusahaan dipakai JavaScript untuk menyaring dropdown Unit Kerja
        $this->get(route('komplain.form'))
            ->assertSee('data-perusahaan="KV-TEST"', false);
    }

    public function test_form_pakai_editor_dan_upload_lampiran()
    {
        $this->get(route('komplain.form'))
            ->assertOk()
            ->assertSee('ckeditor/ckeditor.js')
            ->assertSee('name="Lampiran[]"', false)
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('previewLampiran', false);
    }

    public function test_uraian_dibersihkan_dan_lampiran_tersimpan()
    {
        Storage::fake('public');

        $this->post(route('komplain.simpan'), $this->payload([
            'UraianSingkat' => '<p onclick="alert(1)">Barang <strong>cacat</strong></p><script>alert(2)</script>',
            'Lampiran' => [
                UploadedFile::fake()->create('bukti.jpg', 50, 'image/jpeg'),
                UploadedFile::fake()->create('berita-acara.pdf', 100, 'application/pdf'),
            ],
        ]))->assertSessionHasNoErrors();

        $komplain = KomplainVendor::latest('id')->first();

        // HTML editor dibersihkan: atribut dan script dibuang, tag format tetap
        $this->assertStringNotContainsString('onclick', $komplain->UraianSingkat);
        $this->assertStringNotContainsString('<script', $komplain->UraianSingkat);
        $this->assertStringContainsString('<strong>cacat</strong>', $komplain->UraianSingkat);

        $this->assertCount(2, $komplain->Lampiran);
        Storage::disk('public')->assertExists($komplain->Lampiran[0]['path']);
        $this->assertSame('berita-acara.pdf', $komplain->Lampiran[1]['nama']);

        // detail internal menampilkan berkas pendukung
        $admin = $this->buatUser('Admin', 'Admin Test');
        $this->actingAs($admin)->get(route('komplain.show', encrypt($komplain->id)))
            ->assertOk()
            ->assertSee('bukti.jpg')
            ->assertSee('<strong>cacat</strong>', false);
    }

    public function test_lampiran_selain_foto_dan_pdf_ditolak()
    {
        Storage::fake('public');

        $this->post(route('komplain.simpan'), $this->payload([
            'Lampiran' => [UploadedFile::fake()->create('virus.exe', 10, 'application/octet-stream')],
        ]))->assertSessionHasErrors('Lampiran.0');
    }

    public function test_master_unit_dapat_dikelola()
    {
        $admin = $this->buatUser('Admin', 'Admin Test');

        $this->actingAs($admin)->get(route('unit.index'))->assertOk();
        $this->actingAs($admin)->get(route('unit.create'))->assertOk();

        $this->actingAs($admin)->post(route('unit.store'), [
            'nama_unit' => 'Unit Baru Uji',
            'kode_unit' => 'UBU',
            'KodePerusahaan' => 'KV-TEST',
            'Aktif' => 'Y',
        ])->assertSessionHasNoErrors();

        $baru = MasterUnit::where('nama_unit', 'Unit Baru Uji')->firstOrFail();
        $this->assertSame('KV-TEST', $baru->KodePerusahaan);

        $this->actingAs($admin)->get(route('unit.edit', encrypt($baru->id)))->assertOk();

        // unit aktif langsung muncul di dropdown form publik
        $this->get(route('komplain.form'))->assertSee('Unit Baru Uji');

        $this->actingAs($admin)->put(route('unit.update', encrypt($baru->id)), [
            'nama_unit' => 'Unit Baru Uji',
            'kode_unit' => 'UBU',
            'KodePerusahaan' => 'KV-TEST',
            'Aktif' => 'N',
        ])->assertSessionHasNoErrors();

        // nonaktif -> hilang dari form publik
        $this->get(route('komplain.form'))->assertDontSee('Unit Baru Uji');
    }

    public function test_index_unit_menampilkan_daftar_perusahaan()
    {
        $admin = $this->buatUser('Admin', 'Admin Test');

        $this->actingAs($admin)->get(route('unit.index'))
            ->assertOk()
            ->assertSee('RS Test Komplain')
            ->assertSee('Unit Tanpa RS (belum dipetakan)');

        // daftar unit satu perusahaan
        $this->actingAs($admin)->get(route('unit.perusahaan', 'KV-TEST'))
            ->assertOk()
            ->assertSee('RS Test Komplain');

        // kode perusahaan tak dikenal -> 404
        $this->actingAs($admin)->get(route('unit.perusahaan', 'TIDAK-ADA'))->assertNotFound();

        // form tambah unit langsung terisi perusahaannya
        $this->actingAs($admin)->get(route('unit.create', ['perusahaan' => 'KV-TEST']))
            ->assertOk()
            ->assertSee('RS Test Komplain');
    }

    public function test_user_tanpa_role_tidak_boleh_menandatangani()
    {
        $komplain = KomplainVendor::create([
            'KodeKomplain' => 'KV-TEST-0001',
            'Tanggal' => now()->toDateString(),
            'NamaPelapor' => 'Pelapor Test',
            'status' => 'Menunggu TTD Kepala Divisi',
        ]);

        $bukanApprover = $this->buatUser('Staff', 'Staff Test');

        $this->actingAs($bukanApprover)
            ->post(route('komplain.approve', encrypt($komplain->id)))
            ->assertSessionHas('error');

        $this->assertSame('Menunggu TTD Kepala Divisi', $komplain->fresh()->Status);
    }

    public function test_halaman_internal_dapat_dibuka()
    {
        $admin = $this->buatUser('Admin', 'Admin Test');

        $this->actingAs($admin)->get(route('komplain.index'))->assertOk();
        $this->actingAs($admin)->get(route('parameter-komplain.index'))->assertOk();
    }

    public function test_master_parameter_komplain_dapat_dikelola()
    {
        $admin = $this->buatUser('Admin', 'Admin Test');

        $this->actingAs($admin)->get(route('parameter-komplain.index'))->assertOk();
        $this->actingAs($admin)->get(route('parameter-komplain.create'))->assertOk();

        // tambah parameter baru
        $this->actingAs($admin)->post(route('parameter-komplain.store'), [
            'Jenis' => 'ParameterEvaluasi',
            'Nama' => 'Parameter Uji Baru',
            'Urutan' => 99,
            'Aktif' => 'Y',
        ])->assertSessionHasNoErrors();

        $baru = MasterParameterKomplain::where('Nama', 'Parameter Uji Baru')->firstOrFail();

        $this->actingAs($admin)->get(route('parameter-komplain.edit', encrypt($baru->id)))->assertOk();

        // parameter aktif langsung muncul di form publik
        $this->get(route('komplain.form'))->assertSee('Parameter Uji Baru');

        // nonaktifkan -> hilang dari form publik
        $this->actingAs($admin)->put(route('parameter-komplain.update', encrypt($baru->id)), [
            'Jenis' => 'ParameterEvaluasi',
            'Nama' => 'Parameter Uji Baru',
            'Urutan' => 99,
            'Aktif' => 'N',
        ])->assertSessionHasNoErrors();

        $this->get(route('komplain.form'))->assertDontSee('Parameter Uji Baru');
    }

    public function test_parameter_di_luar_master_ditolak()
    {
        $this->post(route('komplain.simpan'), $this->payload([
            'ParameterEvaluasi' => ['Parameter Karangan'],
        ]))->assertSessionHasErrors('ParameterEvaluasi.0');
    }
}
