@extends('layouts.app')

@section('content')
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

                    @php
                        // Array warna background pastel untuk tiap vendor
                        $vendorColors = [
                            '#e3f2fd', // Biru muda
                            '#e8f5e9', // Hijau muda
                            '#fff3e0', // Oranye muda
                            '#f3e5f5', // Ungu muda
                            '#fff9c4', // Kuning muda
                            '#fce4ec', // Pink muda
                            '#e0f7fa', // Cyan muda
                        ];
                    @endphp
                    <div class="table-responsive">
                        <table class="table align-middle table-bordered" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width: 5%;">No
                                    </th>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width: 15%;">
                                        Parameter Penilaian</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" colspan="2"
                                            style="width: {{ 80 / count($data->getVendor) }}%;">
                                            {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" style="width: calc(35 / {{ count($data->getVendor) }}%);">
                                            Deskripsi</th>
                                        <th class="text-center"
                                            style="width: calc(10 / {{ count($data->getVendor) }}%); min-width:60px; max-width: 90px;">
                                            Subtotal
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->getJenisPermintaan->getForm->Parameter as $key => $pm)
                                    <tr>
                                        <td class="text-center" style="background:#f8f9fa;">{{ $key + 1 }}</td>
                                        <td style="background:#f8f9fa;">
                                            {{ $parameter[$pm - 1]->Nama ?? '-' }}
                                        </td>
                                        @foreach ($data->getVendor as $vIdx => $Vendor)
                                            <td
                                                style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                                {!! $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key] ?? '' !!}
                                            </td>
                                            <td
                                                style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width: 90px; width:1%;">
                                                <input type="text"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}"
                                                    class="form-control bg-transparent border-0" readonly
                                                    style="font-weight:bold; text-align:center; min-width:60px; max-width:90px;">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                {{-- Grand Total --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Grand Total</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
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
                                                $grandTotalShow =
                                                    $grandTotal > 0
                                                        ? $grandTotal
                                                        : $data->getHtaGpa->getDetailHta[$vIdx]->GrandTotal ?? '';
                                            @endphp
                                            <input type="text" value="{{ $grandTotalShow }}"
                                                class="form-control bg-transparent border-0" readonly
                                                style="font-weight:bold; text-align:center; min-width:60px; max-width:90px;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Umur Ekonomis --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Umur Ekonomis</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->UmurEkonomis ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Tarif Diusulkan --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Tarif Diusulkan</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Buyback Period --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Buyback Period</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->BuybackPeriod ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Target Pemakaian Bulanan --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Target Pemakaian Bulanan</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TargetPemakaianBulanan ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Keterangan --}}
                                <tr>
                                    <th class="text-end align-top" colspan="2"
                                        style="vertical-align: top; background:#f8f9fa;">Keterangan</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <textarea class="form-control bg-transparent border-0" rows="6" readonly style="resize: vertical;">{!! $data->getHtaGpa->getDetailHta[$vIdx]->Keterangan ?? '-' !!}</textarea>
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
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
                                                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}"
                                                            alt="QR Code" style="width:80px; height:80px;"><br>
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
    {{-- Modal Justifikasi Persetujuan --}}
    <div class="modal fade" id="modalJustifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-check-circle me-2"></i>Konfirmasi Persetujuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formApprove" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="justifikasi" class="form-label fw-bold">
                                Justifikasi Pembelian Alat <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('justifikasi') is-invalid @enderror" id="justifikasi" name="justifikasi"
                                rows="4" placeholder="Contoh: Spesifikasi alat sesuai kebutuhan, harga kompetitif, vendor terpercaya, dll."
                                required></textarea>
                            @error('justifikasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Justifikasi akan tercatat dalam history persetujuan.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fa fa-paper-plane me-1"></i>Kirim Persetujuan
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
@endpush
