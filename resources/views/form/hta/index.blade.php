@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Isi HTA / GPA</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">HTA</li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">List Barang</h4>
                    <p class="card-text">
                        Tabel ini berisi semua data barang yang diajukan.
                    </p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="merkTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Barang</th>
                                    <th>Merek</th>
                                    <th>HTA / GPA</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($data->getHta ?? []) as $key => $list)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $list->getNamaBarang->Nama ?? '-' }}</td>
                                        <td>{{ $list->getNamaBarang->getMerk->Nama ?? '-' }}</td>
                                        <td>
                                            @php
                                                $lastSegment = collect(request()->segments())->last();
                                                $idBarang = $list->getNamaBarang->id ?? null;
                                            @endphp
                                            @if ($data->Jenis == '1')
                                                <a href="{{ route('hta.create', ['IdPengajuan' => $lastSegment, 'barang' => $idBarang]) }}"
                                                    class="btn btn-sm btn-primary">HTA</a>
                                            @else
                                                <a href="{{ route('gpa.create', ['IdPengajuan' => $lastSegment, 'barang' => $idBarang]) }}"
                                                    class="btn btn-sm btn-primary">GPA</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($list->TotalPoinVendor1) || !empty($list->TotalPoinVendor2) || !empty($list->TotalPoinVendor3))
                                                <span class="badge badge-success">Sudah diisi</span>
                                            @else
                                                <span class="badge badge-warning">Belum diisi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data barang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
