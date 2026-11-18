@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('unit.index') }}">Master Unit</a></li>
                    <li class="active">{{ $nama }}</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-sitemap"></i>
                    Unit &mdash; {{ $nama }}
                </h3>
                <p class="ph-subtitle">
                    Unit nonaktif tidak muncul pada form komplain.
                </p>
            </div>
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-sitemap"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">List Unit</h4>
                        <small class="card-text text-muted">{{ $nama }}</small>
                    </div>
                    <div>
                        <a href="{{ route('unit.index') }}" class="btn btn-secondary me-2">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <a class="btn btn-primary" href="{{ route('unit.create', ['perusahaan' => $kode]) }}">
                            <i class="fa fa-plus"></i> Tambah Unit
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="unitTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Unit</th>
                                    <th width="15%">Kode</th>
                                    <th width="10%">Status</th>
                                    <th width="18%">Aksi</th>
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
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Unit yang dihapus tidak muncul lagi pada form komplain.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('unit.destroy', ':id') }}'.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#unitTable').DataTable().ajax.reload();
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

            $('#unitTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                bDestroy: true,
                ajax: "{{ route('unit.perusahaan', $kode) }}",
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
                        data: 'nama_unit',
                        name: 'nama_unit'
                    },
                    {
                        data: 'kode_unit',
                        name: 'kode_unit'
                    },
                    {
                        data: 'AktifBadge',
                        name: 'Aktif'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
