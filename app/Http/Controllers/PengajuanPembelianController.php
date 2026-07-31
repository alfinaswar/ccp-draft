<?php

namespace App\Http\Controllers;

use App\Mail\NotifikasiApprovalDirekturGroupMail;
use App\Models\AktivitasPengajuan;
use App\Models\AturanPengajuan;
use App\Models\AturanPengajuanPresentasi;
use App\Models\DokumenApproval;
use App\Models\FeasibilityStudy;
use App\Models\HtaDanGpa;
use App\Models\HtaDanGpaDetail;
use App\Models\HtaMedis;
use App\Models\LembarDisposisi;
use App\Models\ListVendor;
use App\Models\ListVendorDetail;
use App\Models\MasterBarang;
use App\Models\MasterDepartemen;
use App\Models\MasterForm;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterParameter;
use App\Models\MasterPerusahaan;
use App\Models\MasterVendor;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use App\Models\PermintaanPembelian;
use App\Models\Rekomendasi;
use App\Models\TutupPengajuan;
use App\Models\User;
use App\Models\UsulanInvestasi;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PengajuanPembelianController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // dd($request->tanggal_presentasi);
            $kodePerusahaan = auth()->user()->kodeperusahaan;
            $user = auth()->user();
            $hiddenStatuses = ['Selesai', 'Ditolak CEO', 'Disetujui CEO'];
            if ($user->hasRole('Admin') || $user->hasRole('CCP') || $user->hasRole('CEO') || $user->hasRole('Group Head')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->orderBy('id', 'desc');
                if ($request->filled('perusahaan')) {
                    $data->where('KodePerusahaan', $request->perusahaan);
                }
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                } else {
                    $data->whereNotIn('Status', $hiddenStatuses);
                }
                if ($request->filled('tanggal_presentasi')) {
                    $data->where('TanggalPresentasi', $request->tanggal_presentasi);
                }
            } elseif ($user->hasRole('SMI')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', 1)
                    ->orderBy('id', 'desc');
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                } else {
                    $data->whereNotIn('Status', $hiddenStatuses);
                }
                if ($request->filled('tanggal_presentasi')) {
                    $data->where('TanggalPresentasi', $request->tanggal_presentasi);
                }
            } elseif ($user->hasRole('LOGUM')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', '!=', 1)
                    ->orderBy('id', 'desc');

                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                } else {
                    $data->whereNotIn('Status', $hiddenStatuses);
                }
                if ($request->filled('tanggal_presentasi')) {
                    $data->where('TanggalPresentasi', $request->tanggal_presentasi);
                }
            } else {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->orderBy('id', 'desc');
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
                if ($request->filled('tanggal_presentasi')) {
                    $data->where('TanggalPresentasi', $request->tanggal_presentasi);
                }

                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                } else {
                    $data->whereNotIn('Status', $hiddenStatuses);
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                // contoh penerapan pencarian di kolom realasi/virtual (supaya search datatable bisa)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->input('search.value') != '') {
                        $search = $request->input('search.value');
                        // Search pada kolom KodePengajuan, Status, relasi Nama Perusahaan, Nama Barang, dsb
                        $query->where(function ($q) use ($search) {
                            $q->where('KodePengajuan', 'like', "%{$search}%")
                                ->orWhere('Status', 'like', "%{$search}%")
                                ->orWhereHas('getPerusahaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getJenisPermintaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%")
                                        ->orWhere('Tipe', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->editColumn('Jenis', function ($row) {
                    // Kolom Jenis ini berubah menjadi Nama dari relasi
                    return optional($row->getJenisPermintaan)->Nama ?? '-';
                })
                ->addColumn('LokasiPenempatan', function ($row) {
                    $lokasi = '-';
                    if (isset($row->getPermintaan) && isset($row->getPermintaan->getDetail) && count($row->getPermintaan->getDetail) > 0) {
                        $lokasi = $row->getPermintaan->getDetail[0]->RencanaPenempatan ?? '-';
                    }
                    return $lokasi;
                })

                ->editColumn('KodePerusahaan', function ($row) {
                    return $row->getPerusahaan->Nama ?? '-';
                })
                ->addColumn('KodePengajuan', function ($row) {
                    $id = encrypt($row->id);
                    $kode = isset($row->KodePengajuan) ? $row->KodePengajuan : '-';
                    return '<a href="' . route('ajukan.show', $id) . '" style="color: #007bff; font-weight: bold;">' . e($kode) . '</a>';
                })
                ->addColumn('NamaBarang', function ($row) {
                    $namaBarang = '-';
                    $merek = '-';
                    $tipe = '-';
                    if ($row->getPengajuanItem && count($row->getPengajuanItem) > 0) {
                        $item = $row->getPengajuanItem[0];
                        $namaBarang = $item->getBarang->Nama ?? '-';
                        $merek = $item->getBarang->getMerk->Nama ?? '';
                        $tipe = $item->getBarang->Tipe ?? '-';
                    }
                    return $namaBarang . ' / ' . $merek . ' / ' . $tipe;
                })

                ->addColumn('action', function ($row) {
                    $id = encrypt($row->id);

                    $buttonDelete = '';
                    if ($row->Status === 'Draft') {
                        $buttonDelete = '
                            <button class="btn btn-md btn-danger btn-delete" data-id="' . $id . '" title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        ';
                    }

                    $buttonTracking = '
                        <a href="' . route('rekomendasi.tracking', $id) . '" class="btn btn-md btn-warning" title="Tracking Progres" target="_blank">
                            <i class="fa fa-route"></i> Tracking
                        </a>
                    ';

                    return $buttonDelete . ' ' . $buttonTracking;
                })

                ->addColumn('CekStatus', function ($row) {
                    $html = '-';
                    // if ($row->Jenis == 1) {
                    $hta = [1, 2, 16];
                    $fui = [7, 11, 12, 13, 14, 15];
                    $dispo = [9, 10];
                    // $cariHTa = HtaDanGpa::where('JenisForm', $hta)->where('IdPengajuan', $row->id)->first();
                    $cekHta = null;
                    foreach ($hta as $JenisHta) {
                        $cariHTaItem = HtaDanGpa::where('JenisForm', $JenisHta)->where('IdPengajuan', $row->id)->first();
                        if ($cariHTaItem) {
                            $cek = DokumenApproval::where('JenisFormId', $JenisHta)
                                ->where('DokumenId', $cariHTaItem->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                            if ($cek) {
                                $cekHta = $cek;
                                break;
                            }
                        }
                    }

                    $cekFui = null;
                    foreach ($fui as $JenisFui) {

                        $cariFUI = UsulanInvestasi::where('JenisForm', $JenisFui)->where('IdPengajuan', $row->id)->first();
                        $cek = null;
                        if ($cariFUI) {
                            $cek = DokumenApproval::where('JenisFormId', $JenisFui)
                                ->where('DokumenId', $cariFUI->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }
                        if ($cek) {
                            $cekFui = $cek;
                            break;
                        }
                    }
                    // Cari dokumen Disposisi yang sesuai pengajuan
                    $cekDispo = null;
                    foreach ($dispo as $JenisDispo) {
                        $cariDispoItem = LembarDisposisi::where('JenisForm', $JenisDispo)->where('IdPengajuan', $row->id)->first();
                        if ($cariDispoItem) {
                            $cek = DokumenApproval::where('JenisFormId', $JenisDispo)
                                ->where('DokumenId', $cariDispoItem->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                            if ($cek) {
                                $cekDispo = $cek;
                                break;
                            }
                        }
                    }


                    $pesan = [];
                    if ($cekDispo) {
                        $pesan[] = '<span style="color:#dc3545;"><b>Form: Disposisi</b> - <i>Perlu Disetujui</i></span>';
                    }
                    if ($cekFui) {
                        $pesan[] = '<span style="color:#dc3545;"><b>Form: FUI</b> - <i>Perlu Disetujui</i></span>';
                    }
                    if ($cekHta) {
                        $pesan[] = '<span style="color:#dc3545;"><b>Form: HTA</b> - <i>Perlu Disetujui</i></span>';
                    }

                    if (!empty($pesan)) {
                        $html = implode('<br>', $pesan);
                    }
                    // }
                    return $html;
                })
                ->addColumn('Status', function ($row) {
                    switch ($row->Status) {
                        case 'Draft':
                            // Abu-abu, icon pencil
                            return '<span class="badge" style="background-color:#6c757d;color:#fff;">
                                <i class="fa fa-pencil-alt"></i> Draft
                            </span>';
                        case 'Diajukan':
                            // Biru, icon paper-plane
                            return '<span class="badge" style="background-color:#0d6efd;color:#fff;">
                                <i class="fa fa-paper-plane"></i> Diajukan
                            </span>';
                        case 'Dalam Review':
                            // Jingga, icon search
                            return '<span class="badge" style="background-color:#fd7e14;color:#fff;">
                                <i class="fa fa-search"></i> Dalam Review
                            </span>';
                        case 'Selesai':
                            // Hijau tua, icon flag-checkered
                            return '<span class="badge" style="background-color:#198754;color:#fff;">
                                <i class="fa fa-flag-checkered"></i> Selesai
                            </span>';
                        case 'Disetujui':
                            // Hijau muda, icon thumbs-up
                            return '<span class="badge" style="background-color:#20c997;color:#fff;">
                                <i class="fa fa-thumbs-up"></i> Disetujui
                            </span>';
                        case 'Siap Presentasi':
                            // Biru muda, icon chalkboard-teacher
                            return '<span class="badge" style="background-color:#0dcaf0;color:#000;">
                                <i class="fa fa-chalkboard-teacher"></i> Siap Presentasi
                            </span>';
                        case 'Selesai Review':
                            // Ungu, icon clipboard-check
                            return '<span class="badge" style="background-color:#6f42c1;color:#fff;">
                                <i class="fa fa-clipboard-check"></i> Selesai Review
                            </span>';
                        case 'Menunggu Rekomendasi GH':
                            // Oranye, icon hourglass-half
                            return '<span class="badge" style="background-color:#ffc107;color:#212529;">
                                <i class="fa fa-hourglass-half"></i> Menunggu Rekomendasi GH
                            </span>';
                        case 'Ditolak':
                            // Merah, icon times-circle
                            return '<span class="badge" style="background-color:#dc3545;color:#fff;">
                                <i class="fa fa-times-circle"></i> Ditolak
                            </span>';
                        default:
                            return '<span class="badge" style="background-color:#f8f9fa;color:#212529;">
                                <i class="fa fa-question-circle"></i> ' . e($row->Status ?? '-') . '
                            </span>';
                    }
                })
                ->addColumn('TanggalPresentasi', function ($row) {
                    return $row->TanggalPresentasi
                        ? \Carbon\Carbon::parse($row->TanggalPresentasi)->translatedFormat('d M Y H:i')
                        : '-';
                })
                ->rawColumns(['action', 'LokasiPenempatan', 'KodePengajuan', 'Status', 'NamaBarang', 'TanggalPresentasi', 'CekStatus'])
                ->make(true);
        }
        $kodePerusahaan = auth()->user()->kodeperusahaan;
        $jenisPermintaan = MasterJenisPengajuan::get();
        $perusahaan = MasterPerusahaan::get();
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDepartemen', 'getDiajukanOleh', 'getJenisPermintaan', 'getDetail')
            ->where('Status', 'Telah Disetujui')
            ->where('KodePerusahaan', $kodePerusahaan)
            ->whereDoesntHave('getPengajuanPembelian')
            ->latest()
            ->get();

        return view('form.pengajuan-pembelian.index', compact('permintaan', 'jenisPermintaan', 'perusahaan'));
    }
    public function indexSelesai(Request $request)
    {
        if ($request->ajax()) {
            $kodePerusahaan = auth()->user()->kodeperusahaan;
            $user = auth()->user();
            if ($user->hasRole('Admin') || $user->hasRole('CCP') || $user->hasRole('CEO') || $user->hasRole('Group Head')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('Status', 'Selesai')
                    ->orderBy('TanggalPresentasi', 'desc');
                if ($request->filled('perusahaan')) {
                    $data->where('KodePerusahaan', $request->perusahaan);
                }
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
            } elseif ($user->hasRole('SMI')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', 1)
                    ->where('Status', 'Selesai')
                    ->orderBy('TanggalPresentasi', 'desc');
            } elseif ($user->hasRole('LOGUM')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', '!=', 1)
                    ->where('Status', 'Selesai')
                    ->orderBy('TanggalPresentasi', 'desc');
            } else {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem', 'getPermintaan'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Status', 'Selesai')
                    ->orderBy('TanggalPresentasi', 'desc');
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                // contoh penerapan pencarian di kolom realasi/virtual (supaya search datatable bisa)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->input('search.value') != '') {
                        $search = $request->input('search.value');
                        // Search pada kolom KodePengajuan, Status, relasi Nama Perusahaan, Nama Barang, dsb
                        $query->where(function ($q) use ($search) {
                            $q->where('KodePengajuan', 'like', "%{$search}%")
                                ->orWhere('Status', 'like', "%{$search}%")
                                ->orWhereHas('getPerusahaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getJenisPermintaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%")
                                        ->orWhere('Tipe', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->editColumn('Jenis', function ($row) {
                    // Kolom Jenis ini berubah menjadi Nama dari relasi
                    return optional($row->getJenisPermintaan)->Nama ?? '-';
                })
                ->editColumn('KodePerusahaan', function ($row) {
                    return $row->getPerusahaan->Nama ?? '-';
                })
                ->addColumn('KodePengajuan', function ($row) {
                    $id = encrypt($row->id);
                    $kode = isset($row->KodePengajuan) ? $row->KodePengajuan : '-';
                    return '<a href="' . route('ajukan.show', $id) . '" style="color: #007bff; font-weight: bold;">' . e($kode) . '</a>';
                })
                ->addColumn('LokasiPenempatan', function ($row) {
                    // Null safety check
                    if (
                        $row->getPermintaan &&
                        isset($row->getPermintaan->getDetail) &&
                        is_array($row->getPermintaan->getDetail) &&
                        isset($row->getPermintaan->getDetail[0]) &&
                        isset($row->getPermintaan->getDetail[0]->RencanaPenempatan)
                    ) {
                        return $row->getPermintaan->getDetail[0]->RencanaPenempatan;
                    }
                    return '-';
                })

                ->addColumn('NamaBarang', function ($row) {
                    $namaBarang = '-';
                    $merek = '-';
                    if ($row->getPengajuanItem && count($row->getPengajuanItem) > 0) {
                        $item = $row->getPengajuanItem[0];
                        $namaBarang = $item->getBarang->Nama ?? '-';
                        $merek = $item->getBarang->getMerk->Nama ?? '';
                        $tipe = $item->getBarang->Tipe ?? '-';
                    }
                    return $namaBarang . ' / ' . $merek;
                })
                ->addColumn('action', function ($row) {
                    $id = encrypt($row->id);

                    $buttonDelete = '
                        <button class="btn btn-md btn-danger btn-delete" data-id="' . $id . '" title="Hapus">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';

                    $buttonTracking = '
                        <a href="' . route('rekomendasi.tracking', $id) . '" class="btn btn-md btn-warning" title="Tracking Progres" target="_blank">
                            <i class="fa fa-route"></i> Tracking
                        </a>
                    ';

                    return $buttonDelete . ' ' . $buttonTracking;
                })

                ->addColumn('CekStatus', function ($row) {
                    $html = '-';
                    if ($row->Jenis === 1) {
                        $hta = 1;
                        $fui = [7, 11, 12];
                        $dispo = 9;
                        $cariHTa = HtaDanGpa::where('JenisForm', $hta)->where('IdPengajuan', $row->id)->first();
                        $cekHta = null;
                        if ($cariHTa) {
                            $cekHta = DokumenApproval::where('JenisFormId', $hta)
                                ->where('DokumenId', $cariHTa->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }
                        $cekFui = null;
                        foreach ($fui as $JenisFui) {
                            $cariFUI = UsulanInvestasi::where('JenisForm', $JenisFui)->where('IdPengajuan', $row->id)->first();
                            $cek = null;
                            if ($cariFUI) {
                                $cek = DokumenApproval::where('JenisFormId', $JenisFui)
                                    ->where('DokumenId', $cariFUI->id)
                                    ->where('UserId', auth()->user()->id)
                                    ->where('Status', 'Pending')
                                    ->first();
                            }
                            if ($cek) {
                                $cekFui = $cek;
                                break;
                            }
                        }

                        // Cari dokumen Disposisi yang sesuai pengajuan
                        $cariDispo = LembarDisposisi::where('JenisForm', $dispo)->where('IdPengajuan', $row->id)->first();
                        $cekDispo = null;
                        if ($cariDispo) {
                            $cekDispo = DokumenApproval::where('JenisFormId', $dispo)
                                ->where('DokumenId', $cariDispo->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }

                        $pesan = [];
                        if ($cekDispo) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: Disposisi</b> - <i>Perlu Disetujui</i></span>';
                        }
                        if ($cekFui) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: FUI</b> - <i>Perlu Disetujui</i></span>';
                        }
                        if ($cekHta) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: HTA</b> - <i>Perlu Disetujui</i></span>';
                        }

                        if (!empty($pesan)) {
                            $html = implode('<br>', $pesan);
                        }
                    }
                    return $html;
                })
                ->addColumn('Status', function ($row) {
                    switch ($row->Status) {
                        case 'Draft':
                            // Abu-abu, icon pencil
                            return '<span class="badge" style="background-color:#6c757d;color:#fff;">
                                <i class="fa fa-pencil-alt"></i> Draft
                            </span>';
                        case 'Diajukan':
                            // Biru, icon paper-plane
                            return '<span class="badge" style="background-color:#0d6efd;color:#fff;">
                                <i class="fa fa-paper-plane"></i> Diajukan
                            </span>';
                        case 'Dalam Review':
                            // Jingga, icon search
                            return '<span class="badge" style="background-color:#fd7e14;color:#fff;">
                                <i class="fa fa-search"></i> Dalam Review
                            </span>';
                        case 'Selesai':
                            // Hijau tua, icon flag-checkered
                            return '<span class="badge" style="background-color:#198754;color:#fff;">
                                <i class="fa fa-flag-checkered"></i> Selesai
                            </span>';
                        case 'Disetujui':
                            // Hijau muda, icon thumbs-up
                            return '<span class="badge" style="background-color:#20c997;color:#fff;">
                                <i class="fa fa-thumbs-up"></i> Disetujui
                            </span>';
                        case 'Siap Presentasi':
                            // Biru muda, icon chalkboard-teacher
                            return '<span class="badge" style="background-color:#0dcaf0;color:#000;">
                                <i class="fa fa-chalkboard-teacher"></i> Siap Presentasi
                            </span>';
                        case 'Selesai Review':
                            // Ungu, icon clipboard-check
                            return '<span class="badge" style="background-color:#6f42c1;color:#fff;">
                                <i class="fa fa-clipboard-check"></i> Selesai Review
                            </span>';
                        case 'Menunggu Rekomendasi GH':
                            // Oranye, icon hourglass-half
                            return '<span class="badge" style="background-color:#ffc107;color:#212529;">
                                <i class="fa fa-hourglass-half"></i> Menunggu Rekomendasi GH
                            </span>';
                        case 'Ditolak':
                            // Merah, icon times-circle
                            return '<span class="badge" style="background-color:#dc3545;color:#fff;">
                                <i class="fa fa-times-circle"></i> Ditolak
                            </span>';
                        default:
                            return '<span class="badge" style="background-color:#f8f9fa;color:#212529;">
                                <i class="fa fa-question-circle"></i> ' . e($row->Status ?? '-') . '
                            </span>';
                    }
                })
                ->addColumn('TanggalPresentasi', function ($row) {
                    return $row->TanggalPresentasi
                        ? \Carbon\Carbon::parse($row->TanggalPresentasi)->translatedFormat('d F Y')
                        : '-';
                })
                ->rawColumns(['action', 'LokasiPenempatan', 'KodePengajuan', 'Status', 'NamaBarang', 'TanggalPresentasi', 'CekStatus'])
                ->make(true);
        }
    }
    /**}
     * Show the form for creating a new resource..show
     */
    public function create($id)
    {
        $vendor = MasterVendor::where('Status', 'Y')->orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDepartemen', 'getDiajukanOleh', 'getDetail', 'getPengajuanPembelian.getVendor.getVendorDetail')->find($id);
        $JenisPengajuan = MasterJenisPengajuan::get();
        $departemen = MasterDepartemen::get();
        return view('form.pengajuan-pembelian.create', compact('JenisPengajuan', 'masterbarang', 'permintaan', 'vendor', 'departemen'));
    }

    public function SimpanDraft($id)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'pengajuan.tanggal' => 'required|date',
            'pengajuan.id_permintaan' => 'required',
            'pengajuan.jenis' => 'required|string|max:255',
            'pengajuan.tujuan' => 'required|string|max:255',
            'pengajuan.perkiraan_utilitasi_bulanan' => 'required',
            'pengajuan.perkiraan_bep_pada_tahun' => 'required',
            'pengajuan.rkap' => 'required',
            'pengajuan.nominal_rkap' => 'required',
            'vendors.*.penawaran_file.*' => 'required|file|max:5120',
        ]);

        $nomor = $this->generateNomorPengajuan();

        $pengajuan = PengajuanPembelian::updateOrCreate(
            [
                'IdPermintaan' => $request->pengajuan['id_permintaan'],
            ],
            [
                'KodePengajuan' => $nomor,
                'Tanggal' => $request->pengajuan['tanggal'],
                'Jenis' => $request->pengajuan['jenis'],
                'Tujuan' => $request->pengajuan['tujuan'],
                'PerkiraanUtilitasiBulanan' => $request->pengajuan['perkiraan_utilitasi_bulanan'],
                'PerkiraanBepPadaTahun' => $request->pengajuan['perkiraan_bep_pada_tahun'],
                'Rkap' => $request->pengajuan['rkap'],
                'NominalRkap' => preg_replace('/\D/', '', $request->pengajuan['nominal_rkap']),
                'DepartemenId' => $request->pengajuan['departemen'],
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
                'UserCreate' => auth()->user()->name,
            ]
        );

        foreach ($request->vendors as $key => $vendorData) {
            if (!isset($vendorData['vendor_id']) || $vendorData['vendor_id'] === null) {
                continue;
            }
            $filename = null;
            $ListVendorOld = ListVendor::where('IdPengajuan', $pengajuan->id)
                ->where('VendorKe', $key + 1)
                ->first();
            $filename = null;
            $isOldFileExists = ($ListVendorOld && $ListVendorOld->SuratPenawaranVendor);
            if (isset($vendorData['penawaran_file']) && is_array($vendorData['penawaran_file'])) {
                $fileArr = $vendorData['penawaran_file'];
                if (isset($fileArr[0]) && $fileArr[0] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $fileArr[0];
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                } elseif (isset($fileArr[0]) && is_string($fileArr[0]) && $fileArr[0]) {
                    $filename = $fileArr[0];
                }
            } elseif (isset($vendorData['penawaran_file']) && is_string($vendorData['penawaran_file']) && $vendorData['penawaran_file'] !== null && $vendorData['penawaran_file'] !== '') {
                $filename = $vendorData['penawaran_file'];
            } elseif ($request->hasFile('penawaran_file_' . ($key + 1))) {
                $file = $request->file('penawaran_file_' . ($key + 1));
                if ($file) {
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                }
            } elseif ($isOldFileExists) {
                $filename = $ListVendorOld->SuratPenawaranVendor;
            }
            if (!$filename) {
                return back()
                    ->withErrors(['vendors.' . $key . '.penawaran_file.0' => 'File penawaran vendor wajib diupload.'])
                    ->withInput();
            }

            $ListVendor = ListVendor::updateOrCreate(
                [
                    'IdPengajuan' => $pengajuan->id,
                    'VendorKe' => $key + 1,
                ],
                [
                    'NamaVendor' => $vendorData['vendor_id'],
                    'NamaPic' => $vendorData['nama_pic'],
                    'KontakPic' => $vendorData['no_hp_pic'],
                    'SuratPenawaranVendor' => $filename,
                    'HargaTanpaDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) : 0,
                    'HargaDenganDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) : 0,
                    'TotalDiskon' => preg_replace('/\D/', '', $vendorData['total_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_diskon']) : 0,
                    'Ppn' => isset($vendorData['ppn_persen']) && $vendorData['ppn_persen'] !== '' ? $vendorData['ppn_persen'] : 0,
                    'TotalPpn' => preg_replace('/\D/', '', $vendorData['total_ppn']) !== '' ? preg_replace('/\D/', '', $vendorData['total_ppn']) : 0,
                    'TotalHarga' => preg_replace('/\D/', '', $vendorData['grand_total']) !== '' ? preg_replace('/\D/', '', $vendorData['grand_total']) : 0,
                    'KodePerusahaan' => auth()->user()->kodeperusahaan,
                    // Masukkan UserCreate atau UserUpdate sesuai kebutuhan
                    'UserCreate' => auth()->user()->name,
                    'UserUpdate' => auth()->user()->name,
                ]
            );

            if (isset($vendorData['details']) && is_array($vendorData['details'])) {
                ListVendorDetail::where('IdPengajuan', $pengajuan->id)
                    ->where('IdListVendor', $ListVendor->id)
                    ->forceDelete();

                foreach ($vendorData['details'] as $detailB) {
                    $diskonValue = 0;
                    if (isset($detailB['diskon_item'])) {
                        if (isset($detailB['jenis_diskon_item']) && strtolower($detailB['jenis_diskon_item']) == 'rp') {
                            // Jika Rp, ambil angka aja
                            $diskonValue = preg_replace('/\D/', '', $detailB['diskon_item']);
                        } else {
                            // Jika bukan Rp, buang semua karakter kecuali titik, dan jika ada koma ganti menjadi titik
                            $diskonValue = str_replace(',', '.', $detailB['diskon_item']);
                            $diskonValue = preg_replace('/[^0-9.]/', '', $diskonValue);
                        }
                    }
                    ListVendorDetail::create([
                        'IdPengajuan' => $pengajuan->id,
                        'IdListVendor' => $ListVendor->id,
                        'NamaBarang' => $detailB['barang_id'] ?? 0,
                        'NamaVendor' => null,
                        'Jumlah' => isset($detailB['jumlah']) ? $detailB['jumlah'] : 0,
                        'HargaSatuan' => isset($detailB['harga_satuan']) ? preg_replace('/\D/', '', $detailB['harga_satuan']) : 0,
                        'Diskon' => $diskonValue,
                        'JenisDiskon' => $detailB['jenis_diskon_item'] ?? 0,
                        'TotalDiskon' => isset($detailB['total_diskon']) ? preg_replace('/\D/', '', $detailB['total_diskon']) : 0,
                        'TotalHarga' => isset($detailB['total_harga']) ? preg_replace('/\D/', '', $detailB['total_harga']) : 0,
                        'KodePerusahaan' => auth()->user()->kodeperusahaan,
                        'UserCreate' => auth()->user()->name,
                    ]);
                }
            }
        }
        foreach ($request->vendors[0]['details'] as $key => $listalat) {
            PengajuanItem::updateOrCreate(
                [
                    'IdPengajuan' => $pengajuan->id,
                    'IdBarang' => $listalat['barang_id'],
                ],
                [
                    'RencanaPenempatan' => $listalat['rencana_penempatan'] ?? null,
                    'DiajukanOleh' => $listalat['diajukan_oleh'] ?? null,
                    'DiajukanDepartemen' => $request->pengajuan['departemen'] ?? null,
                    'Jumlah' => $listalat['jumlah'] ?? null,
                    'Satuan' => $listalat['satuan'] ?? null,
                    'VendorAcc' => $listalat['vendor_acc'] ?? null,
                    'HargaSatuanAcc' => isset($listalat['harga_satuan_acc']) ? preg_replace('/\D/', '', $listalat['harga_satuan_acc']) : null,
                    'HargaNegoAcc' => isset($listalat['harga_nego_acc']) ? preg_replace('/\D/', '', $listalat['harga_nego_acc']) : null,
                    'HargaAkhirFui' => isset($listalat['harga_akhir_fui']) ? preg_replace('/\D/', '', $listalat['harga_akhir_fui']) : null,
                    'KodePerusahaan' => auth()->user()->kodeperusahaan,
                    'UserCreate' => auth()->user()->name,
                ]
            );
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $pengajuan->KodePengajuan,
            'Jenis' => 'Pengajuan Pembelian',
            'Keterangan' => 'Membuat pengajuan pembelian baru dengan nomor ' . $pengajuan->KodePengajuan,
            'UserCreate' => auth()->user()->name,
        ]);

        activity('pengajuan_pembelian')
            ->causedBy(auth()->user())
            ->performedOn($pengajuan)
            ->withProperties([
                'attributes' => $pengajuan->toArray(),
                'vendors' => $request->vendors,
            ])
            ->log('Membuat pengajuan pembelian baru dengan kode ' . $pengajuan->KodePengajuan);
        return redirect()->back()->with('success', 'Pengisian vendor berhasil, silahkan lanjutkan isi vendor selanjutnya jika diperlukan');
    }

    private function generateNomorPengajuan()
    {
        $prefix = 'PJ';
        $tahun = date('y');  // 2 digit tahun
        $bulan = date('m');  // 2 digit bulan
        do {
            $maxNomor = PengajuanPembelian::where('KodePengajuan', 'like', "{$prefix}{$tahun}{$bulan}%")
                ->orderByDesc('KodePengajuan')
                ->value('KodePengajuan');

            if ($maxNomor) {
                $lastNumber = (int) substr($maxNomor, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $nomorAkhir = $prefix . $tahun . $bulan . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = PengajuanPembelian::where('KodePengajuan', $nomorAkhir)->exists();
        } while ($exists);

        return $nomorAkhir;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = decrypt($id);
        $data = PengajuanPembelian::with('getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getPengajuanItem.getDisposisi', 'getDepartemen', 'getPengajuanItem.getFs')->find($id);
        // dd($data);
        $tutup = TutupPengajuan::first();
        $hariBuka = AturanPengajuan::get();
        $hariBukaPresentasi = AturanPengajuanPresentasi::get();
        $vendor = MasterVendor::orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        return view('form.pengajuan-pembelian.show', compact('data', 'vendor', 'masterbarang', 'tutup', 'hariBuka', 'hariBukaPresentasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $vendor = MasterVendor::where('Status', 'Y')->orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDepartemen', 'getDiajukanOleh', 'getDetail', 'getPengajuanPembelian.getVendor.getVendorDetail')->find($id);
        $JenisPengajuan = MasterJenisPengajuan::get();
        $departemen = MasterDepartemen::get();
        $data = PengajuanPembelian::with('getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getDepartemen')->find($id);
        // dd($id);
        return view('form.pengajuan-pembelian.edit', compact('JenisPengajuan', 'masterbarang', 'permintaan', 'vendor', 'departemen', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'pengajuan.tanggal' => 'required|date',
            'pengajuan.id_permintaan' => 'required',
            'pengajuan.jenis' => 'required|string|max:255',
            'pengajuan.tujuan' => 'required|string|max:255',
            'pengajuan.perkiraan_utilitasi_bulanan' => 'required',
            'pengajuan.perkiraan_bep_pada_tahun' => 'required',
            'pengajuan.rkap' => 'required',
            'pengajuan.nominal_rkap' => 'required',
        ]);

        $pengajuan = PengajuanPembelian::findOrFail($id);

        $pengajuan->Tanggal = $request->pengajuan['tanggal'];
        $pengajuan->Jenis = $request->pengajuan['jenis'];
        $pengajuan->Tujuan = $request->pengajuan['tujuan'];
        $pengajuan->PerkiraanUtilitasiBulanan = $request->pengajuan['perkiraan_utilitasi_bulanan'];
        $pengajuan->PerkiraanBepPadaTahun = $request->pengajuan['perkiraan_bep_pada_tahun'];
        $pengajuan->Rkap = $request->pengajuan['rkap'];
        $pengajuan->NominalRkap = preg_replace('/\D/', '', $request->pengajuan['nominal_rkap']);
        $pengajuan->DepartemenId = $request->pengajuan['departemen'];
        $pengajuan->KodePerusahaan = auth()->user()->kodeperusahaan;
        $pengajuan->UserUpdate = auth()->user()->name;
        $pengajuan->save();

        // Dapatkan vendor lama & detailnya
        $existingVendors = ListVendor::where('IdPengajuan', $pengajuan->id)->get()->keyBy('VendorKe');
        $existingVendorDetails = ListVendorDetail::where('IdPengajuan', $pengajuan->id)->get()->groupBy('IdListVendor');

        $usedVendorKeys = [];

        foreach ($request->vendors as $key => $vendorData) {
            if (!isset($vendorData['vendor_id']) || $vendorData['vendor_id'] === null) {
                continue;
            }

            $vendorKe = $key + 1;  // Penentu urutan vendor
            $usedVendorKeys[] = $vendorKe;

            // Cek jika vendor ke-x sudah ada
            $ListVendor = $existingVendors->get($vendorKe);
            $filename = null;

            if ($ListVendor) {
                // Sudah ada, patch/replace data vendor
            }

            // Batasi ukuran file 5 MB (5 * 1024 * 1024)
            $maxFileSize = 5 * 1024 * 1024;
            if (isset($vendorData['penawaran_file']) && $vendorData['penawaran_file'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $vendorData['penawaran_file'];
                if ($file->getSize() > $maxFileSize) {
                    return redirect()->back()->with('error', 'Ukuran file penawaran vendor melebihi batas maksimum 5 MB.');
                }
                $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('penawaran_vendor', $filename, 'public');
            } elseif (isset($vendorData['penawaran_file']) && is_array($vendorData['penawaran_file'])) {
                $fileArr = $vendorData['penawaran_file'];
                if (isset($fileArr[0]) && $fileArr[0] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $fileArr[0];
                    if ($file->getSize() > $maxFileSize) {
                        return redirect()->back()->with('error', 'Ukuran file penawaran vendor melebihi batas maksimum 5 MB.');
                    }
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                } elseif (isset($fileArr[0]) && is_string($fileArr[0]) && $fileArr[0]) {
                    $filename = $fileArr[0];
                }
            } elseif (isset($vendorData['penawaran_file']) && is_string($vendorData['penawaran_file']) && $vendorData['penawaran_file'] !== null && $vendorData['penawaran_file'] !== '') {
                $filename = $vendorData['penawaran_file'];
            } elseif ($request->hasFile('penawaran_file_' . ($key + 1))) {
                $file = $request->file('penawaran_file_' . ($key + 1));
                if ($file) {
                    if ($file->getSize() > $maxFileSize) {
                        return redirect()->back()->with('error', 'Ukuran file penawaran vendor melebihi batas maksimum 5 MB.');
                    }
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                }
            } elseif ($ListVendor && $ListVendor->SuratPenawaranVendor) {
                $filename = $ListVendor->SuratPenawaranVendor;
            }

            $vendorDataToSave = [
                'IdPengajuan' => $pengajuan->id,
                'VendorKe' => $vendorKe,
                'NamaPic' => isset($vendorData['nama_pic']) && $vendorData['nama_pic'] !== null && $vendorData['nama_pic'] !== '' ? $vendorData['nama_pic'] : 'Tidak Diisi',
                'KontakPic' => isset($vendorData['no_hp_pic']) && $vendorData['no_hp_pic'] !== null && $vendorData['no_hp_pic'] !== '' ? $vendorData['no_hp_pic'] : 'Tidak Diisi',
                'NamaVendor' => $vendorData['vendor_id'],
                'SuratPenawaranVendor' => $filename,
                'HargaTanpaDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) : 0,
                'HargaDenganDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) : 0,
                'TotalDiskon' => preg_replace('/\D/', '', $vendorData['total_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_diskon']) : 0,
                'Ppn' => isset($vendorData['ppn_persen']) && $vendorData['ppn_persen'] !== '' ? $vendorData['ppn_persen'] : 0,
                'TotalPpn' => preg_replace('/\D/', '', $vendorData['total_ppn']) !== '' ? preg_replace('/\D/', '', $vendorData['total_ppn']) : 0,
                'TotalHarga' => preg_replace('/\D/', '', $vendorData['grand_total']) !== '' ? preg_replace('/\D/', '', $vendorData['grand_total']) : 0,
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
                'UserUpdate' => auth()->user()->name,
            ];

            if ($ListVendor) {
                $vendorDataToSave['UserCreate'] = $ListVendor->UserCreate ?: auth()->user()->name;
                $ListVendor->update($vendorDataToSave);
            } else {
                $vendorDataToSave['UserCreate'] = auth()->user()->name;
                $ListVendor = ListVendor::create($vendorDataToSave);
            }

            // Simpan detail vendor, update jika ada, hapus jika diperlukan
            $existingDetailsForThisVendor = $existingVendorDetails->get($ListVendor->id) ?? collect();
            $usedDetailIds = [];

            if (isset($vendorData['details']) && is_array($vendorData['details'])) {
                foreach ($vendorData['details'] as $detailBKey => $detailB) {
                    // Cari jika detail sudah ada berdasarkan urutan
                    $detailToUpdate = $existingDetailsForThisVendor->get($detailBKey);

                    $diskonValue = 0;
                    if (isset($detailB['diskon_item'])) {
                        if (isset($detailB['jenis_diskon_item']) && strtolower($detailB['jenis_diskon_item']) == 'rp') {
                            $diskonValue = preg_replace('/\D/', '', $detailB['diskon_item']);
                        } else {
                            $diskonValue = str_replace(',', '.', $detailB['diskon_item']);
                            $diskonValue = preg_replace('/[^0-9.]/', '', $diskonValue);
                        }
                    }

                    $detailDataToSave = [
                        'IdPengajuan' => $pengajuan->id,
                        'IdListVendor' => $ListVendor->id,
                        'NamaBarang' => $detailB['barang_id'] ?? 0,
                        'NamaVendor' => null,
                        'Jumlah' => isset($detailB['jumlah']) ? $detailB['jumlah'] : 0,
                        'HargaSatuan' => isset($detailB['harga_satuan']) ? (preg_replace('/\D/', '', $detailB['harga_satuan']) !== '' ? preg_replace('/\D/', '', $detailB['harga_satuan']) : 0) : 0,
                        'Diskon' => $diskonValue,
                        'JenisDiskon' => $detailB['jenis_diskon_item'] ?? 0,
                        'TotalDiskon' => isset($detailB['total_diskon']) ? (preg_replace('/\D/', '', $detailB['total_diskon']) !== '' ? preg_replace('/\D/', '', $detailB['total_diskon']) : 0) : 0,
                        'TotalHarga' => isset($detailB['total_harga']) ? (preg_replace('/\D/', '', $detailB['total_harga']) !== '' ? preg_replace('/\D/', '', $detailB['total_harga']) : 0) : 0,
                        'KodePerusahaan' => auth()->user()->kodeperusahaan,
                        'UserCreate' => auth()->user()->name,
                    ];

                    if ($detailToUpdate) {
                        $detailToUpdate->update($detailDataToSave);
                        $usedDetailIds[] = $detailToUpdate->id;
                    } else {
                        $newDetail = ListVendorDetail::create($detailDataToSave);
                        $usedDetailIds[] = $newDetail->id;
                    }
                }
            }

            // Hapus detail yang tidak ada di input baru (hanya milik vendor ini)
            $detailsToDelete = $existingDetailsForThisVendor->whereNotIn('id', $usedDetailIds);
            foreach ($detailsToDelete as $dtd) {
                $dtd->delete();
            }
        }

        // Hapus vendor (dan detailnya) yang tidak ada pada request vendor baru
        $vendorsToDelete = $existingVendors->whereNotIn('VendorKe', $usedVendorKeys);
        foreach ($vendorsToDelete as $vendorDel) {
            // Hapus detail
            ListVendorDetail::where('IdListVendor', $vendorDel->id)->delete();
            // Hapus vendor
            $vendorDel->delete();
        }

        // foreach ($request->vendors[0]['details'] as $key => $listalat) {
        //     PengajuanItem::create([
        //         'IdPengajuan' => $pengajuan->id,
        //         'IdBarang' => $listalat['barang_id'],
        //         'Jumlah' => $listalat['jumlah'] ?? null,
        //         'Satuan' => $listalat['satuan'] ?? null,
        //         'HargaSatuan' => isset($listalat['harga_satuan']) ? preg_replace('/\D/', '', $listalat['harga_satuan']) : null,
        //         'HargaNego' => isset($listalat['harga_nego']) ? preg_replace('/\D/', '', $listalat['harga_nego']) : null,
        //         'UserCreate' => auth()->user()->name,
        //         'UserUpdate' => auth()->user()->name,
        //     ]);
        // }
        AktivitasPengajuan::create([
            'KodePengajuan' => $pengajuan->KodePengajuan,
            'Jenis' => 'Pengajuan Pembelian',
            'Keterangan' => 'Mengupdate pengajuan pembelian dengan nomor ' . $pengajuan->KodePengajuan,
            'UserCreate' => auth()->user()->name,
        ]);
        activity('pengajuan_pembelian')
            ->causedBy(auth()->user())
            ->performedOn($pengajuan)
            ->withProperties([
                'attributes' => $pengajuan->toArray(),
                'vendors' => $request->vendors,
            ])
            ->log('Memperbarui pengajuan pembelian dengan kode ' . $pengajuan->KodePengajuan);
        // dd(123);
        return redirect()->route('ajukan.show', encrypt($pengajuan->id))->with('success', 'Pengajuan pembelian berhasil diperbarui');


    }

    public function UpdatePengajuan(Request $request, $id)
    {
        $data = PengajuanPembelian::with('getRekomendasi.getRekomedasiDetail', 'getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getPengajuanItem.getDisposisi', 'getDepartemen', 'getPengajuanItem.getFs', 'getHtaGpa', 'getPengajuanItem.getFui')->find($id);
        if ($request->Status === 'Ditolak') {
            $pengajuan = PengajuanPembelian::findOrFail($id);
            $pengajuan->Status = $request->Status;
            $pengajuan->Keterangan = $request->Keterangan ?? '';
            $pengajuan->save();

            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user()->id)
                    ->withProperties([
                        'ip' => request()->ip(),
                        'input' => $request->all(),  // Melampirkan semua inputan user
                        'status' => $request->Status,
                        'keterangan' => $request->Keterangan ?? '',
                    ])
                    ->log('Menolak pengajuan pembelian ke CCP: ' . $pengajuan->KodePengajuan);
            }
            AktivitasPengajuan::create([
                'KodePengajuan' => $pengajuan->KodePengajuan,
                'Jenis' => 'Pengajuan Pembelian',
                'Keterangan' => 'Menolak pengajuan pembelian dengan nomor ' . $pengajuan->KodePengajuan . '. Keterangan: ' . ($request->Keterangan ?? '-'),
                'UserCreate' => auth()->user()->name,
            ]);

            return redirect()->back()->with('success', 'Pengajuan Berhasil Ditolak, Segera Informasikan Ke Pengaju');

        }
        // PENGECEKAN UNTUK MAJU PRESENTASI
        if ($request->Status === 'Siap Presentasi') {
            $cariFUI = UsulanInvestasi::where('IdPengajuan', $data->id)->first();
            if (is_null($cariFUI)) {
                return redirect()->back()->with('error', 'Form Usulan Investasi belum diisi. Proses tidak dapat dilanjutkan.');
            }

            $jenisForm = null;

            // === Logika penentuan JenisForm (tetap dipertahankan) ===
            switch ($data->Jenis) {
                case 1:
                    switch (true) {
                        case ($cariFUI->BiayaAkhir < 50000000):
                            $jenisForm = '7';
                            break;
                        case ($cariFUI->BiayaAkhir >= 50000000 && $cariFUI->BiayaAkhir <= 100000000):
                            $jenisForm = '11';
                            break;
                        case ($cariFUI->BiayaAkhir > 100000000):
                            $jenisForm = '12';
                            break;
                    }
                    break;
                default:
                    if ($cariFUI->BiayaAkhir !== null && $cariFUI->BiayaAkhir !== '') {
                        switch (true) {
                            case ($cariFUI->BiayaAkhir < 50000000):
                                $jenisForm = '14';
                                break;
                            case ($cariFUI->BiayaAkhir >= 50000000 && $cariFUI->BiayaAkhir <= 100000000):
                                $jenisForm = '15';
                                break;
                            case ($cariFUI->BiayaAkhir > 100000000):
                                $jenisForm = '13';
                                break;
                        }
                    }
                    break;
            }

            // === Logika sinkronisasi DokumenApproval (tetap dipertahankan) ===
            if ($cariFUI && $jenisForm !== null && $cariFUI->JenisForm != $jenisForm) {
                $ApprovalBenar = MasterForm::with([
                    'getApproval' => function ($query) use ($jenisForm) {
                        $query->where('KodePerusahaan', auth()->user()->kodeperusahaan);
                    }
                ])->find($jenisForm);

                $approvalsBenar = $ApprovalBenar && $ApprovalBenar->getApproval
                    ? $ApprovalBenar->getApproval->sortBy('Urutan')->values()
                    : collect([]);

                $currentApprovals = DokumenApproval::where('DokumenId', $cariFUI->id)
                    ->where('JenisFormId', $cariFUI->JenisForm)
                    ->orderBy('Urutan', 'asc')->get();

                $newApprovalsIds = [];
                $urutan = 1;

                foreach ($approvalsBenar as $approvalBenar) {
                    $existing = $currentApprovals->first(function ($item) use ($approvalBenar) {
                        return $item->UserId == $approvalBenar->UserId;
                    });
                    $user = User::find($approvalBenar->UserId);
                    $namaUser = $user ? $user->name : $approvalBenar->Nama;
                    $emailUser = $user ? $user->email : $approvalBenar->Email;

                    if ($existing) {
                        $oldToken = $existing->ApprovalToken;
                        $existing->Urutan = $urutan;
                        $existing->JenisUser = 'Master';
                        $existing->JabatanId = $approvalBenar->JabatanId;
                        $existing->DepartemenId = $approvalBenar->DepartemenId ?? null;
                        $existing->Nama = $namaUser;
                        $existing->Email = $emailUser;
                        $existing->ApprovalToken = $oldToken;

                        if ($existing->UserId == 81) {
                            $existing->Status = 'Pending';
                            $existing->TanggalApprove = null;
                        } else {
                            $existing->Status = 'Approved';
                            $existing->TanggalApprove = Carbon::now();
                        }
                        $existing->save();
                        $newApprovalsIds[] = $existing->id;
                    } else {
                        $new = DokumenApproval::create([
                            'JenisUser' => 'Master',
                            'JenisFormId' => $cariFUI->JenisForm,
                            'DokumenId' => $cariFUI->id,
                            'PerusahaanId' => auth()->user()->kodeperusahaan ?? null,
                            'JabatanId' => $approvalBenar->JabatanId,
                            'DepartemenId' => $approvalBenar->DepartemenId,
                            'UserId' => $approvalBenar->UserId,
                            'Nama' => $namaUser,
                            'Email' => $emailUser,
                            'Urutan' => $urutan,
                            'Status' => $approvalBenar->UserId == 81 ? 'Pending' : 'Approved',
                            'TanggalApprove' => $approvalBenar->UserId == 81 ? null : Carbon::now(),
                            'Catatan' => null,
                            'Ttd' => null,
                            'ApprovalToken' => str_replace('-', '', Str::uuid()),
                        ]);
                        $newApprovalsIds[] = $new->id;
                    }
                    $urutan++;
                }

                $userIdBenar = $approvalsBenar->pluck('UserId')->toArray();
                foreach ($currentApprovals as $approval) {
                    if (!in_array($approval->UserId, $userIdBenar)) {
                        $approval->delete();
                    }
                }
            } elseif ($cariFUI) {
                $ApprovalBenar = MasterForm::with([
                    'getApproval' => function ($query) use ($jenisForm, $cariFUI) {
                        $query->where('KodePerusahaan', auth()->user()->kodeperusahaan);
                    }
                ])->find($jenisForm ?? $cariFUI->JenisForm);

                $approvalsBenar = $ApprovalBenar && $ApprovalBenar->getApproval
                    ? $ApprovalBenar->getApproval->sortBy('Urutan')->values()
                    : collect([]);

                $currentApprovals = DokumenApproval::where('DokumenId', $cariFUI->id)
                    ->where('JenisFormId', $jenisForm ?? $cariFUI->JenisForm)
                    ->orderBy('Urutan', 'asc')->get();

                $urutan = 1;
                foreach ($approvalsBenar as $approvalBenar) {
                    $existing = $currentApprovals->first(function ($item) use ($approvalBenar) {
                        return $item->UserId == $approvalBenar->UserId;
                    });
                    $user = User::find($approvalBenar->UserId);
                    $namaUser = $user ? $user->name : $approvalBenar->Nama;
                    $emailUser = $user ? $user->email : $approvalBenar->Email;

                    if ($existing) {
                        $oldToken = $existing->ApprovalToken;
                        $existing->Urutan = $urutan;
                        $existing->JabatanId = $approvalBenar->JabatanId;
                        $existing->DepartemenId = $approvalBenar->DepartemenId ?? null;
                        $existing->Nama = $namaUser;
                        $existing->Email = $emailUser;
                        $existing->ApprovalToken = $oldToken;
                        $existing->save();
                    }
                    $urutan++;
                }

                $benarUserIds = $approvalsBenar->pluck('UserId')->toArray();
                foreach ($currentApprovals as $approval) {
                    if (!in_array($approval->UserId, $benarUserIds)) {
                        $approval->delete();
                    }
                }
            }

            // === VALIDASI UNTUK STATUS 'SIAP PRESENTASI' ===
            $cekrekom1 = $data->getRekomendasi[0]->getRekomedasiDetail->where('Rekomendasi', 1)->first();

            if ($data->Jenis == '1') {
                $cekFui = $data->getPengajuanItem[0]->getFui;
                $cek = $data->getPengajuanItem[0]->getFs;

                // 1. Cek HTA/GPA (wajib ada dan approved)
                if (empty($data->getHtaGpa)) {
                    return back()->with('error', 'HTA / GPA belum diisi. Proses tidak dapat diteruskan.');
                }

                $approvalHTA = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $data->getHtaGpa->JenisForm)
                    ->where('DokumenId', $data->getHtaGpa->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedHTA = $approvalHTA->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                // 2. Cek FUI (HANYA wajib ada datanya, TIDAK perlu cek approval)
                if (empty($cekFui)) {
                    return back()->with('error', 'FUI belum diisi. Proses tidak dapat diteruskan.');
                }
                // ✅ Approval FUI TIDAK dicek lagi

                $masterBarangId = $data->getPengajuanItem[0]->getBarang->id ?? null;
                $errors = [];

                // Kondisi khusus: Harga > 100 juta dan bukan barang special case
                if ($cekrekom1->HargaNego > 100000000 && $masterBarangId != 294) {
                    if (!$cek) {
                        return back()->with('error', 'FS wajib diisi untuk pengajuan di atas 100 juta.');
                    }

                    $approvalFS = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                        ->where('JenisFormId', $cek->JenisForm)
                        ->where('DokumenId', $cek->id)
                        ->orderBy('Urutan', 'asc')
                        ->get();

                    $semuaApprovedFS = $approvalFS->every(function ($item) {
                        return $item->Status === 'Approved';
                    });

                    if (!$semuaApprovedFS) {
                        $notApprovedFs = $approvalFS->filter(function ($item) {
                            return $item->Status !== 'Approved';
                        })->values();

                        if ($notApprovedFs->count() > 0) {
                            $list = $notApprovedFs->map(function ($item, $idx) {
                                $name = $item->getUser->name ?? 'Pengguna terkait';
                                return ($idx + 1) . ". {$name}";
                            })->implode('<br>');
                            $errors[] = "Daftar Nama Belum Approve FS:<br>{$list}";
                        } else {
                            $errors[] = 'Dokumen <b>FS</b> belum diapprove.';
                        }
                    }
                }

                // 3. Cek Approval HTA / GPA
                if (!$semuaApprovedHTA) {
                    $notApprovedHta = $approvalHTA->filter(function ($item) {
                        return $item->Status !== 'Approved';
                    })->values();

                    if ($notApprovedHta->count() > 0) {
                        $list = $notApprovedHta->map(function ($item, $idx) {
                            $name = $item->getUser->name ?? 'Pengguna terkait';
                            return ($idx + 1) . ". {$name}";
                        })->implode('<br>');
                        $errors[] = "Daftar Nama Belum Approve HTA / GPA:<br>{$list}";
                    } else {
                        $errors[] = 'Dokumen <b>HTA / GPA</b> belum diapprove.';
                    }
                }

                // ✅ Disposisi: TIDAK dicek sama sekali (dihapus)

                if (count($errors) > 0) {
                    $message = '<b>Mohon lengkapi persetujuan berikut sebelum lanjut:</b><br>' . implode('<br><br>', $errors);
                    return back()->with('error', $message);
                }

            } elseif ($data->Jenis != 1) {
                // dd('aasdsad');
                $errors = [];
                $cekFui = $data->getPengajuanItem[0]->getFui;
                $cek = $data->getPengajuanItem[0]->getFs;

                // 1. Cek HTA/GPA (wajib ada dan approved)
                if (empty($data->getHtaGpa)) {
                    return back()->with('error', 'HTA / GPA belum diisi. Proses tidak dapat diteruskan.');
                }

                $approvalHTA = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $data->getHtaGpa->JenisForm)
                    ->where('DokumenId', $data->getHtaGpa->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedHTA = $approvalHTA->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                // 2. Cek FUI (Usulan Investasi) - Minimal harus diapprove oleh urutan 1
                if (empty($cekFui)) {
                    return back()->with('error', 'FUI belum diisi. Proses tidak dapat diteruskan.');
                }

                $approvalFUI = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $cekFui->JenisForm)
                    ->where('DokumenId', $cekFui->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $approverUrutan1 = $approvalFUI->firstWhere('Urutan', 1);

                // Hanya cek urutan 1 saja
                if (!$approverUrutan1 || $approverUrutan1->Status !== 'Approved') {
                    $name = $approverUrutan1 && $approverUrutan1->getUser ? $approverUrutan1->getUser->name : 'Pengguna terkait';
                    $errors[] = "Persetujuan Usulan Investasi (FUI) urutan 1 atas nama <b>{$name}</b> belum approve.";
                }




                // 3. Cek Approval HTA / GPA
                if (!$semuaApprovedHTA) {
                    $notApprovedHta = $approvalHTA->filter(function ($item) {
                        return $item->Status !== 'Approved';
                    })->values();

                    if ($notApprovedHta->count() > 0) {
                        $list = $notApprovedHta->map(function ($item, $idx) {
                            $name = $item->getUser->name ?? 'Pengguna terkait';
                            return ($idx + 1) . ". {$name}";
                        })->implode('<br>');
                        $errors[] = "Daftar Nama Belum Approve HTA / GPA:<br>{$list}";
                    } else {
                        $errors[] = 'Dokumen <b>HTA / GPA</b> belum diapprove.';
                    }
                }

                // ✅ Disposisi: TIDAK dicek sama sekali (dihapus)

                if (count($errors) > 0) {
                    $message = '<b>Mohon lengkapi persetujuan berikut sebelum lanjut:</b><br>' . implode('<br><br>', $errors);
                    return back()->with('error', $message);
                }
            }

            // Log aktivitas
            AktivitasPengajuan::create([
                'KodePengajuan' => $data->KodePengajuan,
                'Jenis' => 'Pengajuan Pembelian',
                'Keterangan' => 'Pengajuan pembelian dengan nomor ' . $data->KodePengajuan . ' telah berstatus Siap Presentasi',
                'UserCreate' => auth()->user()->name,
            ]);
        }
        // dd(23123);
        if (empty($data->getHtaGpa)) {
            return back()->with('error', 'HTA / GPA belum diisi. Proses tidak dapat diteruskan.');
        }
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->getHtaGpa->JenisForm)
            ->where('DokumenId', $data->getHtaGpa->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        // Ignore UserId 2 in approval checks
        $semuaApproved = $approval->filter(function ($item) {
            return $item->UserId != 2;
        })->every(function ($item) {
            return $item->Status === 'Approved';
        });


        if ($semuaApproved) {
            $countVendor = $data->getVendor;
            $namaUser = auth()->user()->name ?? 'User';
            if (count($countVendor) < 2) {
                return redirect()->back()->with('error', "Hai $namaUser, pengajuan tidak bisa diajukan ke CCP. Minimal harus ada 2 vendor dan maksimal 3 vendor ya.");
            }
            if (count($countVendor) > 3) {
                return redirect()->back()->with('error', "Hai $namaUser, pengajuan tidak bisa diajukan ke CCP. Maksimal hanya boleh 3 vendor ya.");
            }
            $pengajuan = PengajuanPembelian::findOrFail($id);
            $pengajuan->Status = $request->Status;
            $pengajuan->Keterangan = $request->Keterangan ?? '';
            $pengajuan->DiajukanOleh = auth()->user()->id;
            if ($request->Status == 'Siap Presentasi') {
                $pengajuan->DiajukanPadaRekomendasi = now();
            } else {
                $pengajuan->DiajukanPada = now();
            }

            $pengajuan->save();

            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user()->id)
                    ->withProperties(['ip' => request()->ip()])
                    ->log('Mengajukan pengajuan pembelian ke CCP: ' . $pengajuan->KodePengajuan);
            }

            $message = '';
            if ($request->Status == 'Diajukan') {
                AktivitasPengajuan::create([
                    'KodePengajuan' => $data->KodePengajuan,
                    'Jenis' => 'Pengajuan Pembelian',
                    'Keterangan' => 'Pengajuan pembelian dengan nomor ' . $data->KodePengajuan . ' telah diajukan ke CCP',
                    'UserCreate' => auth()->user()->name,
                ]);
                $message = 'Terimakasih ' . auth()->user()->name . ', pengajuan Anda berhasil diajukan ke CCP.';
            } elseif ($request->Status == 'Draft') {
                AktivitasPengajuan::create([
                    'KodePengajuan' => $data->KodePengajuan,
                    'Jenis' => 'Pengajuan Pembelian',
                    'Keterangan' => 'Pengajuan pembelian dengan nomor ' . $data->KodePengajuan . ' telah dikembalikan ke draft',
                    'UserCreate' => auth()->user()->name,
                ]);
                $message = 'Pengajuan berhasil dikembalikan ke draft.';
            } else {
                AktivitasPengajuan::create([
                    'KodePengajuan' => $data->KodePengajuan,
                    'Jenis' => 'Pengajuan Pembelian',
                    'Keterangan' => 'Status pengajuan pembelian dengan nomor ' . $data->KodePengajuan . ' telah diperbarui',
                    'UserCreate' => auth()->user()->name,
                ]);
                $message = 'Status pengajuan berhasil diperbarui.';
            }
            AktivitasPengajuan::create([
                'KodePengajuan' => $data->KodePengajuan,
                'Jenis' => 'Pengajuan Pembelian',
                'Keterangan' => 'Pengajuan pembelian dengan nomor ' . $data->KodePengajuan . ' telah masuk ke status "Siap Presentasi"',
                'UserCreate' => auth()->user()->name,
            ]);
            return redirect()
                ->route('ajukan.show', encrypt($id))
                ->with('success', $message);
        } else {
            return back()->with('error', 'HTA / GPA, Belum Disetujui. Proses Tidak Dapat Diteruskan.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $pengajuan = PengajuanPembelian::with('getVendor', 'getVendorDetail')->find($id);
        if (!$pengajuan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        foreach ($pengajuan->getVendor as $vendor) {
            $vendor->delete();
        }

        foreach ($pengajuan->getVendorDetail as $vendorDetail) {
            $vendorDetail->delete();
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master pengajuan: ' . $pengajuan->Nama);
        }

        $pengajuan->delete();

        return response()->json(['status' => 200, 'message' => 'Master perusahaan berhasil dihapus']);
    }
    public function mintaTtdDirGroup($id)
    {
        $id = decrypt($id);
        // dd($id);
        $data = PengajuanPembelian::with([
            'getHtaGpa',
            'getFUI',
            'getDisposisi',
            'getFS',
        ])->find($id);
        $approval = [];

        // Untuk HtaGpa
        $approval['HtaGpa'] = collect();
        if ($data && $data->getHtaGpa) {
            $approval['HtaGpa'] = DokumenApproval::where('JenisFormId', $data->getHtaGpa->JenisForm ?? null)
                ->where('DokumenId', $data->getHtaGpa->id ?? null)
                ->where('UserId', 2)
                // ->where('Status', 'Pending')
                ->get();
        }
        //Untuk FUI
        $approval['Fui'] = collect();
        if ($data && $data->getFUI) {
            $approval['Fui'] = DokumenApproval::where('JenisFormId', $data->getFUI->JenisForm ?? null)
                ->where('DokumenId', $data->getFUI->id ?? null)
                ->where('UserId', 2)
                // ->where('Status', 'Pending')
                ->get();
        }
        // Untuk Disposisi
        $approval['Disposisi'] = collect();
        if ($data && $data->getDisposisi) {
            $approval['Disposisi'] = DokumenApproval::where('JenisFormId', $data->getDisposisi->JenisForm ?? null)
                ->where('DokumenId', $data->getDisposisi->id ?? null)
                ->where('UserId', 2)
                // ->where('Status', 'Pending')
                ->get();
        }

        // Untuk FS
        $approval['Fs'] = collect();
        if ($data && $data->getFS) {
            $approval['Fs'] = DokumenApproval::where('JenisFormId', $data->getFS->JenisForm ?? null)
                ->where('DokumenId', $data->getFS->id ?? null)
                ->where('UserId', 2)
                // ->where('Status', 'Pending')
                ->get();
        }
        // dd($approval);

        // Cek apakah semua approval sudah "Approved"
        $semuaApprove = true;
        foreach ($approval as $key => $items) {
            if ($items->isNotEmpty()) {
                foreach ($items as $item) {
                    if ($item->Status !== 'Approved') {
                        $semuaApprove = false;
                        break 2;
                    }
                }
            }
        }
        // dd($semuaApprove);
        return view('form.pengajuan-pembelian.cek-tanda-tangan-direktur-group', compact('approval', 'data', 'semuaApprove'));
    }
    public function KirimApprovalDirekturGroup(Request $request, $id)
    {
        try {
            // ========== 1. VALIDASI DATA UTAMA ==========
            $id = decrypt($id);
            $rekomendasi = PengajuanPembelian::find($id);

            if (!$rekomendasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Rekomendasi tidak ditemukan.',
                ], 404);
            }

            $cariDispo = LembarDisposisi::where('IdPengajuan', $rekomendasi->id)->first();
            if (!$cariDispo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lembar disposisi tidak ditemukan.',
                ], 404);
            }

            // ========== 2. APPROVAL DISPOSISI ==========
            $jenisDispo = $rekomendasi->Jenis == '1' ? 9 : 10;
            $approvalDispo = DokumenApproval::where('JenisFormId', $jenisDispo)
                ->where('DokumenId', $cariDispo->id)
                ->where('UserId', 2)
                ->where('Status', 'Pending')
                ->first();

            if (!$approvalDispo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Approval Disposisi tidak ditemukan atau telah disetujui.',
                ], 400);
            }

            // ========== 3. APPROVAL FUI (OPTIONAL) ==========
            $fui = UsulanInvestasi::where('IdPengajuan', $rekomendasi->id)->first();
            $approvalFui = null;

            if ($fui && !empty($fui->BiayaAkhir)) {
                $jenisFormFui = $this->getJenisFormFui($rekomendasi->Jenis, $fui->BiayaAkhir);

                if ($jenisFormFui) {
                    $approvalFui = DokumenApproval::where('JenisFormId', $jenisFormFui)
                        ->where('DokumenId', $fui->id)
                        ->where('UserId', 2)
                        ->where('Status', 'Pending')
                        ->first();

                    // Fallback ke JenisForm asli jika berbeda
                    if (!$approvalFui && $jenisFormFui != $fui->JenisForm) {
                        $approvalFui = DokumenApproval::where('JenisFormId', $fui->JenisForm)
                            ->where('DokumenId', $fui->id)
                            ->where('UserId', 2)
                            ->where('Status', 'Pending')
                            ->first();
                    }
                }
            }

            // ========== 4. DATA USER APPROVER ==========
            $user = User::find(2);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User approver (Direktur Group) tidak ditemukan.',
                ], 404);
            }

            // ========== 5. PERSIAPAN DATA LAMPIRAN & QR CODE ==========
            $cekdata = Rekomendasi::where('IdPengajuan', $rekomendasi->id)->first();
            $idPengajuan = $cekdata->IdPengajuan;
            $idPengajuanItem = $cekdata->PengajuanItemId;

            $rekomendasiLampiran = $this->prepareRekomendasiLampiran($cekdata->PengajuanItemId);
            $lembarDisposisi = $this->prepareLembarDisposisi($idPengajuan, $idPengajuanItem);
            $dataDisposisi = $this->prepareDataDisposisi($lembarDisposisi);

            $dataHta = $this->prepareDataHta($idPengajuan, $idPengajuanItem);
            $approvalHta = $this->prepareApprovalWithQR($dataHta->getHtaGpa ?? null);

            $parameter = MasterParameter::get();

            $usulan = $this->prepareDataFui($idPengajuan, $idPengajuanItem);
            $VendorAcc = $this->prepareVendorAcc($idPengajuanItem);
            $approvalFuiList = $this->prepareApprovalWithQR($usulan);
            $data2 = $this->prepareData2($idPengajuan, $VendorAcc);

            $datafs = $this->prepareDataFs($idPengajuan, $idPengajuanItem);
            $approvalFsList = $this->prepareApprovalWithQR($datafs);

            $permintaan = PermintaanPembelian::with([
                'getDetail.getBarang.getMerk',
                'getDiajukanOleh',
                'getDetail.getBarang.getSatuan'
            ])->find($rekomendasi->IdPermintaan);
            $approvalPermintaan = $this->prepareApprovalWithQR($permintaan, 80);

            // ========== 6. KIRIM EMAIL ==========
            Mail::to($user->email)->send(new NotifikasiApprovalDirekturGroupMail(
                $rekomendasi,
                $cariDispo,
                $fui,
                $user,
                $approvalDispo,
                $approvalFui,
                $rekomendasiLampiran,
                $dataDisposisi,
                $data2,
                $usulan,
                $approvalFuiList,
                $VendorAcc,
                $approvalFuiList,
                $permintaan,
                $approvalPermintaan,
                $dataHta,
                $approvalHta,
                $parameter,
                $datafs,
                $approvalFsList,
                $this->prepareDataRekom($idPengajuan)
            ));

            // ========== 7. LOG AKTIVITAS ==========
            $cariPengajuan = PengajuanPembelian::find($rekomendasi->IdPengajuan ?? null);
            $kodePengajuan = $cariPengajuan?->KodePengajuan ?? $rekomendasi->KodePengajuan ?? null;

            AktivitasPengajuan::create([
                'KodePengajuan' => $kodePengajuan,
                'Jenis' => 'Rekomendasi',
                'Keterangan' => "Approval Direktur Group untuk pengajuan {$kodePengajuan} - Email notifikasi berhasil dikirim",
                'UserCreate' => auth()->user()->name ?? 'system',
            ]);

            // ========== 8. RETURN SUCCESS JSON ==========
            return response()->json([
                'status' => 'success',
                'message' => 'Email notifikasi approval berhasil dikirim ke Direktur Group.',
                'data' => [
                    'kode_pengajuan' => $kodePengajuan,
                    'email_penerima' => $user->email,
                    'nama_penerima' => $user->name,
                ]
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging (opsional: simpan ke file/log system)
            \Log::error('KirimApprovalDirekturGroup Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_id' => $id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function approvalDirekturGroup($id)
    {
        // dd($id);
        $id = decrypt($id);
        $dokumenLists = [];
        $pengajuan = PengajuanPembelian::with(['getHtaGpa', 'getFUI', 'getDisposisi', 'getFS'])->find($id);

        if ($pengajuan->getHtaGpa) {
            if ($pengajuan->getHtaGpa instanceof Collection) {
                foreach ($pengajuan->getHtaGpa as $hta) {
                    $dokumenLists[] = [
                        'JenisFormId' => $hta->JenisForm,
                        'DokumenId' => $hta->id,
                    ];
                }
            } else {
                $dokumenLists[] = [
                    'JenisFormId' => $pengajuan->getHtaGpa->JenisForm,
                    'DokumenId' => $pengajuan->getHtaGpa->id,
                ];
            }
        }
        // FUI
        if ($pengajuan->getFUI) {
            if ($pengajuan->getFUI instanceof Collection) {
                foreach ($pengajuan->getFUI as $fui) {
                    $dokumenLists[] = [
                        'JenisFormId' => $fui->JenisForm,
                        'DokumenId' => $fui->id,
                    ];
                }
            } else {
                $dokumenLists[] = [
                    'JenisFormId' => $pengajuan->getFUI->JenisForm,
                    'DokumenId' => $pengajuan->getFUI->id,
                ];
            }
        }
        // Disposisi
        if ($pengajuan->getDisposisi) {
            if ($pengajuan->getDisposisi instanceof Collection) {
                foreach ($pengajuan->getDisposisi as $disposisi) {
                    $dokumenLists[] = [
                        'JenisFormId' => $disposisi->JenisForm,
                        'DokumenId' => $disposisi->id,
                    ];
                }
            } else {
                $dokumenLists[] = [
                    'JenisFormId' => $pengajuan->getDisposisi->JenisForm,
                    'DokumenId' => $pengajuan->getDisposisi->id,
                ];
            }
        }
        // FS
        if ($pengajuan->getFS) {
            if ($pengajuan->getFS instanceof Collection) {
                foreach ($pengajuan->getFS as $fs) {
                    $dokumenLists[] = [
                        'JenisFormId' => $fs->JenisForm,
                        'DokumenId' => $fs->id,
                    ];
                }
            } else {
                $dokumenLists[] = [
                    'JenisFormId' => $pengajuan->getFS->JenisForm,
                    'DokumenId' => $pengajuan->getFS->id,
                ];
            }
        }

        $updated = 0;
        // dd($dokumenLists);
        foreach ($dokumenLists as $item) {
            // Jika memungkinkan banyak approval dalam satu dokumen, lebih baik gunakan ->get() dan foreach di dalamnya
            $approvals = DokumenApproval::where('JenisFormId', $item['JenisFormId'])
                ->where('DokumenId', $item['DokumenId'])
                ->where('UserId', 2)
                // ->where('Status', 'Pending')
                ->get();
            // dd($approvals);
            foreach ($approvals as $approval) {
                $approval->Status = 'Approved';
                $approval->TanggalApprove = now();
                $approval->save();
                $updated++;
            }
        }
        // Activity log untuk aksi persetujuan bulk approval direktur group
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Bulk approval Direktur Group: Semua dokumen berhasil diapprove oleh Direktur Group');
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $pengajuan->KodePengajuan ?? null,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => "Pengajuan {$pengajuan->KodePengajuan} telah disetujui oleh dr. Widya Putri, MARS.",
            'UserCreate' => 'dr. Widya Putri, MARS',
        ]);


        return view('emails.setelah-approval-direktur-group')->with('success', 'Data approval berhasil diupdate.');

    }
    // ========== HELPER METHODS ==========

    private function getJenisFormFui($jenisRekomendasi, $biayaAkhir)
    {
        if ($jenisRekomendasi == 1) { // Medis
            if ($biayaAkhir < 50000000)
                return '7';
            if ($biayaAkhir <= 100000000)
                return '11';
            return '12';
        } else { // Umum
            if ($biayaAkhir < 50000000)
                return '14';
            if ($biayaAkhir <= 100000000)
                return '15';
            return '13';
        }
    }

    private function prepareRekomendasiLampiran($pengajuanItemId)
    {
        $rekomendasiLampiran = Rekomendasi::with([
            'getRekomedasiDetail.getPerusahaan',
            'getRekomedasiDetail.getBarang',
            'getRekomedasiDetail.getNegara'
        ])->where('PengajuanItemId', $pengajuanItemId)->first();

        // Generate QR Code Nego
        if ($rekomendasiLampiran?->UserNego) {
            $rekomendasiLampiran->qrCodeNego = $this->generateQrCodeBase64($rekomendasiLampiran->id);
        }
        // Generate QR Code Approve
        if ($rekomendasiLampiran?->DisetujuiOleh) {
            $rekomendasiLampiran->qrCodeApprove = $this->generateQrCodeBase64($rekomendasiLampiran->id);
        }

        return $rekomendasiLampiran;
    }

    private function prepareLembarDisposisi($idPengajuan, $idPengajuanItem)
    {
        return LembarDisposisi::with(['getDetail', 'getBarang'])
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();
    }

    private function prepareDataDisposisi($lembarDisposisi)
    {
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $lembarDisposisi->JenisForm)
            ->where('DokumenId', $lembarDisposisi->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        // Generate QR untuk approval yang sudah Approved
        foreach ($approval as $item) {
            if ($item->Status == 'Approved') {
                $item->qrCode = $this->generateQrCodeBase64(route('approval.validasi', $item->ApprovalToken));
            }
        }

        return [
            'lembarDisposisi' => $lembarDisposisi,
            'namaBarang' => $lembarDisposisi->getBarang->Nama ?? '-',
            'harga' => $lembarDisposisi->Harga,
            'rencanaVendor' => $lembarDisposisi->getVendor->Nama ?? '-',
            'tujuanPenempatan' => $lembarDisposisi->TujuanPenempatan,
            'formPermintaan' => $lembarDisposisi->FormPermintaanUser,
            'approval' => $approval,
        ];
    }

    private function prepareDataHta($idPengajuan, $idPengajuanItem)
    {
        return PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getHtaGpa.getDetailHta' => fn($q) => $q->where('PengajuanItemId', $idPengajuanItem),
            'getVendor.getHtaGpa' => fn($q) => $q->where('PengajuanItemId', $idPengajuanItem),
            'getJenisPermintaan.getForm',
            'getHtaGpa.getPenilai1',
            'getHtaGpa.getPenilai2',
            'getHtaGpa.getPenilai3',
            'getHtaGpa.getPenilai4',
            'getHtaGpa.getPenilai5',
            'getHtaGpa.getPenilai',
            'getPengajuanItem' => fn($q) => $q->where('id', $idPengajuanItem)->with('getBarang.getMerk')
        ])->find($idPengajuan);
    }

    private function prepareApprovalWithQR($model, $qrSize = 300)
    {
        if (!$model)
            return collect();

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $model->JenisForm ?? null)
            ->where('DokumenId', $model->id ?? null)
            ->orderBy('Urutan', 'asc')
            ->get();

        foreach ($approval as $item) {
            if ($item->Status == 'Approved' && $item->ApprovalToken) {
                $item->qrCode = $this->generateQrCodeBase64(
                    route('approval.validasi', $item->ApprovalToken),
                    $qrSize
                );
            }
        }

        return $approval;
    }

    private function prepareDataFui($idPengajuan, $idPengajuanItem)
    {
        return UsulanInvestasi::with([
            'getFuiDetail.getVendor',
            'getBarang',
            'getVendor',
            'getAccDirektur',
            'getAccKadiv',
            'getDepartemen',
            'getDepartemen2',
            'getNamaForm'
        ])->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();
    }

    private function prepareVendorAcc($idPengajuanItem)
    {
        return Rekomendasi::with([
            'getRekomedasiDetail' => fn($q) => $q->where('Rekomendasi', 1),
            'getRekomedasiDetail.getNamaVendor'
        ])->where('PengajuanItemId', $idPengajuanItem)->first();
    }

    private function prepareData2($idPengajuan, $VendorAcc)
    {
        $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor ?? null;
        $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan ?? null;

        return PengajuanPembelian::with([
            'getVendor' => fn($q) => $Acc ? $q->where('NamaVendor', $Acc) : $q,
            'getVendor.getVendorDetail' => fn($q) => $NamaBarangAcc ? $q->where('NamaBarang', $NamaBarangAcc) : $q,
            'getRekomendasi' => fn($q) => $q->with([
                'getRekomedasiDetail' => fn($q2) => $q2->where('Rekomendasi', 1)
            ])
        ])->find($idPengajuan);
    }

    private function prepareDataFs($idPengajuan, $idPengajuanItem)
    {
        return FeasibilityStudy::with('getFsDetail', 'getBarang')
            ->where('IdPengajuan', $idPengajuan)
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();
    }

    private function prepareDataRekom($idPengajuan)
    {
        return Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')
            ->where('IdPengajuan', $idPengajuan)->first();
    }

    private function generateQrCodeBase64($content, $size = 300, $margin = 10)
    {
        try {
            $qrCode = QrCode::create($content)->setSize($size)->setMargin($margin);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            return base64_encode($result->getString());
        } catch (\Exception $e) {
            \Log::warning('QR Code generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
