@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Master Parameter Komplain</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-list-check"></i>
                    Master Parameter Komplain
                </h3>
                <p class="ph-subtitle">
                    Pilihan checkbox pada Formulir Evaluasi BMHP Umum, per bagian formulir.
                </p>
            </div>
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-list-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">List Parameter Komplain</h4>
                        <small class="card-text text-muted">Parameter nonaktif tidak ditampilkan pada formulir.</small>
                    </div>
                    <a class="btn btn-primary" href="{{ route('parameter-komplain.create') }}">Tambah Parameter</a>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <select id="filterJenis" class="form-select">
                                <option value="">Semua Bagian</option>
                                @foreach ($jenisList as $kode => $label)
                                    <option value="{{ $kode }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="parameterTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Bagian Formulir</th>
                                    <th>Nama Parameter</th>
                                    <th width="8%">Urutan</th>
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
                    text: "Apakah Anda yakin ingin menghapus parameter ini? Formulir yang sudah masuk tidak berubah.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('parameter-komplain.destroy', ':id') }}'.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#parameterTable').DataTable().ajax.reload();
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

            $('#parameterTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                bDestroy: true,
                ajax: {
                    url: "{{ route('parameter-komplain.index') }}",
                    data: function(d) {
                        d.jenis = $('#filterJenis').val();
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
                        data: 'JenisText',
                        name: 'Jenis'
                    },
                    {
                        data: 'Nama',
                        name: 'Nama'
                    },
                    {
                        data: 'Urutan',
                        name: 'Urutan'
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

            $('#filterJenis').on('change', function() {
                $('#parameterTable').DataTable().ajax.reload();
            });
        });
    </script>
@endpush
