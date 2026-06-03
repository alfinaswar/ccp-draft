@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Pengajuan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengajuan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- End Filter Bar --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Daftar Pengajuan Pembelian</h4>
                    <p class="card-text">
                        Tabel ini berisi semua data pengajuan pembelian.
                    </p>
                </div>
                <div class="card-body">
                    <div class="col-lg-12">
                        <div class="card p-3">
                            <form id="filterForm" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="filterJenis" class="form-label mb-0">Jenis</label>
                                    <select class="form-select" id="filterJenis" name="jenis">
                                        <option value="">Semua Jenis</option>
                                        @foreach ($jenis as $item)
                                            <option value="{{ $item->id }}">{{ $item->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterPerusahaan" class="form-label mb-0">Perusahaan</label>
                                    <select class="select2" id="filterPerusahaan" name="perusahaan">
                                        <option value="">Semua Perusahaan</option>
                                        @foreach ($perusahaan as $item)
                                            <option value="{{ $item->Kode }}">{{ $item->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterStatus" class="form-label mb-0">Status</label>
                                    <select class="form-select select2" id="filterStatus" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="Diajukan">Diajukan</option>
                                        <option value="Dalam Review">Dalam Review</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Ditolak CEO">Ditolak CEO</option>
                                        <option value="Disetujui CEO">Disetujui CEO</option>
                                        <option value="Menunggu Rekomendasi GH">Menunggu Rekomendasi GH</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Selesai Review">Selesai Review</option>
                                        <option value="Siap Presentasi">Siap Presentasi</option>

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterTanggalPresentasi" class="form-label mb-0">Tanggal Presentasi</label>
                                    <input type="date" class="form-control" id="filterTanggalPresentasi"
                                        name="tanggal_presentasi">
                                </div>

                                <div class="col-12 mt-2">
                                    <button type="button" id="resetFilterBtn" class="btn btn-secondary">
                                        <i class="fa fa-undo"></i> Reset Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3 nav-justified tab-style-5 d-sm-flex d-block" id="pills-tab"
                            role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-review-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-review" type="button" role="tab"
                                    aria-controls="pills-review" aria-selected="true">
                                    <i class="fa fa-search"></i> Dalam Proses Review
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-finish-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-finish" type="button" role="tab"
                                    aria-controls="pills-finish" aria-selected="false">
                                    <i class="fa fa-flag-checkered"></i> Selesai
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane show active text-muted" id="pills-review" role="tabpanel"
                                aria-labelledby="pills-review-tab" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table datanew cell-border compact stripe" id="pengajuanTable"
                                        width="100%">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nomor</th>
                                                <th>Nama Barang</th>
                                                <th>Lokasi / Penempatan</th>
                                                <th>Jenis</th>
                                                <th>Perusahaan</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Tanggal Diajukan</th>
                                                <th>Tanggal Presentasi</th>
                                                <th>Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane text-muted" id="pills-finish" role="tabpanel"
                                aria-labelledby="pills-finish-tab" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table datanew cell-border compact stripe" id="pengajuanTable2"
                                        width="100%">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nomor</th>
                                                <th>Nama Barang</th>
                                                <th>Lokasi / Penempatan</th>
                                                <th>Jenis</th>
                                                <th>Perusahaan</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Tanggal Diajukan</th>
                                                <th>Tanggal Presentasi</th>
                                                <th>Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('rekomendasi-pembelian.modal-taggal-diajukan')
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
        @if (Session::get('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#dc3545',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#dc3545',
                });
            </script>
        @endif
        @if (Session::get('warning'))
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: '{{ Session::get('warning') }}',
                    iconColor: '#ffc107',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#ffc107',
                });
            </script>
        @endif

        <script>
            $(document).ready(function() {



                $('body').on('click', '.btn-delete', function() {
                    var id = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: "Apakah Anda yakin ingin menghapus pengajuan pembelian ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('ajukan.destroy', ':id') }}'.replace(':id', id),
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.status === 200) {
                                        Swal.fire('Dihapus!', response.message, 'success');
                                        $('#pengajuanTable').DataTable().ajax.reload();
                                    } else {
                                        Swal.fire('Gagal!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Gagal!', xhr.responseJSON?.message ??
                                        'Terjadi kesalahan saat menghapus.', 'error');
                                }
                            });
                        }
                    });
                });

                // Load DataTable
                function loadDataTable() {
                    $('#pengajuanTable').DataTable({
                        responsive: true,
                        serverSide: true,
                        processing: true,
                        bDestroy: true,
                        ajax: {
                            url: "{{ route('rekomendasi.index') }}",
                            data: function(d) {
                                d.jenis = $('#filterJenis').val();
                                d.perusahaan = $('#filterPerusahaan').val();
                                d.status = $('#filterStatus').val();
                                d.tanggalPresentasi = $('#filterTanggalPresentasi').val();
                            }
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
                                data: 'KodePengajuan',
                                name: 'KodePengajuan'
                            },
                            {
                                data: 'NamaBarang',
                                name: 'NamaBarang',
                                searchable: true
                            },
                            {
                                data: 'LokasiPenempatan',
                                name: 'LokasiPenempatan',
                                searchable: true
                            },
                            {
                                data: 'Jenis',
                                name: 'Jenis'
                            },
                            {
                                data: 'KodePerusahaan',
                                name: 'KodePerusahaan'
                            },
                            {
                                data: 'UserCreate',
                                name: 'UserCreate'
                            },
                            {
                                data: 'DiajukanPada',
                                name: 'DiajukanPada'
                            },
                            {
                                data: 'TanggalPresentasi',
                                name: 'TanggalPresentasi'
                            },
                            {
                                data: 'Status',
                                name: 'Status'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });
                    $('#pengajuanTable2').DataTable({
                        responsive: true,
                        serverSide: true,
                        processing: true,
                        bDestroy: true,
                        ajax: {
                            url: "{{ route('rekomendasi.index-selesai') }}",
                            data: function(d) {
                                d.jenis = $('#filterJenis').val();
                                d.perusahaan = $('#filterPerusahaan').val();
                                d.status = $('#filterStatus').val();
                                d.tanggalPresentasi = $('#filterTanggalPresentasi').val();
                            }
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
                                data: 'KodePengajuan',
                                name: 'KodePengajuan'
                            },
                            {
                                data: 'NamaBarang',
                                name: 'NamaBarang'
                            },
                            {
                                data: 'LokasiPenempatan',
                                name: 'LokasiPenempatan',
                                searchable: true
                            },
                            {
                                data: 'Jenis',
                                name: 'Jenis'
                            },
                            {
                                data: 'KodePerusahaan',
                                name: 'KodePerusahaan'
                            },
                            {
                                data: 'UserCreate',
                                name: 'UserCreate'
                            },
                            {
                                data: 'DiajukanPada',
                                name: 'DiajukanPada'
                            },
                            {
                                data: 'TanggalPresentasi',
                                name: 'TanggalPresentasi'
                            },
                            {
                                data: 'Status',
                                name: 'Status'
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

                // Listen to filter changes
                $('#filterJenis, #filterPerusahaan, #filterStatus,#filterTanggalPresentasi').on('change', function() {
                    $('#pengajuanTable').DataTable().ajax.reload();
                    $('#pengajuanTable2').DataTable().ajax.reload();
                });

                // Reset filter button click
                $('#resetFilterBtn').on('click', function() {
                    $('#filterJenis').val('');
                    $('#filterPerusahaan').val('');
                    $('#filterStatus').val('');
                    $('#filterTanggalPresentasi').val('');
                    $('#pengajuanTable').DataTable().ajax.reload();
                });

                loadDataTable();
            });
        </script>
    @endpush
