@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Detail Penilaian HTA</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">HTA</a></li>
                    <li class="breadcrumb-item active">Detail Penilaian HTA</li>
                </ul>
            </div>
        </div>
        <div class="col text-end">
            <a href="javascript:window.history.back();" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informasi Barang</h4>
                </div>
                <div class="card-body pb-2">
                    <div class="row mb-2">
                        <div class="col-md-2 fw-bold">Kode Barang</div>
                        <div class="col-md-4">: {{ $dataBarang?->KodeBarang ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 fw-bold">Nama Barang</div>
                        <div class="col-md-4">: {{ $dataBarang?->Nama ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 fw-bold">Merek</div>
                        <div class="col-md-2">: {{ $dataBarang?->getMerk?->Nama ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        HTA (Health Technology Assessment)
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs tab-style-1 d-sm-flex d-block mb-3" id="vendorTab" role="tablist">
                        @foreach ($data->getListVendorHta as $vnIdx => $listVendorHta)
                            <li class="nav-item">
                                <a class="nav-link @if ($loop->first) active @endif"
                                    id="vendor{{ $vnIdx }}-tab" data-bs-toggle="tab"
                                    data-bs-target="#vendor{{ $vnIdx }}" href="#vendor{{ $vnIdx }}"
                                    role="tab" aria-controls="vendor{{ $vnIdx }}"
                                    @if ($loop->first) aria-selected="true" @else aria-selected="false" @endif>
                                    {{ $listVendorHta->getNamaVendor->Nama ?? 'Vendor' . ($vnIdx + 1) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content" id="vendorTabContent">
                        @foreach ($data->getListVendorHta as $vnIdx => $listVendorHta)
                            <div class="tab-pane fade @if ($loop->first) show active @endif"
                                id="vendor{{ $vnIdx }}" role="tabpanel"
                                aria-labelledby="vendor{{ $vnIdx }}-tab">
                                <div class="mb-3">
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-2 fw-bold">Nama Vendor</div>
                                        <div class="col-md-10">
                                            : {{ $listVendorHta->getNamaVendor->Nama ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-2 fw-bold">Alamat</div>
                                        <div class="col-md-10">
                                            : {{ $listVendorHta->getNamaVendor->Alamat ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-2 fw-bold">Email</div>
                                        <div class="col-md-10">
                                            : {{ $listVendorHta->getNamaVendor->Email ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-2 fw-bold">Telepon</div>
                                        <div class="col-md-10">
                                            : {{ $listVendorHta->getNamaVendor->Telepon ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <table class="table align-middle" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:18%;">Kriteria / Parameter</th>
                                            <th>Deskripsi Penilaian</th>
                                            <th style="width:400px;">Nilai (Masing-masing Penilai)</th>
                                            <th style="width:120px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $grandTotal = 0;
                                        @endphp
                                        @foreach ($listVendorHta->getDetailListVendorHtaGpa as $i => $detail)
                                            @php
                                                $nilai_arr = [];
                                                $subtotal = 0;
                                                for ($pn = 1; $pn <= 5; $pn++) {
                                                    $score = $detail->{'Nilai' . $pn} ?? null;
                                                    $nilai_arr[] = $score;
                                                    $subtotal += intval($score);
                                                }
                                                $grandTotal += $subtotal;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control-plaintext" readonly
                                                        style="background:transparent;border:none"
                                                        value="{{ $detail->getParameter->Nama ?? '-' }}">
                                                </td>
                                                <td>
                                                    <div class="form-control-plaintext">{!! $detail->Deskripsi ?? '-' !!}</div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($nilai_arr as $k => $nilai)
                                                            <input type="text"
                                                                class="form-control-plaintext d-inline-block mb-1"
                                                                style="width: 70px; background:transparent; border:none"
                                                                value="{{ is_numeric($nilai) ? $nilai : '-' }}" readonly>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $subtotal }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-light">
                                            <td colspan="3"><span class="fw-bold">Total</span></td>
                                            <td>
                                                <span class="fw-bold">{{ $grandTotal }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 text-end">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
