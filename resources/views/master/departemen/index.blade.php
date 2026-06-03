@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Departemen</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Departemen</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col text-end">
            <a class="btn btn-primary" href="{{ route('departemen.create') }}">Tambah Departemen Baru</a>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal"
                data-bs-target="#modalSinkronDepartemen">
                <i class="fas fa-sync-alt"></i> Sinkron
            </button>

            <!-- Modal Sinkron Departemen -->
            @include('master.departemen.modal-sinkron')
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">List Departemen</h4>
                    <p class="card-text">
                        Tabel ini berisi semua data departemen yang ada.
                    </p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="departemenTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>Updated At</th>
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
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Apakah Anda yakin ingin menghapus departemen ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('departemen.destroy', ':id') }}'.replace(':id',
                                id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#departemenTable').DataTable().ajax.reload();
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
                $('#departemenTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('departemen.index') }}",
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
                            data: 'KodePerusahaan',
                            name: 'KodePerusahaan'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
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
        });
    </script>
    <script>
        $(function() {
            var isSyncing = false;

            // Fungsi untuk mengaktifkan/menonaktifkan tombol Batal dan F5
            function setInteractionDisabled(disabled) {
                // Tombol batal
                $('#btnCancelSinkron').prop('disabled', disabled);

                // Disable F5 saat proses berlangsung
                if (disabled) {
                    $(document).on('keydown.sinkronblock', function(e) {
                        // 116 == F5, 82 == R (Ctrl+R)
                        if ((e.which === 116) || (e.which === 82 && e.ctrlKey)) {
                            e.preventDefault();
                        }
                    });
                } else {
                    $(document).off('keydown.sinkronblock');
                }
            }

            $('#formSinkronDepartemen').on('submit', function(e) {
                e.preventDefault();

                $('#progressSinkronWrapper').show();
                $('#btnSubmitSinkron').prop('disabled', true).text('Menyinkronkan...');
                setInteractionDisabled(true); // Nonaktifkan tombol batal & F5
                isSyncing = true;

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        return xhr;
                    },
                    beforeSend: function() {
                        $('#progressSinkronBar').css('width', '100%')
                            .addClass('progress-bar-animated');
                        $('#progressSinkronBar').text('Proses sinkronisasi, mohon tunggu...');
                    },
                    success: function(res) {
                        $('#progressSinkronBar').removeClass('progress-bar-animated');
                        setInteractionDisabled(false); // Aktifkan kembali tombol dan F5
                        isSyncing = false;

                        if (res.success) {
                            $('#progressSinkronBar').text(res.message);
                            setTimeout(function() {
                                $('#modalSinkronDepartemen').modal('hide');
                                $('#progressSinkronWrapper').hide();
                                $('#progressSinkronBar').text(
                                    'Proses sinkronisasi, mohon tunggu...');
                                $('#btnSubmitSinkron').prop('disabled', false).text(
                                    'Ya, Sinkronkan');
                                // Reload datatable jika berhasil
                                if ($.fn.DataTable.isDataTable('#departemenTable')) {
                                    $('#departemenTable').DataTable().ajax.reload(null,
                                        false);
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    iconColor: '#4BCC1F',
                                    confirmButtonText: 'Oke',
                                    confirmButtonColor: '#4BCC1F',
                                });
                            }, 1200);
                        } else {
                            $('#progressSinkronBar').css('width', '100%')
                                .removeClass('progress-bar-animated bg-success')
                                .addClass('bg-danger')
                                .text(res.message ? res.message : 'Terjadi kesalahan!');
                            setTimeout(function() {
                                $('#progressSinkronWrapper').hide();
                                $('#progressSinkronBar').removeClass('bg-danger')
                                    .addClass('bg-success').text(
                                        'Proses sinkronisasi, mohon tunggu...');
                                $('#btnSubmitSinkron').prop('disabled', false).text(
                                    'Ya, Sinkronkan');
                            }, 1500);
                            Swal.fire('Gagal', res.message ? res.message : 'Terjadi kesalahan!',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        $('#progressSinkronBar').removeClass('progress-bar-animated').css(
                                'width', '100%')
                            .removeClass('bg-success')
                            .addClass('bg-danger')
                            .text(xhr.responseJSON && xhr.responseJSON.message ? xhr
                                .responseJSON.message : 'Terjadi kesalahan pada server.');
                        setInteractionDisabled(
                            false); // Aktifkan kembali tombol dan F5 meski error
                        isSyncing = false;
                        setTimeout(function() {
                            $('#progressSinkronWrapper').hide();
                            $('#progressSinkronBar').removeClass('bg-danger').addClass(
                                'bg-success').text(
                                'Proses sinkronisasi, mohon tunggu...');
                            $('#btnSubmitSinkron').prop('disabled', false).text(
                                'Ya, Sinkronkan');
                        }, 1500);
                        Swal.fire('Gagal', xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Terjadi kesalahan pada server.',
                            'error');
                    },
                    complete: function() {
                        setInteractionDisabled(false);
                        isSyncing = false;
                    }
                });
            });

            // Tombol batal pada modal sinkronisasi
            $('#btnCancelSinkron').on('click', function() {
                if (isSyncing) {
                    return false;
                } else {
                    $('#modalSinkronDepartemen').modal('hide');
                }
            });

            $('#modalSinkronDepartemen').on('hidden.bs.modal', function() {
                $('#progressSinkronWrapper').hide();
                $('#progressSinkronBar').removeClass('bg-danger').addClass('bg-success').text(
                    'Proses sinkronisasi, mohon tunggu...');
                $('#btnSubmitSinkron').prop('disabled', false).text('Ya, Sinkronkan');
                setInteractionDisabled(false); // Pastikan tombol batal & F5 aktif saat modal tertutup
                isSyncing = false;
                $('#formSinkronDepartemen')[0].reset();
            });
        });
    </script>
@endpush
