@extends('layouts.app')

@section('content')
    @push('css')
        <style>
            #pengajuanTable td a[title*="Klik untuk melihat"] {
                position: relative;
                padding: 4px 8px;
                border-radius: 4px;
                transition: all 0.2s ease;
                border: 1px dashed transparent;
            }

            #pengajuanTable td a[title*="Klik untuk melihat"]:hover {
                background-color: #fff5f5;
                border-color: #dc3545;
                color: #a71d2a !important;
                text-decoration: none;
            }

            #pengajuanTable td a[title*="Klik untuk melihat"]:hover::after {
                content: "🔗";
                position: absolute;
                right: -15px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 10px;
                opacity: 0.7;
            }
        </style>
    @endpush

    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('ajukan.index') }}">Pengajuan Pembelian</a></li>
                    <li class="active">Detail</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-file-invoice"></i>
                    Detail Pengajuan Pembelian
                </h3>
                <p class="ph-subtitle">Lihat informasi lengkap mengenai pengajuan pembelian ini</p>
            </div>

            <!-- KANAN: Icon + Tombol Kembali -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="{{ route('ajukan.index') }}" class="ph-btn">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Permintaan Pembelian -->
    @include('form.pengajuan-pembelian.modal-perminttan')

    <div class="row">
        <div class="col-sm-12">
            {{-- Card Filter --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Filter Data Pengajuan Pembelian</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" class="row mb-3 align-items-end">
                        @php
                            $user = auth()->user();
                        @endphp

                        @if ($user->hasRole('Admin') || $user->hasRole('CCP') || $user->hasRole('CEO') || $user->hasRole('Group Head'))
                            <div class="col-md-3">
                                <label for="filter-perusahaan" class="form-label">Filter Perusahaan</label>
                                <select class="form-select select2" id="filter-perusahaan" name="perusahaan">
                                    <option value="">-- Semua Perusahaan --</option>
                                    @foreach ($perusahaan as $p)
                                        <option value="{{ $p->Kode }}">{{ $p->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <label for="filter-jenis" class="form-label">Filter Jenis Permintaan</label>
                            <select class="form-select select2" id="filter-jenis" name="jenis">
                                <option value="">-- Semua Jenis --</option>
                                @foreach ($jenisPermintaan as $item)
                                    <option value="{{ $item->id }}">{{ $item->Nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-status" class="form-label">Filter Status</label>
                            <select class="form-select select2" id="filter-status" name="status">
                                <option value="">Pilih Status</option>
                                <option value="Diajukan">Diajukan Ke CCP</option>
                                <option value="Dalam Review">Dalam Review CCP</option>
                                <option value="Selesai Review">Selesai Review CCP</option>
                                <option value="Menunggu Rekomendasi GH">Menunggu Rekomendasi GH CCP</option>
                                <option value="Siap Presentasi">Siap Presentasi</option>
                                <option value="Selesai">Selesai Presentasi Komite</option>
                                <option value="Ditolak CEO">Ditolak CEO</option>
                                <option value="Disetujui CEO">Disetujui CEO</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                        <!-- Tambah filter tanggal presentasi -->
                        <div class="col-md-3 mt-3 mt-md-0">
                            <label for="filter-tanggal-presentasi" class="form-label">Filter Tanggal Presentasi</label>
                            <input type="date" class="form-control" id="filter-tanggal-presentasi"
                                name="tanggal_presentasi" />
                        </div>
                        <div class="col-md-3 text-start pt-2 pt-md-0 mt-3">
                            <button type="button" id="reset-filter" class="btn btn-secondary mt-3 mt-md-0">
                                <i class="fa fa-sync-alt"></i> Reset Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- End Card Filter --}}

            {{-- Card DataTable --}}
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Daftar Pengajuan Pembelian</h4>
                        <p class="card-text mb-0">
                            Tabel ini berisi semua data pengajuan pembelian.
                        </p>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <button type="button" class="ph-btn" data-bs-toggle="modal"
                            data-bs-target="#modalPermintaanPembelian">
                            <i class="fa fa-database"></i> Ambil Data Permintaan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped datanew cell-border compact" id="pengajuanTable" width="100%">

                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode Pengajuan</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis</th>
                                    <th>Lokasi / Penempatan</th>
                                    <th>Perusahaan</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Presentasi</th>
                                    <th>Status</th>
                                    <th>-</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Card DataTable --}}

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

            // Filter Function
            function loadDataTable(jenis = '', status = '', tanggal_presentasi = '') {
                $('#pengajuanTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('ajukan.index') }}",
                        data: function(d) {
                            d.jenis = $('#filter-jenis').val();
                            d.status = $('#filter-status').val();
                            d.perusahaan = $('#filter-perusahaan').val();
                            d.tanggal_presentasi = $('#filter-tanggal-presentasi').val();
                        },
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
                            name: 'KodePengajuan',
                            defaultContent: '-'
                        },
                        {
                            data: 'NamaBarang',
                            name: 'NamaBarang',
                            defaultContent: '-'
                        },
                        {
                            data: 'Jenis',
                            name: 'Jenis',
                            defaultContent: '-'
                        },
                        {
                            data: 'LokasiPenempatan',
                            name: 'LokasiPenempatan',
                            defaultContent: '-'
                        },
                        {
                            data: 'KodePerusahaan',
                            name: 'KodePerusahaan',
                            defaultContent: '-'
                        },
                        {
                            data: 'UserCreate',
                            name: 'UserCreate',
                            defaultContent: '-'
                        },
                        {
                            data: 'TanggalPresentasi',
                            name: 'TanggalPresentasi',
                            defaultContent: '-'
                        },
                        {
                            data: 'Status',
                            name: 'Status',
                            defaultContent: '-'
                        },
                        {
                            data: 'CekStatus',
                            name: 'CekStatus',
                            defaultContent: '-'
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

            // Initial Load
            loadDataTable();

            // Filter event
            $('#filter-jenis, #filter-status, #filter-perusahaan, #filter-tanggal-presentasi').on('change',
                function() {
                    $('#pengajuanTable').DataTable().ajax.reload();
                });

            // Reset filter
            $('#reset-filter').on('click', function() {
                $('#filter-jenis').val('').trigger('change');
                $('#filter-status').val('').trigger('change');
                $('#filter-perusahaan').val('').trigger('change');
                $('#filter-tanggal-presentasi').val('').trigger('change');
                $('#pengajuanTable').DataTable().ajax.reload();
            });
        });
    </script>
@endpush
