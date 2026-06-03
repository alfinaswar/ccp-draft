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
                                <div class="col-md-4">
                                    <label for="filterJenis" class="form-label mb-0">Jenis</label>
                                    <select class="form-select" id="filterJenis" name="jenis">
                                        <option value="">Semua Jenis</option>
                                        @foreach ($jenis as $item)
                                            <option value="{{ $item->id }}">{{ $item->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filterPerusahaan" class="form-label mb-0">Perusahaan</label>
                                    <select class="select2" id="filterPerusahaan" name="perusahaan">
                                        <option value="">Semua Perusahaan</option>
                                        @foreach ($perusahaan as $item)
                                            <option value="{{ $item->Kode }}">{{ $item->Nama }}</option>
                                        @endforeach
                                    </select>
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


                        <div class="table-responsive">
                            <table class="table datanew cell-border compact stripe" id="pengajuanTable2" width="100%">
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
                                        $('#pengajuanTable2').DataTable().ajax.reload();
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
                    $('#pengajuanTable2').DataTable({
                        responsive: true,
                        serverSide: true,
                        processing: true,
                        bDestroy: true,
                        ajax: {
                            url: "{{ route('laporan.history') }}",
                            data: function(d) {
                                d.jenis = $('#filterJenis').val();
                                d.perusahaan = $('#filterPerusahaan').val();
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
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });
                }

                // Listen to filter changes
                $('#filterJenis, #filterPerusahaan').on('change', function() {
                    $('#pengajuanTable2').DataTable().ajax.reload();
                });

                // Reset filter button click
                $('#resetFilterBtn').on('click', function() {
                    $('#filterJenis').val('');
                    $('#filterPerusahaan').val('');
                    $('#pengajuanTable2').DataTable().ajax.reload();
                });

                loadDataTable();
            });
        </script>
    @endpush
