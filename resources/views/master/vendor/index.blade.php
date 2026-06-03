@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Vendor</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Vendor</li>
                </ul>
            </div>
        </div>
    </div>

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


    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Daftar Vendor</h4>
                    <p class="card-text">
                        Tabel ini menampilkan seluruh vendor yang tersedia.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                            <label for="jenisFilter" class="form-label"><strong>Filter Jenis Vendor</strong></label>
                            <div class="input-group" style="gap: 10px;">
                                <select id="jenisFilter" class="form-select me-2">
                                    <option value="">Semua Jenis</option>
                                    <option value="Umum">Umum</option>
                                    <option value="Medis">Medis</option>
                                </select>
                                <button class="btn btn-secondary" type="button" id="resetJenisFilter"
                                    style="margin-left: 5px;">Reset Filter</button>
                            </div>
                        </div>
                        <div class="col text-end">
                            <a class="btn btn-success me-2" href="{{ route('vendor.exportExcel') }}">
                                <i class="fa fa-download"></i> Download Excel
                            </a>
                            <a class="btn btn-primary" href="{{ route('vendor.create') }}">Tambah Vendor Baru</a>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="vendorTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    {{-- <th>No HP</th> --}}
                                    <th>Email</th>
                                    <th>Nama PIC</th>
                                    <th>No HP PIC</th>
                                    <th>Jenis</th>
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Delete handling
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Apakah Anda yakin ingin menghapus vendor ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('vendor.destroy', ':id') }}'.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#vendorTable').DataTable().ajax.reload();
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

            function loadDataTable() {
                $('#vendorTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('vendor.index') }}",
                        data: function(d) {
                            d.jenis = $('#jenisFilter').val();
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
                            data: 'Nama',
                            name: 'Nama'
                        },
                        {
                            data: 'Alamat',
                            name: 'Alamat'
                        },
                        // {
                        //     data: 'NoHp',
                        //     name: 'NoHp'
                        // },
                        {
                            data: 'Email',
                            name: 'Email'
                        },
                        {
                            data: 'NamaPic',
                            name: 'NamaPic'
                        },
                        {
                            data: 'NoHpPic',
                            name: 'NoHpPic'
                        },
                        {
                            data: 'Jenis',
                            name: 'Jenis',
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


            loadDataTable();


            $('#jenisFilter').change(function() {
                $('#vendorTable').DataTable().ajax.reload();
            });

            // Reset filter button
            $('#resetJenisFilter').click(function() {
                $('#jenisFilter').val('');
                $('#vendorTable').DataTable().ajax.reload();
            });
        });
    </script>
@endpush
