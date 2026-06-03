@extends('layouts.app')

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="card-title mb-0">Daftar Jenis Formulir Perusahaan</h4>
            </div>

            <div class="card-body">
                <div class="card ribbone-card">
                    <div class="card-body p-6">
                        <h6 class="card-subtitle mb-2 text-dark fw-bold">Keterangan</h6>

                        <div class="mb-3">
                            <label for="formName" class="form-label fw-bold">Nama Form</label>
                            <input type="text" class="form-control" id="formName" value="{{ $form->Nama ?? '' }}"
                                name="NamaForm" readonly>
                        </div>
                        <p class="card-text">
                            Silakan atur urutan, jabatan, dan departemen untuk setiap pengguna yang akan menandatangani
                            dokumen pada formulir ini.<br>
                            Klik <b>Tambah Baris</b> untuk menambah penanda tangan baru. Hapus baris jika tidak diperlukan.
                        </p>
                    </div>
                </div>
                <div class="table-responsive">
                    <form action="{{ route('master-approval.store') }}" method="POST" id="ttdForm">
                        @csrf
                        <input type="hidden" id="formKode" value="{{ $form->id ?? '' }}" name="JenisForm">
                        <input type="hidden" id="formKode" value="{{ $KodePerusahaan ?? '' }}" name="KodePerusahaan">
                        <div class="table-responsive">
                            <table class="table align-middle" id="ttdTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:4%">No</th>
                                        <th style="width:18%">Nama</th>
                                        <th style="width:18%">Jabatan</th>
                                        <th style="width:18%">Departemen</th>
                                        <th style="width:8%">Urutan</th>
                                        <th style="width:16%">Jabatan (Tampil PDF)</th>
                                        <th style="width:6%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($form->getApproval as $index => $approval)
                                        <tr>
                                            <td class="text-center align-middle row-number">{{ $index + 1 }}</td>
                                            <td>
                                                <select class="form-select select2 user-nama" name="UserId[]">
                                                    <option value="">Pilih Nama</option>
                                                    @foreach ($user as $u)
                                                        <option value="{{ $u->id }}"
                                                            data-jabatan="{{ $u->jabatan ?? '' }}"
                                                            data-departemen="{{ $u->departemen ?? '' }}"
                                                            @if ($approval->UserId == $u->id) selected @endif>
                                                            {{ $u->name }} - {{ $u->getJabatan->Nama ?? '-' }}
                                                            {{ $u->getDepartemen->Nama ?? '-' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select select2 jabatan-select" name="JabatanId[]">
                                                    <option value="">Pilih Jabatan</option>
                                                    @foreach ($jabatan as $j)
                                                        <option value="{{ $j->id }}"
                                                            @if ($approval->JabatanId == $j->id) selected @endif>
                                                            {{ $j->Nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select select2 departemenid-select"
                                                    name="DepartemenId[]">
                                                    <option value="">Pilih Departemen</option>
                                                    @foreach ($departemen as $d)
                                                        <option value="{{ $d->id }}"
                                                            @if ($approval->DepartemenId == $d->id) selected @endif>
                                                            {{ $d->Nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" min="1" class="form-control urutan-input"
                                                    name="Urutan[]" placeholder="Urutan" value="{{ $approval->Urutan }}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="NamaJabatan[]"
                                                    placeholder="Nama Jabatan" value="{{ $approval->NamaJabatan ?? '' }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                    @if ($index == 0 && $form->getApproval->count() == 1) disabled @endif>
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center align-middle row-number">1</td>
                                            <td>
                                                <select class="form-select select2 user-nama" name="UserId[]">
                                                    <option value="" data-jabatan="" data-departemen=""
                                                        data-departemenid="">
                                                        Pilih Nama
                                                    </option>
                                                    @foreach ($user as $u)
                                                        <option value="{{ $u->id }}"
                                                            data-jabatan="{{ $u->jabatan ?? '' }}"
                                                            data-departemen="{{ $u->departemen ?? '' }}">
                                                            {{ $u->name }} - {{ $u->getJabatan->Nama ?? '-' }}
                                                            {{ $u->getDepartemen->Nama ?? '-' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select select2 jabatan-select" name="JabatanId[]">
                                                    <option value="">Pilih Jabatan</option>
                                                    @foreach ($jabatan as $j)
                                                        <option value="{{ $j->id }}">{{ $j->Nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select select2 departemenid-select"
                                                    name="DepartemenId[]">
                                                    <option value="">Pilih Departemen</option>
                                                    @foreach ($departemen as $d)
                                                        <option value="{{ $d->id }}">{{ $d->Nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" min="1" class="form-control urutan-input"
                                                    name="Urutan[]" placeholder="Urutan" value="1" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="NamaJabatan[]"
                                                    placeholder="Nama Jabatan" value="">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                    disabled>
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- Tambah jarak di bawah tabel sebelum tombol --}}
                        <div class="mb-2 d-flex justify-content-end align-items-center" style="margin-top: 20px;">
                            <button type="button" id="addRowBtn" class="btn btn-primary btn-sm me-auto">
                                <i class="fa fa-plus"></i> Tambah Baris
                            </button>
                            <a href="{{ route('master-approval.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                            <button type="submit" class="btn btn-success ms-2">Simpan</button>
                        </div>
                    </form>
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
            $('.select2').select2({
                width: '100%'
            });

            $('#ttdTable').on('change', '.user-nama', function() {
                var selectedOption = $(this).find('option:selected');
                var jabatanId = selectedOption.data('jabatan');
                var departemenId = selectedOption.data('departemen') || '';
                var $tr = $(this).closest('tr');
                var $jabatanSelect = $tr.find('.jabatan-select');
                var $departemenInput = $tr.find('.departemen-input');
                var $departemenIdSelect = $tr.find('.departemenid-select');

                if (jabatanId) {
                    var $match = $jabatanSelect.find('option').filter(function() {
                        return $(this).val() == jabatanId;
                    });
                    if ($match.length > 0) {
                        $jabatanSelect.val($match.val()).trigger('change');
                    }
                }

                // Departemen otomatis (nama)
                $departemenInput.val(departemenId);
                if (departemenId) {
                    var $depMatch = $departemenIdSelect.find('option').filter(function() {
                        return $(this).val() == departemenId;
                    });
                    if ($depMatch.length > 0) {
                        $departemenIdSelect.val($depMatch.val()).trigger('change');
                    }
                }
            });

            $('#addRowBtn').on('click', function() {
                var rowCount = $('#ttdTable tbody tr').length + 1;
                var namaOptions = '';
                @foreach ($user as $u)
                    namaOptions +=
                        `<option value="{{ $u->id }}" data-jabatan="{{ $u->jabatan ?? '' }}" data-departemen="{{ $u->departemen ?? '' }}" data-departemenid="{{ $u->departemen_id ?? '' }}">{{ $u->name }}</option>`;
                @endforeach

                var jabatanOptions = '';
                @foreach ($jabatan as $j)
                    jabatanOptions += `<option value="{{ $j->id }}">{{ $j->Nama }}</option>`;
                @endforeach

                var departemenOptions = '';
                @foreach ($departemen as $d)
                    departemenOptions +=
                        `<option value="{{ $d->id }}">{{ $d->Nama }}</option>`;
                @endforeach

                var newRow = `
                    <tr>
                        <td class="text-center align-middle row-number">` + rowCount + `</td>
                        <td>
                            <select class="form-select select2 user-nama" name="UserId[]">
                                <option value="" data-jabatan="" data-departemen="">Pilih Nama</option>
                                ` + namaOptions +
                    `
                            </select>
                        </td>
                        <td>
                            <select class="form-select select2 jabatan-select" name="JabatanId[]">
                                <option value="">Pilih Jabatan</option>
                                ` + jabatanOptions +
                    `
                            </select>
                        </td>

                        <td>
                            <select class="form-select select2 departemenid-select" name="DepartemenId[]">
                                <option value="">Pilih Departemen</option>
                                ` + departemenOptions +
                    `
                            </select>
                        </td>
                        <td>
                            <input type="number" min="1" class="form-control urutan-input" name="Urutan[]" placeholder="Urutan" value="` +
                    rowCount + `" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="NamaJabatan[]" placeholder="Nama Jabatan" value="">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-remove-row">&times;</button>
                        </td>
                    </tr>
                `;
                $('#ttdTable tbody').append(newRow);

                // Inisialisasi Select2 pada select2 yang baru saja ditambahkan
                $('#ttdTable tbody tr:last .select2').select2({
                    width: '100%'
                });

                updateRowNumbersAndUrutan();
            });


            $('#ttdTable').on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
                updateRowNumbersAndUrutan();
                if ($('#ttdTable tbody tr').length === 1) {
                    $('#ttdTable tbody tr .btn-remove-row').prop('disabled', true);
                }
            });
            if ($('#ttdTable tbody tr').length === 1) {
                $('#ttdTable tbody tr .btn-remove-row').prop('disabled', true);
            }

            function updateRowNumbersAndUrutan() {
                $('#ttdTable tbody tr').each(function(index, row) {
                    $(row).find('.row-number').text(index + 1);
                    $(row).find('.urutan-input').val(index + 1);
                    $(row).find('.btn-remove-row').prop('disabled', $('#ttdTable tbody tr').length === 1);
                });
            }
        });
    </script>
@endpush
