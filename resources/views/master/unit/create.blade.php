@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('unit.index') }}">Master Unit</a></li>
                    <li class="active">Tambah</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-sitemap"></i>
                    Tambah Unit
                </h3>
            </div>
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-sitemap"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0 text-white">Formulir Unit</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('unit.store') }}" method="POST">
                        @csrf
                        @include('master.unit._form')
                        <div class="text-end mt-3">
                            <a href="{{ route('unit.perusahaan', $kembali) }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
