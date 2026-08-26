@extends('layouts.app')

@section('content')
@push('css')
    <style>
    #modalJustifikasi .modal-header {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        color: #fff;
        border-radius: .5rem .5rem 0 0;
        padding: 1rem 1.5rem;
    }
    #modalJustifikasi .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: .8;
    }
    #modalJustifikasi .modal-header .btn-close:hover {
        opacity: 1;
    }
    #modalJustifikasi .modal-body {
        padding: 1.75rem 1.75rem 1rem;
    }
    #modalJustifikasi .status-card {
        flex: 1;
        border: 2px solid #e3e6f0;
        border-radius: .6rem;
        padding: 1rem 1.25rem;
        cursor: pointer;
        transition: all .2s ease;
        background: #fff;
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    #modalJustifikasi .status-card:hover {
        border-color: #a6b8f0;
        background: #f8f9ff;
    }
    #modalJustifikasi .status-card.active-approved {
        border-color: #1cc88a;
        background: #eafaf3;
        box-shadow: 0 0 0 3px rgba(28, 200, 138, .15);
    }
    #modalJustifikasi .status-card.active-rejected {
        border-color: #e74a3b;
        background: #fdecea;
        box-shadow: 0 0 0 3px rgba(231, 74, 59, .15);
    }
    #modalJustifikasi .status-card .status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    #modalJustifikasi .status-card.approved .status-icon {
        background: #d4f5e6;
        color: #1cc88a;
    }
    #modalJustifikasi .status-card.rejected .status-icon {
        background: #fadbd8;
        color: #e74a3b;
    }
    #modalJustifikasi .status-card .status-title {
        font-weight: 600;
        margin: 0;
        font-size: .95rem;
    }
    #modalJustifikasi .status-card .status-desc {
        font-size: .78rem;
        color: #6c757d;
        margin: 0;
    }
    #modalJustifikasi .form-label.fw-bold {
        color: #3a3b45;
        margin-bottom: .6rem;
    }
    #modalJustifikasi textarea.form-control {
        border-radius: .5rem;
        border: 1.5px solid #d1d3e2;
        padding: .75rem 1rem;
        transition: border-color .2s, box-shadow .2s;
    }
    #modalJustifikasi textarea.form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, .15);
    }
    #modalJustifikasi .info-box {
        background: #f8f9fc;
        border-left: 3px solid #4e73df;
        padding: .6rem .9rem;
        border-radius: .35rem;
        font-size: .82rem;
        color: #5a5c69;
        margin-top: .5rem;
    }
    #modalJustifikasi .modal-footer {
        padding: 1rem 1.75rem 1.5rem;
    }
</style>
@endpush
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Detail Penilaian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Hta / Gpa</a></li>
                    <li class="breadcrumb-item active">Detail Penilaian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Penilaian HTA / GPA
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">Informasi Barang</h5>
                        <table class="table align-middle" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:180px;">Nama Barang</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Tipe ?? '-' }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width:40px;">No
                                    </th>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width:180px;">
                                        Parameter Penilaian</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" colspan="7" style="min-width:250px;">
                                            {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" style="width:120px;">Deskripsi</th>
                                        <th class="text-center" style="width:70px;">Nilai 1</th>
                                        <th class="text-center" style="width:70px;">Nilai 2</th>
                                        <th class="text-center" style="width:70px;">Nilai 3</th>
                                        <th class="text-center" style="width:70px;">Nilai 4</th>
                                        <th class="text-center" style="width:70px;">Nilai 5</th>
                                        <th class="text-center" style="width:90px;">Subtotal</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->getJenisPermintaan->getForm->Parameter as $key => $pm)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td>
                                            {{ $parameter[$pm - 1]->Nama ?? '-' }}
                                        </td>
                                        @foreach ($data->getVendor as $vIdx => $Vendor)
                                            <td>
                                                {!! $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key] ?? '' !!}
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai1[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai2[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai3[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai4[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai5[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}"
                                                    class="form-control bg-light" readonly style="font-weight:bold;">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Grand Total</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="6"></th>
                                        <th>
                                            @php
                                                $grandTotal = 0;
                                                if (
                                                    isset($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal) &&
                                                    is_array($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal)
                                                ) {
                                                    foreach (
                                                        $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal
                                                        as $subtotal
                                                    ) {
                                                        $grandTotal += is_numeric($subtotal) ? floatval($subtotal) : 0;
                                                    }
                                                }
                                                // Fallback ke GrandTotal lama jika kolom tetap mau muncul
                                                $grandTotalShow =
                                                    $grandTotal > 0
                                                        ? $grandTotal
                                                        : $data->getHtaGpa->getDetailHta[$vIdx]->GrandTotal ?? '';
                                            @endphp
                                            <input type="text" value="{{ $grandTotalShow }}"
                                                class="form-control bg-light" readonly style="font-weight:bold;">
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Umur Ekonomis
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"> <input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->UmurEkonomis ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Tarif Diusulkan
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"> <input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Buyback Period
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"><input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->BuybackPeriod ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Target Pemakaian
                                        Bulanan</th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"> <input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TargetPemakaianBulanan ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end align-top" colspan="2" style="vertical-align: top;">Keterangan
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6">
                                            <textarea class="form-control bg-light" rows="6" readonly style="resize: vertical;">{!! $data->getHtaGpa->getDetailHta[$vIdx]->Keterangan ?? '-' !!}</textarea>
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan HTA / GPA</strong></h5>
                            <div class="mb-2 text-center">
                                @if (!empty($approval))
                                    <div class="row justify-content-center">
                                        @foreach ($approval as $item)
                                            <div class="col text-center" style="font-weight:600;">
                                                {{ $item->NamaJabatan ?? '-' }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless" style="max-width:100%; margin: 0 auto;">
                                    <colgroup>
                                        @if (!empty($approval))
                                            @foreach ($approval as $item)
                                                <col style="width: {{ 100 / count($approval) }}%;">
                                            @endforeach
                                        @endif
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="height:80px; vertical-align: top;">
                                                    @if ($item->Status == 'Approved' && !empty($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}"
                                                            alt="QR Code" style="width:80px; height:80px;"><br>
                                                    @elseif ($item->Status == 'Approved')
                                                        <span class="text-danger" style="font-size:11px;">QR code tidak tersedia</span>
                                                    @else
                                                        <span style="font-size:11px;">{{ $item->Status ?? '-' }}</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                        </tr>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="padding-bottom:0;">
                                                    <hr
                                                        style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center align-top">
                                                    <span style="font-weight:600;">
                                                        {{ $item->Nama ?? '-' }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $item->Status ?? '-' }}</small><br>
                                                    <small><em>
                                                            {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                                        </em></small>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            @foreach ($approval as $item)
                                @if (auth()->id() == ($item->UserId ?? null) && $item->Status != 'Approved' && !empty($item->ApprovalToken))
                                    <button type="button" class="btn btn-approve me-2"
                                        style="background-color: #28a745; color: #fff; border-color: #28a745;"
                                        data-bs-toggle="modal" data-bs-target="#modalJustifikasi"
                                        data-approval-token="{{ $item->ApprovalToken }}"
                                        data-approval-route="{{ route('htagpa.submitJustifikasi', $item->ApprovalToken) }}"
                                        data-jabatan="{{ $item->NamaJabatan ?? ($item->getJabatan->Nama ?? $item->JenisUser) }}"
                                        data-nama="{{ $item->Nama ?? 'Penilai' }}">
                                        <i class="fa fa-check"></i>
                                        Setujui
                                    </button>
                                @endif
                            @endforeach
                        </div>
                        @if (isset($approval) && count($approval) > 0)
                            <div class="mt-4">
                                <dl>
                                    @php $nomor = 1; @endphp
                                    @foreach ($approval as $item)
                                        @if (!empty($item->Justifikasi))
                                            <dt>
                                                <strong>{{ $nomor++ }}.
                                                    Justifikasi{{ $item->Nama ? ' oleh ' . $item->Nama : '' }}:</strong>
                                            </dt>
                                            <dd class="text-muted" style="margin-bottom:10px;">
                                                {{ $item->Justifikasi }}
                                            </dd>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
          @if (!empty($htagpa))
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Status Approval</h5>
                            {{-- @if (!empty($approval) && count($approval) > 0)
                                <a href="{{ route('htagpa.kirim-ulang-notifikasi', $htagpa->id) }}"
                                    class="btn btn-primary" id="btnKonfirmasiKirimUlang">
                                    <i class="fa fa-paper-plane me-1"></i> Kirim Ulang Notifikasi
                                </a>
                            @endif --}}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="card mb-3 border-info">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold text-info mb-2">
                                            Informasi Penggunaan Link Approval
                                        </h6>
                                        <p class="card-text mb-0">
                                            Anda juga dapat menyalin link approval agar dapat langsung dibagikan melalui
                                            WhatsApp atau media lainnya, sehingga penilai yang belum merespon dapat segera
                                            mengambil tindakan.<br>

                                        </p>
                                    </div>
                                </div>


                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Urutan</th>
                                            <th>Status</th>
                                            <th>Status Email</th>
                                            <th>TanggalApprove</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($approval && count($approval) > 0)
                                            @php
                                                $canCopy = true;
                                            @endphp
                                            @foreach ($approval as $key => $item)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $item->Nama }}</td>
                                                    <td>{{ $item->Email }}</td>
                                                    <td>{{ $item->Urutan }}</td>
                                                    <td>
                                                        <span
                                                            class="badge
                                                            @if ($item->Status == 'Approved') bg-success
                                                            @elseif($item->Status == 'Pending') bg-warning text-dark
                                                            @elseif($item->Status == 'Rejected') bg-danger
                                                            @else bg-secondary @endif">
                                                            {{ $item->Status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $item->StatusEmail }}</td>
                                                    <td>
                                                        {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                                    </td>
                                                     <td>
                                                        @php
                                                            $approvalUrl = route('htagpa.sebelum-approve', $item->ApprovalToken ?? '');
                                                            $templateText = "Yth. Bapak/Ibu {$item->Nama},\n\nMohon untuk melakukan approval Formulir HTA/GPA pada link berikut:\n{$approvalUrl}\n\nTerima kasih.";
                                                        @endphp

                                                        @if ($canCopy)
                                                            @if ($item->Status !== 'Approved')
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm"
                                                                    onclick="navigator.clipboard.writeText(`{{ $templateText }}`); Swal.fire('Disalin!','Template link approval beserta kata-kata telah disalin ke clipboard!','success')">
                                                                    <i class="fa fa-copy"></i> Salin Link Approval
                                                                </button>
                                                                @if ($item->UserId == 81)
                                                                    <div><span class="text-muted">Notifikasi Akan dikirimkan oleh ccp setelah presentasi</span></div>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">Sudah disetujui - tidak bisa salin lagi</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Approval sebelumnya harus disetujui</span>
                                                        @endif
                                                    </td>

                                                </tr>
                                                @if ($item->Status !== 'Approved')
                                                    @php
                                                        $canCopy = false;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="17" class="text-center">Belum ada data approval.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>


                            </div>

                        </div>
                    </div>
                </div>
            @endif
            <div class="modal fade" id="modalJustifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="fa fa-check-circle me-2"></i>
                    Konfirmasi Persetujuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formApprove" method="POST">
                @csrf
                <div class="modal-body">

                    {{-- Pilihan Status --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Pilihan Status <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3">
                            <label class="status-card approved" for="statusApproved">
                                <input class="d-none" type="radio" name="Status" id="statusApproved" value="Approved" required>
                                <div class="status-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div>
                                    <p class="status-title">Approved</p>
                                    <p class="status-desc">Setujui permintaan pembelian ini</p>
                                </div>
                            </label>

                            <label class="status-card rejected" for="statusRejected">
                                <input class="d-none" type="radio" name="Status" id="statusRejected" value="Rejected" required>
                                <div class="status-icon">
                                    <i class="fa fa-times"></i>
                                </div>
                                <div>
                                    <p class="status-title">Rejected</p>
                                    <p class="status-desc">Tolak permintaan pembelian ini</p>
                                </div>
                            </label>
                        </div>
                        @error('Status')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Justifikasi --}}
                    <div class="mb-3">
                        <label for="justifikasi" class="form-label fw-bold">
                            Justifikasi Pembelian Alat <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('justifikasi') is-invalid @enderror"
                                  id="justifikasi" name="justifikasi" rows="5"
                                  placeholder="Contoh: Spesifikasi alat sesuai kebutuhan, harga kompetitif, vendor terpercaya, dll."
                                  required></textarea>
                        @error('justifikasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="info-box">
                            <i class="fa fa-info-circle me-1 text-primary"></i>
                            Justifikasi akan tercatat dalam history persetujuan sebagai audit trail.
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light btn-sm px-4 border" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="fa fa-paper-plane me-1"></i> Kirim Persetujuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
    @if (Session::get('error'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#d33',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#d33',
                });
            }, 500);
        </script>
    @endif
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#28a745',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#28a745',
                });
            }, 500);
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiApprove(idx, penilaiKe) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: 'Apakah Anda yakin ingin menyetujui HTA/GPA ini sebagai Penilai ' + penilaiKe + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + idx).submit();
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalJustifikasi = document.getElementById('modalJustifikasi');
            const formApprove = document.getElementById('formApprove');
            const inputJustifikasi = document.getElementById('justifikasi');
            const modalJabatan = document.getElementById('modalJabatan');

            // Saat modal dibuka, isi data dinamis dari tombol yang diklik
            modalJustifikasi.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                // Ambil data dari data-attributes
                const token = button.getAttribute('data-approval-token');
                const route = button.getAttribute('data-approval-route');
                const jabatan = button.getAttribute('data-jabatan');

                // Set action form & tampilkan info jabatan
                formApprove.action = route;
                modalJabatan.textContent = jabatan;

                // Reset form & error saat modal dibuka
                inputJustifikasi.value = '';
                inputJustifikasi.classList.remove('is-invalid');
                const existingFeedback = inputJustifikasi.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            });

            // Validasi client-side sebelum submit
            formApprove.addEventListener('submit', function(e) {
                const justifikasi = inputJustifikasi.value.trim();

                if (!justifikasi) {
                    e.preventDefault();
                    inputJustifikasi.classList.add('is-invalid');

                    // Tambahkan pesan error jika belum ada
                    if (!inputJustifikasi.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.textContent = 'Justifikasi wajib diisi sebelum menyetujui';
                        inputJustifikasi.parentNode.appendChild(errorDiv);
                    }
                    inputJustifikasi.focus();
                    return false;
                }
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Memproses...';

            });

            // Reset tombol saat modal ditutup
            modalJustifikasi.addEventListener('hidden.bs.modal', function() {
                const submitBtn = formApprove.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Kirim Persetujuan';
            });
        });
    </script>
        <script>
    // Highlight card saat radio dipilih
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('#modalJustifikasi .status-card');
        const radios = document.querySelectorAll('#modalJustifikasi input[name="Status"]');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                cards.forEach(c => c.classList.remove('active-approved', 'active-rejected'));
                const parent = this.closest('.status-card');
                if (this.value === 'Approved') parent.classList.add('active-approved');
                if (this.value === 'Rejected') parent.classList.add('active-rejected');
            });
        });

        // Reset saat modal ditutup
        document.getElementById('modalJustifikasi').addEventListener('hidden.bs.modal', function () {
            cards.forEach(c => c.classList.remove('active-approved', 'active-rejected'));
            document.getElementById('formApprove').reset();
        });
    });
</script>
@endpush
