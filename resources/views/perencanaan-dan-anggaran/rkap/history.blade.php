@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">History RKAP Perusahaan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rkap.index') }}">Master Perusahaan</a></li>
                    <li class="breadcrumb-item active">History RKAP</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-3">

        <div class="col d-flex justify-content-between">
            <a href="{{ route('rkap.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRkap">
                <i class="fa fa-plus-circle"></i> Tambah RKAP
            </button>
        </div>
    </div>
    @include('perencanaan-dan-anggaran.rkap.modal')
    @include('perencanaan-dan-anggaran.rkap.modal-edit')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Daftar History RKAP</h4>
                    <p class="card-text">
                        Tabel ini berisi riwayat data RKAP perusahaan yang bersangkutan.
                    </p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="historyRkapTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tahun</th>
                                    <th>Nominal RKAP</th>
                                    <th>Sisa RKAP</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Diperbarui Oleh</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ Session::get('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif
    <script>
        function formatRupiah(angka, prefix) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Medis
            const medisInput = document.getElementById('nominalMedis');
            const medisDisplay = document.getElementById('viewNominalMedis');
            if (medisInput) {
                medisInput.addEventListener('input', function(e) {
                    this.value = formatRupiah(this.value, 'Rp ');
                    medisDisplay.textContent = this.value ? 'Nominal: ' + this.value : '';
                });
            }
            // Umum
            const umumInput = document.getElementById('nominalUmum');
            const umumDisplay = document.getElementById('viewNominalUmum');
            if (umumInput) {
                umumInput.addEventListener('input', function(e) {
                    this.value = formatRupiah(this.value, 'Rp ');
                    umumDisplay.textContent = this.value ? 'Nominal: ' + this.value : '';
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectTahun = document.getElementById('tahun');
            let tahunSekarang = new Date().getFullYear();
            let tahunMax = tahunSekarang + 7;
            for (let tahun = tahunMax; tahun >= 2010; tahun--) {
                let option = document.createElement('option');
                option.value = tahun;
                option.text = tahun;
                selectTahun.appendChild(option);
            }
            if ($(selectTahun).length) {
                $(selectTahun).select2({
                    placeholder: "Pilih Tahun",
                    allowClear: true,
                    minimumResultsForSearch: Infinity
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            function loadHistoryTable() {
                $('#historyRkapTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('rkap.history', request()->route('id')) }}",
                    },
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Tahun',
                            name: 'Tahun'
                        },
                        {
                            data: 'NominalRkap',
                            name: 'NominalRkap'
                        },
                        {
                            data: 'SisaRkap',
                            name: 'SisaRkap'
                        },
                        {
                            data: 'UserCreate',
                            name: 'UserCreate'
                        },
                        {
                            data: 'UserUpdate',
                            name: 'UserUpdate'
                        },

                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            loadHistoryTable();
        });
    </script>
@endpush
