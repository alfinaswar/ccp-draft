@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Permintaan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Permintaan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col text-end">
            <a class="btn btn-primary" href="{{ route('pp.create') }}">
                <i class="fa fa-plus me-1"></i> Tambah Permintaan Baru
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Daftar Permintaan Pembelian</h4>
                    <p class="card-text">
                        Tabel ini berisi semua data permintaan pembelian yang diajukan oleh departemen.
                    </p>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3" role="alert">
                        <i class="fa fa-info-circle"></i>
                        Klik <strong>Kirim Permintaan</strong> pada aksi untuk mengganti status dari <span
                            class="badge bg-secondary">Draft</span> menjadi <span class="badge bg-success">Sudah
                            Diajukan</span>.
                    </div>
                    <div class="card p-3">
                        <div class="row mb-3 align-items-end">
                            @if (auth()->user()->hasRole('Group Head') ||
                                    auth()->user()->hasRole('CEO') ||
                                    auth()->user()->hasRole('Admin') ||
                                    auth()->user()->hasRole('CCP'))
                                <div class="col-md-4">
                                    <label for="filter-rs" class="form-label">Filter Perusahaan</label>
                                    <select class="form-select select2" id="filter-rs">
                                        <option value="">-- Semua Perusahaan --</option>
                                        @foreach ($perusahaan as $item)
                                            <option value="{{ $item->Kode }}">{{ $item->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <label for="filter-jenis" class="form-label">Filter Jenis Permintaan</label>
                                <select class="form-select select2" id="filter-jenis">
                                    <option value="">-- Semua Jenis --</option>
                                    @foreach ($jenisPermintaan as $item)
                                        <option value="{{ $item->id }}">{{ $item->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter-status" class="form-label">Filter Status</label>
                                <select class="form-select select2" id="filter-status">
                                    <option value="">-- Semua Status --</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Sudah Diajukan">Sudah Diajukan</option>
                                    <option value="Telah Disetujui">Telah Disetujui</option>
                                    <option value="Dalam Proses Review">Dalam Proses Review</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-4 text-start pt-2 pt-md-0 mt-3">
                                <button type="button" id="reset-filter" class="btn btn-secondary mt-3 mt-md-0">
                                    <i class="fa fa-sync-alt"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="permintaanTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nomor</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis</th>
                                    <th>Lokasi / Penempatan</th>
                                    <th>Departemen</th>
                                    <th>Tanggal</th>
                                    <th>Diajukan Oleh</th>
                                    <th>Asal Permintaan</th>
                                    <th>Diajukan Pada</th>
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
        $(document).ready(function() {
            // Hapus data
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Apakah Anda yakin ingin menghapus permintaan pembelian ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('pp.destroy', ':id') }}'.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#permintaanTable').DataTable().ajax.reload();
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
                $('#permintaanTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('pp.index') }}",
                        data: function(d) {
                            d.jenis = $('#filter-jenis').val();
                            d.status = $('#filter-status').val();
                            d.rs = $('#filter-rs').val();
                        }
                    },
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
                        paginate: {
                            next: '<i class="fa fa-angle-double-right"></i>',
                            previous: '<i class="fa fa-angle-double-left"></i>'
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'NomorPermintaan'
                        },
                        {
                            data: 'NamaBarang'
                        },
                        {
                            data: 'Jenis'
                        },
                        {
                            data: 'LokasiPenempatan'
                        },
                        {
                            data: 'Departemen'
                        },
                        {
                            data: 'Tanggal'
                        },
                        {
                            data: 'DiajukanOleh'
                        },
                        {
                            data: 'KodePerusahaan'
                        },
                        {
                            data: 'DiajukanPada'
                        },
                        {
                            data: 'Status'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            $('#filter-jenis, #filter-status,#filter-rs').on('change', function() {
                $('#permintaanTable').DataTable().ajax.reload();
            });

            // Reset Filter Button
            $('#reset-filter').on('click', function() {
                $('#filter-jenis').val('').trigger('change');
                $('#filter-status').val('').trigger('change');
                $('#permintaanTable').DataTable().ajax.reload();
            });

            loadDataTable();
        });
    </script>
@endpush
