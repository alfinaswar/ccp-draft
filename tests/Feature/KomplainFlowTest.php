<?php

namespace Tests\Feature;

use App\Mail\NotifKomplainVendor;
use App\Models\DokumenApproval;
use App\Models\KomplainVendor;
use App\Models\MasterApproval;
use App\Models\MasterForm;
use App\Models\MasterJabatan;
use App\Models\MasterParameterKomplain;
use App\Models\MasterPerusahaan;
use App\Models\MasterUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
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
            'KodePerusahaan' => $this->perusahaan->id,
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
        $this->get(route('komplain.form'))->assertOk()->assertSee('Form Komplain Vendor');
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
        // Urutan 1 adalah Pelapor (auto approve), Urutan 2 Kadiv, Urutan 3 Group Head
        $formId = MasterForm::where('Nama', 'Komplain Vendor')->value('id');
        $approval = DokumenApproval::where('JenisFormId', $formId)
            ->where('DokumenId', $komplain->id)
            ->orderBy('Urutan')
            ->get();
        $this->assertCount(3, $approval);
        $this->assertSame(1, (int) $approval[0]->Urutan);
        $this->assertSame('Approved', $approval[0]->Status);
        $this->assertSame('Pelapor Test', $approval[0]->Nama);

        $approval[1]->update(['UserId' => $kadiv->id]);
        $approval[2]->update(['UserId' => $gh->id]);

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
            ->assertSee($komplain->KodeKomplain)
            ->assertSee('E. Tanda Tangan')
            ->assertSee('table-bordered')
            ->assertDontSee('F. Tindak Lanjut CCP')
            ->assertDontSee('kv-sheet');

        // parameter yang dihapus dari master tetap tersimpan di laporan lama
        MasterParameterKomplain::where('Jenis', 'TindakanCcp')
            ->whereIn('Nama', ['Diganti BMHP', 'Investigasi Kualitas'])
            ->delete();

        $this->assertSame(['Diganti BMHP', 'Investigasi Kualitas'], $komplain->fresh()->TindakanCcp);
    }

    public function test_notifikasi_email_berjenjang_master_approval_dan_approval_saya()
    {
        Mail::fake();
        Storage::fake('public');

        $formId = MasterForm::where('Nama', 'Komplain Vendor')->value('id');
        $approver2 = $this->buatUser('Kadiv', 'Pejabat Kedua');
        $approver3 = $this->buatUser('Group Head', 'Pejabat Ketiga');

        MasterApproval::create([
            'KodePerusahaan' => $this->perusahaan->Kode,
            'JenisForm' => $formId,
            'Urutan' => 1,
            'UserId' => $approver2->id,
            'NamaJabatan' => 'Kepala Divisi',
            'Aktif' => 'Y',
        ]);
        MasterApproval::create([
            'KodePerusahaan' => $this->perusahaan->Kode,
            'JenisForm' => $formId,
            'Urutan' => 2,
            'UserId' => $approver3->id,
            'NamaJabatan' => 'Group Head',
            'Aktif' => 'Y',
        ]);

        // 1. Submit form komplain dengan lampiran
        $this->post(route('komplain.simpan'), $this->payload([
            'Lampiran' => [
                UploadedFile::fake()->create('bukti-komplain.jpg', 50, 'image/jpeg'),
            ],
        ]))->assertRedirect();

        $komplain = KomplainVendor::latest('id')->firstOrFail();

        $approvals = DokumenApproval::where('JenisFormId', $formId)
            ->where('DokumenId', $komplain->id)
            ->orderBy('Urutan')
            ->get();

        // Urutan 1 Pelapor (Approved), Urutan 2 & 3 Pending
        $this->assertCount(3, $approvals);
        $this->assertSame(1, (int) $approvals[0]->Urutan);
        $this->assertSame('Approved', $approvals[0]->Status);
        $this->assertSame(2, (int) $approvals[1]->Urutan);
        $this->assertSame('Pending', $approvals[1]->Status);
        $this->assertSame($approver2->id, (int) $approvals[1]->UserId);
        $this->assertSame(3, (int) $approvals[2]->Urutan);
        $this->assertSame('Pending', $approvals[2]->Status);
        $this->assertSame($approver3->id, (int) $approvals[2]->UserId);

        // Email hanya terkirim ke Approval ke-2, dan belum ke Approval ke-3
        Mail::assertSent(NotifKomplainVendor::class, function (NotifKomplainVendor $mail) use ($approver2, $komplain) {
            $built = $mail->build();
            $adaPdf = collect($built->rawAttachments)->contains(fn ($att) => ($att['name'] ?? '') === 'Laporan-Komplain-' . $komplain->KodeKomplain . '.pdf');
            $adaLampiran = collect($built->attachments)->contains(fn ($att) => ($att['options']['as'] ?? '') === 'bukti-komplain.jpg');

            return $mail->hasTo($approver2->email)
                && (int) $mail->targetApproval->Urutan === 2
                && $adaPdf
                && $adaLampiran;
        });
        Mail::assertNotSent(NotifKomplainVendor::class, function (NotifKomplainVendor $mail) use ($approver3) {
            return $mail->hasTo($approver3->email);
        });

        // Menu Approval Saya: muncul di Approval ke-2, belum muncul di Approval ke-3
        $this->actingAs($approver2)
            ->getJson(route('approval-saya.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee($komplain->KodeKomplain);

        $this->actingAs($approver3)
            ->getJson(route('approval-saya.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertDontSee($komplain->KodeKomplain);

        // 2. Approval ke-2 menyetujui -> email dikirim ke Approval ke-3
        $this->actingAs($approver2)
            ->post(route('komplain.approve', encrypt($komplain->id)))
            ->assertSessionMissing('error');

        Mail::assertSent(NotifKomplainVendor::class, function (NotifKomplainVendor $mail) use ($approver3) {
            return $mail->hasTo($approver3->email)
                && (int) $mail->targetApproval->Urutan === 3;
        });

        // Menu Approval Saya: kini muncul di Approval ke-3 dan hilang dari Approval ke-2
        $this->actingAs($approver2)
            ->getJson(route('approval-saya.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertDontSee($komplain->KodeKomplain);

        $this->actingAs($approver3)
            ->getJson(route('approval-saya.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee($komplain->KodeKomplain);
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

    public function test_lampiran_maksimal_10_file_diterima()
    {
        Storage::fake('public');

        $files = [];
        for ($i = 1; $i <= 10; $i++) {
            $files[] = UploadedFile::fake()->create("foto-{$i}.jpg", 50, 'image/jpeg');
        }

        $this->post(route('komplain.simpan'), $this->payload([
            'Lampiran' => $files,
        ]))->assertSessionHasNoErrors();

        $komplain = KomplainVendor::latest('id')->first();
        $this->assertCount(10, $komplain->Lampiran);
    }

    public function test_lampiran_melebihi_10_file_ditolak()
    {
        Storage::fake('public');

        $files = [];
        for ($i = 1; $i <= 11; $i++) {
            $files[] = UploadedFile::fake()->create("foto-{$i}.jpg", 50, 'image/jpeg');
        }

        $this->post(route('komplain.simpan'), $this->payload([
            'Lampiran' => $files,
        ]))->assertSessionHasErrors('Lampiran');
    }

    public function test_lampiran_melebihi_5_mb_ditolak()
    {
        Storage::fake('public');

        $this->post(route('komplain.simpan'), $this->payload([
            'Lampiran' => [UploadedFile::fake()->create('besar.jpg', 6000, 'image/jpeg')],
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

    public function test_alur_lengkap_enam_level_master_approval()
    {
        $formId = MasterForm::firstOrCreate(['Nama' => 'Komplain Vendor'], ['UserCreate' => 'Test'])->id;

        $kadiv = $this->buatUser('Kadiv', 'Kadiv Test 6');
        $gh = $this->buatUser('Group Head', 'GH Test 6');
        $logum = $this->buatUser('Logum', 'Logum Test 6');
        $ccp = $this->buatUser('CCP', 'CCP Test 6');
        $ghCcp = $this->buatUser('GH CCP', 'GH CCP Test 6');

        // Atur konfigurasi lengkap 6 urutan di Master Approval
        $levels = [
            ['urutan' => 1, 'user' => null, 'jabatan' => 'Pelapor'],
            ['urutan' => 2, 'user' => $kadiv, 'jabatan' => 'Kepala Divisi'],
            ['urutan' => 3, 'user' => $gh, 'jabatan' => 'Group Head'],
            ['urutan' => 4, 'user' => $logum, 'jabatan' => 'Logistik Umum'],
            ['urutan' => 5, 'user' => $ccp, 'jabatan' => 'Tim CCP'],
            ['urutan' => 6, 'user' => $ghCcp, 'jabatan' => 'GH CCP'],
        ];

        foreach ($levels as $lvl) {
            MasterApproval::create([
                'KodePerusahaan' => $this->perusahaan->Kode,
                'JenisForm' => $formId,
                'Urutan' => $lvl['urutan'],
                'UserId' => $lvl['user']?->id,
                'NamaJabatan' => $lvl['jabatan'],
                'Aktif' => 'Y',
            ]);
        }

        // 1. Submit pengajuan formulir
        $this->post(route('komplain.simpan'), $this->payload())
            ->assertRedirect();

        $komplain = KomplainVendor::latest('id')->firstOrFail();
        $this->assertSame('Menunggu TTD Kepala Divisi', $komplain->Status);

        $approvals = DokumenApproval::where('JenisFormId', $formId)
            ->where('DokumenId', $komplain->id)
            ->orderBy('Urutan')
            ->get();

        $this->assertCount(6, $approvals);
        $this->assertSame(1, (int) $approvals[0]->Urutan);
        $this->assertSame('Approved', $approvals[0]->Status);
        $this->assertSame(2, (int) $approvals[1]->Urutan);
        $this->assertSame('Pending', $approvals[1]->Status);
        $this->assertSame(6, (int) $approvals[5]->Urutan);

        // 2. Kadiv approve -> Status jadi Menunggu TTD Group Head
        $this->actingAs($kadiv)
            ->post(route('komplain.approve', encrypt($komplain->id)), ['Catatan' => 'OK dari Kadiv'])
            ->assertSessionMissing('error');
        $this->assertSame('Menunggu TTD Group Head', $komplain->fresh()->Status);

        // 3. GH approve -> Status jadi Menunggu Tindak Lanjut Logistik Umum
        $this->actingAs($gh)
            ->post(route('komplain.approve', encrypt($komplain->id)), ['Catatan' => 'OK dari GH'])
            ->assertSessionMissing('error');
        $this->assertSame('Menunggu Tindak Lanjut Logistik Umum', $komplain->fresh()->Status);

        // 4. Logistik Umum approve sekaligus mengisi Langkah Awal -> Status jadi Menunggu Tindakan CCP
        $this->actingAs($logum)
            ->post(route('komplain.approve', encrypt($komplain->id)), [
                'LangkahAwal' => ['Dilaporkan ke Distributor/Penyedia'],
                'Catatan' => 'Sudah dikonfirmasi ke vendor',
            ])
            ->assertSessionMissing('error');

        $komplain->refresh();
        $this->assertSame('Menunggu Tindakan CCP', $komplain->Status);
        $this->assertSame(['Dilaporkan ke Distributor/Penyedia'], $komplain->LangkahAwal);
        $this->assertSame('Logum Test 6', $komplain->LogistikUmumOleh);

        // 5. Tim CCP approve sekaligus mengisi Tindakan CCP, Keterangan investigasi, dan upload berkas pendukung
        Storage::fake('public');
        $fileBuktiFoto = UploadedFile::fake()->image('temuan_cacat.jpg');
        $fileLaporanPdf = UploadedFile::fake()->create('laporan_investigasi.pdf', 300, 'application/pdf');

        $this->actingAs($ccp)
            ->post(route('komplain.approve', encrypt($komplain->id)), [
                'TindakanCcp' => ['Diganti BMHP'],
                'KeteranganCcp' => '<p>Hasil investigasi batch 2026-X cacat segel dari pabrik.</p>',
                'LampiranCcp' => [$fileBuktiFoto, $fileLaporanPdf],
                'Catatan' => 'Investigasi selesai, barang diganti',
            ])
            ->assertSessionMissing('error');

        $komplain->refresh();
        $this->assertSame('Menunggu Tindakan CCP', $komplain->Status);
        $this->assertSame(['Diganti BMHP'], $komplain->TindakanCcp);
        $this->assertSame('CCP Test 6', $komplain->CcpOleh);
        $this->assertStringContainsString('Hasil investigasi batch 2026-X cacat segel', $komplain->KeteranganCcp);
        $this->assertCount(2, $komplain->LampiranCcp);
        $this->assertSame('temuan_cacat.jpg', $komplain->LampiranCcp[0]['nama']);
        $this->assertSame('laporan_investigasi.pdf', $komplain->LampiranCcp[1]['nama']);
        Storage::disk('public')->assertExists($komplain->LampiranCcp[0]['path']);
        Storage::disk('public')->assertExists($komplain->LampiranCcp[1]['path']);

        // Verifikasi bahwa GH CCP dan Admin melihat hasil investigasi CCP saat membuka show
        $this->actingAs($ghCcp)
            ->get(route('komplain.show', encrypt($komplain->id)))
            ->assertOk()
            ->assertSee('Hasil Temuan &amp; Tindakan Tim CCP', false)
            ->assertSee('Diganti BMHP')
            ->assertSee('Hasil investigasi batch 2026-X cacat segel', false)
            ->assertSee('temuan_cacat.jpg')
            ->assertSee('laporan_investigasi.pdf')
            ->assertSee('Peninjauan Group Head CCP');

        // Admin juga dapat melihat seluruh hasil temuan dan dokumen
        $adminUser = User::factory()->create();
        $adminUser->assignRole('Admin');
        $this->actingAs($adminUser)
            ->get(route('komplain.show', encrypt($komplain->id)))
            ->assertOk()
            ->assertSee('Hasil Temuan &amp; Tindakan Tim CCP', false)
            ->assertSee('Diganti BMHP')
            ->assertSee('temuan_cacat.jpg');

        // 6. GH CCP menyetujui keputusan akhir -> Status jadi Selesai
        $this->actingAs($ghCcp)
            ->post(route('komplain.approve', encrypt($komplain->id)), [
                'Catatan' => 'Disetujui untuk penggantian barang',
            ])
            ->assertSessionMissing('error');

        $this->assertSame('Selesai', $komplain->fresh()->Status);

        // Semua 6 tahapan approval sekarang berstatus Approved
        $allApproved = DokumenApproval::where('JenisFormId', $formId)
            ->where('DokumenId', $komplain->id)
            ->where('Status', '!=', 'Approved')
            ->count();
        $this->assertSame(0, $allApproved);

        // Verifikasi cetak PDF via dompdf
        $pdfResponse = $this->actingAs($adminUser)
            ->get(route('komplain.cetak', encrypt($komplain->id)))
            ->assertOk();
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', $pdfResponse->getContent());
    }
}
