<div class="modal fade" id="modalPenilai" tabindex="-1" aria-labelledby="modalPenilaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenilaiLabel">Permintaan ini Akan dikirim Ke</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <input type="hidden" name="IdPengajuan" value="{{ $data->id }}">
            <div class="modal-body">
                <div class="alert alert-info mb-4" role="alert">
                    <strong>Perhatian:</strong>
                    <ol class="mb-0 ps-4">
                        <li>Pastikan memilih user, jabatan, dan departemen dengan benar.</li>
                        <li>Email akan digunakan untuk pengiriman notifikasi ke pihak atau departemen
                            terkait.</li>
                    </ol>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" style="width:100%;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px;">Urutan</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $approvalCount = count($approval);
                                $userLogin = auth()->user();
                            @endphp
                            @forelse($approval as $key => $app)
                                <tr>
                                    <td>Urutan {{ $key + 1 }}</td>
                                    <td>
                                        @if ($key == 0)
                                            <!-- Urutan 1 ambil dari session login, tidak readonly/disabled -->
                                            <select class="form-control user-penilai-select" name="UserId[]"
                                                style="width: 100%;" data-row-index="{{ $key }}" required>
                                                @foreach ($user as $usr)
                                                    <option value="{{ $usr->id }}|{{ $usr->name }}"
                                                        data-email="{{ $usr->email }}"
                                                        data-jabatanid="{{ $usr->jabatan ?? '' }}"
                                                        data-departemenid="{{ $usr->departemen ?? '' }}"
                                                        {{ $userLogin->id == $usr->id ? 'selected' : '' }}>
                                                        {{ $usr->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control user-penilai-select" name="UserId[]"
                                                style="width: 100%;" data-row-index="{{ $key }}" required>
                                                <option value="">Pilih User</option>
                                                @foreach ($user as $usr)
                                                    <option value="{{ $usr->id }}|{{ $usr->name }}"
                                                        data-email="{{ $usr->email }}"
                                                        data-jabatanid="{{ $usr->jabatan ?? '' }}"
                                                        data-departemenid="{{ $usr->departemen ?? '' }}"
                                                        {{ isset($app->UserId) && $app->UserId == $usr->id ? 'selected' : '' }}>
                                                        {{ $usr->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($key == 0)
                                            <input type="email" class="form-control email-penilai-input"
                                                name="Email[]" value="{{ $userLogin->email }}"
                                                data-row-index="{{ $key }}" required>
                                        @else
                                            <input type="email" class="form-control email-penilai-input"
                                                name="Email[]"
                                                value="{{ $app->Email ?? ($app->getUser->email ?? '') }}"
                                                data-row-index="{{ $key }}" required>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($key == 0)
                                            <select class="form-control jabatan-penilai-select" name="JabatanId[]"
                                                style="width: 100%;" data-row-index="{{ $key }}" required>
                                                @foreach ($jabatan as $jab)
                                                    <option value="{{ $jab->id }}"
                                                        {{ $userLogin->jabatan == $jab->id ? 'selected' : '' }}>
                                                        {{ $jab->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control jabatan-penilai-select" name="JabatanId[]"
                                                style="width: 100%;" data-row-index="{{ $key }}" required>
                                                <option value="">Pilih Jabatan</option>
                                                @foreach ($jabatan as $jab)
                                                    <option value="{{ $jab->id }}"
                                                        {{ isset($app->JabatanId) && $app->JabatanId == $jab->id ? 'selected' : '' }}>
                                                        {{ $jab->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($key == 0)
                                            <select class="form-control departemen-penilai-select" name="DepartemenId[]"
                                                style="width: 100%;" data-row-index="{{ $key }}" required>
                                                @foreach ($departemen as $dept)
                                                    <option value="{{ $dept->id }}"
                                                        {{ $userLogin->departemen == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control departemen-penilai-select" name="DepartemenId[]"
                                                style="width: 100%;" data-row-index="{{ $key }}" required>
                                                <option value="">Pilih Departemen</option>
                                                @foreach ($departemen as $dept)
                                                    <option value="{{ $dept->id }}"
                                                        {{ isset($app->DepartemenId) && $app->DepartemenId == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data
                                        penilai</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Tutup
                </button>
                <button type="submit" class="btn btn-primary" id="btnKonfirmasiAjukan">
                    <i class="fa fa-paper-plane me-1"></i> Simpan dan Ajukan
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            if ($.fn.select2) {
                $('#modalPenilai').on('shown.bs.modal', function() {
                    $('.user-penilai-select, .jabatan-penilai-select, .departemen-penilai-select').each(
                        function() {
                            if ($(this).hasClass("select2-hidden-accessible")) {
                                $(this).select2('destroy');
                            }
                        });

                    $('.user-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih User",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                    $('.jabatan-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih Jabatan",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                    $('.departemen-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih Departemen",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                });

                if ($('#modalPenilai').hasClass('show')) {
                    $('.user-penilai-select, .jabatan-penilai-select, .departemen-penilai-select').each(function() {
                        if ($(this).hasClass("select2-hidden-accessible")) {
                            $(this).select2('destroy');
                        }
                    });
                    $('.user-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih User",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                    $('.jabatan-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih Jabatan",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                    $('.departemen-penilai-select').select2({
                        dropdownParent: $('#modalPenilai'),
                        width: '100%',
                        placeholder: "Pilih Departemen",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                }
            } else {
                console.warn("Plugin select2 belum dimuat.");
            }

            $(document).on('change', '.user-penilai-select', function() {
                var $select = $(this);
                var rowIndex = $select.data('row-index');
                var selectedOption = $select.find('option:selected');
                var email = selectedOption.data('email') || '';
                // Isi kolom email pada baris yang sama
                $('input.email-penilai-input[data-row-index="' + rowIndex + '"]').val(email);

                // Opsi tambahan: Otomatis isi Jabatan & Departemen jika seluruh data tersedia
                var jabatanId = selectedOption.data('jabatanid');
                var departemenId = selectedOption.data('departemenid');
                if (jabatanId !== undefined && jabatanId != "") {
                    var $jabSelect = $('select.jabatan-penilai-select[data-row-index="' + rowIndex + '"]');
                    $jabSelect.val(jabatanId).trigger('change');
                }
                if (departemenId !== undefined && departemenId != "") {
                    var $depSelect = $('select.departemen-penilai-select[data-row-index="' + rowIndex +
                        '"]');
                    $depSelect.val(departemenId).trigger('change');
                }
            });
            $('#btnKonfirmasiAjukan').on('click', function(e) {
                // Validasi: pastikan semua select dan email bertanda required sudah terisi
                var isValid = true;
                $('#modalPenilai')
                    .find('select[required], input[required]')
                    .each(function() {
                        if (!$(this).val()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });
                if (!isValid) {
                    Swal.fire({
                        title: 'Ada data yang belum diisi',
                        text: 'Mohon lengkapi semua field yang wajib diisi!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengajukan permintaan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-paper-plane"></i> Ajukan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary ms-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        let seconds = 0;
                        let timerInterval;
                        Swal.fire({
                            title: '<span class="fw-bold">Mengirim...</span>',
                            html: `
                                <div>
                                    <span class="text-secondary d-block mb-2">Permintaan sedang diproses. Mohon tunggu.</span>
                                    <span class="text-danger fw-semibold">Jangan refresh halaman ini.</span>
                                    <div class="mt-2">
                                        <span class="badge bg-secondary">Detik berjalan: <span id="swal-timer-seconds">0</span></span>
                                    </div>
                                </div>`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            showCancelButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                const $timer = Swal.getHtmlContainer().querySelector(
                                    '#swal-timer-seconds');
                                timerInterval = setInterval(function() {
                                    seconds++;
                                    if ($timer) $timer.textContent = seconds;
                                }, 1000);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        });
                        setTimeout(function() {
                            document.getElementById('formPenilai').submit();
                        }, 300);
                    }
                });
            });
        });
    </script>
@endpush
