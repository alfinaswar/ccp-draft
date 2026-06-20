          @push('css')
              <style>
                  /* Style untuk SweetAlert Loading */
                  .swal2-popup {
                      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                      z-index: 1000000 !important;
                  }

                  .swal2-container {
                      z-index: 1000000 !important;
                  }

                  .swal2-html-container b {
                      color: #3085d6;
                      font-weight: 600;
                  }

                  /* Loading overlay untuk block semua interaksi */
                  #loading-overlay {
                      position: fixed !important;
                      top: 0 !important;
                      left: 0 !important;
                      width: 100% !important;
                      height: 100% !important;
                      z-index: 999999 !important;
                      cursor: not-allowed !important;
                      background: rgba(0, 0, 0, 0.5) !important;
                      pointer-events: all !important;
                  }

                  /* Disable text selection saat loading */
                  body.loading-active {
                      user-select: none !important;
                      -webkit-user-select: none !important;
                      -moz-user-select: none !important;
                      -ms-user-select: none !important;
                      overflow: hidden !important;
                  }

                  /* Custom loading animation */
                  .swal2-loading .swal2-icon {
                      animation: swal2-rotate-loading 1.5s linear infinite;
                  }

                  @keyframes swal2-rotate-loading {
                      0% {
                          transform: rotate(0deg);
                      }

                      100% {
                          transform: rotate(360deg);
                      }
                  }

                  /* Peringatan text styling */
                  .swal2-html-container strong {
                      font-size: 14px;
                      animation: blink-warning 1.5s infinite;
                  }

                  @keyframes blink-warning {

                      0%,
                      100% {
                          opacity: 1;
                      }

                      50% {
                          opacity: 0.5;
                      }
                  }

                  /* Timer counter styling */
                  .swal2-html-container small {
                      display: block;
                      margin-top: 10px;
                      color: #666;
                      font-size: 13px;
                  }

                  #timer-counter {
                      color: #3085d6;
                      font-weight: bold;
                      font-size: 14px;
                  }

                  /* Style khusus untuk lock pointer events di baris penilai 3,4,5 */
                  .locked-pointer-events {
                      pointer-events: none !important;
                      background-color: #e9ecef !important;
                  }

                  /* Gaya icon gembok kecil */
                  .lock-icon {
                      font-size: 13px;
                      color: #b1b1b1;
                      margin-left: 4px;
                      vertical-align: middle;
                  }

                  /* Lebih besar untuk area label */
                  .lock-icon-label {
                      font-size: 16px;
                      color: #b1b1b1;
                      margin-left: 4px;
                      vertical-align: middle;
                  }
              </style>
          @endpush
          <div class="modal fade" id="modalPenilai" tabindex="-1" aria-labelledby="modalPenilaiLabel" aria-hidden="true">
              <div class="modal-dialog modal-xxl modal-dialog-centered modal-dialog-scrollable"
                  style="max-width:90vw; align-items: flex-start;">
                  <div class="modal-content" style="margin-top: 3vh;">
                      <div class="modal-header">
                          <h5 class="modal-title" id="modalPenilaiLabel">Isi Data Penilai</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form id="formPenilai" method="POST" action="{{ route('htagpa.simpan-penilai') }}">
                          @csrf
                          <input type="hidden" name="IdPengajuan" value="{{ $data->id }}">
                          <input type="hidden" name="PengajuanItemId"
                              value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                          <input type="hidden" name="IdBarang"
                              value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
                          <div class="modal-body">
                              <div class="alert alert-info mb-4" role="alert">
                                  <strong>Perhatian:</strong>
                                  <ol class="mb-0 ps-4">
                                      <li>Mohon isi nama penilai beserta gelarnya dengan benar.</li>
                                      <li>Mohon masukkan alamat email penilai dengan benar karena HTA akan diajukan
                                          melalui email tersebut.</li>
                                      <li>Mohon pastikan jabatan dan departemen penilai terisi dengan benar.</li>
                                  </ol>
                              </div>
                              <div class="table-responsive">
                                  <table class="table align-middle" style="width:100%;">
                                      <thead class="table-light">
                                          <tr>
                                              <th style="width:90px;">Penilai</th>
                                              <th>Input Tipe</th>
                                              <th>Nama</th>
                                              <th>Email</th>
                                              <th>Jabatan</th>
                                              <th>Departemen</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @php
                                              $jabatans = $jabatans ?? [];
                                              $departemens = $departemens ?? [];
                                              $approvalList =
                                                  is_array($approval) || is_object($approval) ? $approval : [];
                                          @endphp

                                          @foreach ($approvalList as $i => $app)
                                              @php
                                                  $defaultType = isset($app->JenisUser) ? $app->JenisUser : '';
                                                  $namaText = isset($app->Nama) ? $app->Nama : '';
                                                  $jabatanId = isset($app->JabatanId) ? $app->JabatanId : '';
                                                  $departemenId = isset($app->DepartemenId) ? $app->DepartemenId : '';
                                                  $isLocked = $i + 1 >= 3 && $i + 1 <= 5;
                                              @endphp
                                              <tr>
                                                  <td>
                                                      Penilai {{ $i + 1 }}
                                                      @if ($isLocked)
                                                          <i class="fa fa-lock lock-icon-label" title="Terkunci"></i>
                                                      @endif
                                                  </td>
                                                  <td>
                                                      <div style="position:relative;display:flex;align-items:center;">
                                                          <select name="TipeInputPenilai[]"
                                                              class="form-select tipe-input-penilai{{ $isLocked ? ' locked-controls' : '' }}"
                                                              data-penilai-index="{{ $i + 1 }}">
                                                              <option value="Master"
                                                                  @if ($defaultType == 'Master') selected @endif>
                                                                  Dari Data Master
                                                              </option>
                                                              <option value="Manual"
                                                                  @if ($defaultType == 'Manual') selected @endif>
                                                                  Input Manual
                                                              </option>
                                                          </select>
                                                          @if ($isLocked)
                                                              <i class="fa fa-lock lock-icon" title="Terkunci"></i>
                                                          @endif
                                                      </div>
                                                  </td>
                                                  <td>
                                                      <div class="form-master-penilai"
                                                          data-penilai-index="{{ $i + 1 }}"
                                                          @if ($defaultType != 'Master') style="display:none;" @endif>
                                                          <div
                                                              style="position:relative;display:flex;align-items:center;">
                                                              <select name="NamaPenilai[]"
                                                                  class="form-select select2 penilai-select{{ $isLocked ? ' locked-controls' : '' }}"
                                                                  data-penilai-index="{{ $i + 1 }}">
                                                                  <option value="" data-email=""
                                                                      data-jabatanid="" data-departemenid="">
                                                                      Pilih Nama Penilai {{ $i + 1 }}
                                                                  </option>
                                                                  @foreach ($user as $u)
                                                                      <option
                                                                          value="{{ $u->id }},{{ $u->name }}"
                                                                          data-email="{{ $u->email }}"
                                                                          data-jabatanid="{{ $u->jabatan ?? '' }}"
                                                                          data-departemenid="{{ $u->departemen ?? '' }}"
                                                                          @if (isset($app->UserId) && $u->id == $app->UserId) selected @endif>
                                                                          {{ $u->name }}
                                                                      </option>
                                                                  @endforeach
                                                              </select>
                                                              @if ($isLocked)
                                                                  <i class="fa fa-lock lock-icon" title="Terkunci"></i>
                                                              @endif
                                                          </div>
                                                      </div>
                                                      <div class="form-manual-penilai"
                                                          data-penilai-index="{{ $i + 1 }}"
                                                          @if ($defaultType != 'Manual') style="display:none;" @endif>
                                                          <div
                                                              style="position:relative;display:flex;align-items:center;">
                                                              <input type="text" name="NamaPenilaiManual[]"
                                                                  class="form-control{{ $isLocked ? ' locked-controls' : '' }}"
                                                                  value="{{ $namaText }}"
                                                                  placeholder="Nama Penilai {{ $i + 1 }}">
                                                              @if ($isLocked)
                                                                  <i class="fa fa-lock lock-icon" title="Terkunci"></i>
                                                              @endif
                                                          </div>
                                                      </div>
                                                  </td>
                                                  <td>
                                                      <div style="position:relative;display:flex;align-items:center;">
                                                          <input type="email" name="EmailPenilai[]"
                                                              class="form-control email-penilai-input{{ $isLocked ? ' locked-controls' : '' }}"
                                                              data-penilai-index="{{ $i + 1 }}"
                                                              value="{{ isset($app->Email) ? $app->Email : '' }}"
                                                              placeholder="Email Penilai {{ $i + 1 }}" required>
                                                          @if ($isLocked)
                                                              <i class="fa fa-lock lock-icon" title="Terkunci"></i>
                                                          @endif
                                                      </div>
                                                  </td>
                                                  <td>
                                                      <div style="position:relative;display:flex;align-items:center;">
                                                          <select name="JabatanId[]"
                                                              class="form-select select2 jabatan-penilai{{ $isLocked ? ' locked-controls' : '' }}"
                                                              data-penilai-index="{{ $i + 1 }}">
                                                              <option value="">Pilih Jabatan</option>
                                                              @foreach ($jabatan as $jab)
                                                                  <option value="{{ $jab->id }}"
                                                                      @if (old('JabatanId.' . $i, $jabatanId) == $jab->id) selected @endif>
                                                                      {{ isset($jab->Nama) ? $jab->Nama : (isset($jab->Nama) ? $jab->Nama : '') }}
                                                                  </option>
                                                              @endforeach
                                                          </select>
                                                          @if ($isLocked)
                                                              <i class="fa fa-lock lock-icon" title="Terkunci"></i>
                                                          @endif
                                                      </div>
                                                  </td>
                                                  <td>
                                                      <div style="position:relative;display:flex;align-items:center;">
                                                          <select name="DepartemenId[]"
                                                              class="form-select select2 departemen-penilai{{ $isLocked ? ' locked-controls' : '' }}"
                                                              data-penilai-index="{{ $i + 1 }}">
                                                              <option value="">Pilih Departemen</option>
                                                              @foreach ($departemen as $dep)
                                                                  <option value="{{ $dep->id }}"
                                                                      @if (old('DepartemenId.' . $i, $departemenId) == $dep->id) selected @endif>
                                                                      {{ isset($dep->Nama) ? $dep->Nama : (isset($dep->nama) ? $dep->nama : '') }}
                                                                  </option>
                                                              @endforeach
                                                          </select>
                                                          @if ($isLocked)
                                                              <i class="fa fa-lock lock-icon" title="Terkunci"></i>
                                                          @endif
                                                      </div>
                                                  </td>
                                              </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                  <i class="fa fa-times me-1"></i> Tutup
                              </button>
                              <button type="submit" class="btn btn-primary" id="btnKonfirmasiAjukan">
                                  <i class="fa fa-paper-plane me-1"></i> Konfirmasi Ajukan
                              </button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
          @push('js')
              <script>
                  $(document).ready(function() {
                      function initHtaGpaSelect2() {
                          $('.select2').each(function() {
                              if ($(this).hasClass("select2-hidden-accessible")) {
                                  $(this).select2('destroy');
                              }
                          });
                          $('.select2').select2({
                              dropdownParent: $('#modalPenilai'),
                              width: '100%',
                              placeholder: function() {
                                  return $(this).attr('placeholder') || 'Pilih...';
                              },
                              allowClear: true
                          });
                      }
                      initHtaGpaSelect2();
                      $('#modalPenilai').on('shown.bs.modal', function() {
                          initHtaGpaSelect2();
                      });
                      let isSubmitting = false;

                      // Fungsi utama untuk lock kolom urutan 3,4,5 tidak bisa diganti (handle pointer events saja)
                      function lockUrutan345Fields() {
                          // Untuk select2, perlu lock container juga
                          // Lock di select dan input pada baris 3,4,5
                          for (let no of [3, 4, 5]) {
                              // tipe input select
                              $('select.tipe-input-penilai[data-penilai-index="' + no + '"]').addClass(
                                  'locked-pointer-events');
                              // nama master select
                              $('select.penilai-select[data-penilai-index="' + no + '"]').addClass('locked-pointer-events');
                              // nama manual input
                              $('input[name="NamaPenilaiManual[]"]').eq(no - 1).addClass('locked-pointer-events');
                              // email
                              $('input.email-penilai-input[data-penilai-index="' + no + '"]').addClass(
                                  'locked-pointer-events');
                              // jabatan
                              $('select.jabatan-penilai[data-penilai-index="' + no + '"]').addClass('locked-pointer-events');
                              // departemen
                              $('select.departemen-penilai[data-penilai-index="' + no + '"]').addClass(
                                  'locked-pointer-events');
                          }
                          // Nonaktifkan event pada elemen locked
                          $('.locked-pointer-events').on('mousedown focus click keydown keyup input', function(e) {
                              e.preventDefault();
                              return false;
                          });
                          // Untuk select2, handler click di container
                          $('.locked-pointer-events').each(function() {
                              if ($(this).hasClass('select2')) {
                                  let idx = $(this).data('penilai-index');
                                  // Nonaktifkan click pada Select2
                                  $('.select2-container[data-select2-id]').each(function() {
                                      // Cari yang punya select dengan data-penilai-index yg sama
                                      let $s = $(this).prev('select[data-penilai-index="' + idx + '"]');
                                      if ($s.length) {
                                          $(this).css('pointer-events', 'none');
                                      }
                                  });
                              }
                          });
                          // Tampilkan icon gembok jika belum muncul (backup dari HTML, antisipasi dinamis)
                          for (let no of [3, 4, 5]) {
                              // setiap kolom yg dikunci, cek adakah lock-icon, jika tidak, append (untuk dinamis js, walau sudah ada di blade)
                              // Input Tipe
                              let $tipe = $('select.tipe-input-penilai[data-penilai-index="' + no + '"]');
                              if ($tipe.closest('div').find('.lock-icon').length === 0) {
                                  $tipe.closest('div').append('<i class="fa fa-lock lock-icon" title="Terkunci"></i>');
                              }
                              // Form master select
                              let $masterRow = $('.form-master-penilai[data-penilai-index="' + no +
                                  '"] select.penilai-select');
                              if ($masterRow.length && $masterRow.closest('div').find('.lock-icon').length === 0) {
                                  $masterRow.closest('div').append('<i class="fa fa-lock lock-icon" title="Terkunci"></i>');
                              }
                              // Manual input
                              let $manualRow = $('.form-manual-penilai[data-penilai-index="' + no +
                                  '"] input[name="NamaPenilaiManual[]"]');
                              if ($manualRow.length && $manualRow.closest('div').find('.lock-icon').length === 0) {
                                  $manualRow.closest('div').append('<i class="fa fa-lock lock-icon" title="Terkunci"></i>');
                              }
                              // Email
                              let $email = $('input.email-penilai-input[data-penilai-index="' + no + '"]');
                              if ($email.length && $email.closest('div').find('.lock-icon').length === 0) {
                                  $email.closest('div').append('<i class="fa fa-lock lock-icon" title="Terkunci"></i>');
                              }
                              // Jabatan
                              let $jabatan = $('select.jabatan-penilai[data-penilai-index="' + no + '"]');
                              if ($jabatan.length && $jabatan.closest('div').find('.lock-icon').length === 0) {
                                  $jabatan.closest('div').append('<i class="fa fa-lock lock-icon" title="Terkunci"></i>');
                              }
                              // Departemen
                              let $departemen = $('select.departemen-penilai[data-penilai-index="' + no + '"]');
                              if ($departemen.length && $departemen.closest('div').find('.lock-icon').length === 0) {
                                  $departemen.closest('div').append('<i class="fa fa-lock lock-icon" title="Terkunci"></i>');
                              }
                          }
                      }

                      lockUrutan345Fields();

                      // --- PATCH: reapply lock kalau select2 direinit saat shown modal
                      $('#modalPenilai').on('shown.bs.modal', function() {
                          setTimeout(lockUrutan345Fields, 300);
                      });

                      function disableAllInteractions() {
                          isSubmitting = true;

                          // Disable klik kanan
                          $(document).on('contextmenu.loading', function(e) {
                              e.preventDefault();
                              return false;
                          });

                          // Disable semua keyboard shortcuts
                          $(document).on('keydown.loading', function(e) {
                              // Block F5 (refresh)
                              if (e.keyCode === 116) {
                                  e.preventDefault();
                                  return false;
                              }
                              // Block Ctrl+R (refresh)
                              if ((e.ctrlKey || e.metaKey) && e.keyCode === 82) {
                                  e.preventDefault();
                                  return false;
                              }
                              // Block Ctrl+W (close tab)
                              if ((e.ctrlKey || e.metaKey) && e.keyCode === 87) {
                                  e.preventDefault();
                                  return false;
                              }
                              // Block Ctrl+F4 (close tab)
                              if (e.ctrlKey && e.keyCode === 115) {
                                  e.preventDefault();
                                  return false;
                              }
                              // Block Alt+F4 (close window)
                              if (e.altKey && e.keyCode === 115) {
                                  e.preventDefault();
                                  return false;
                              }
                              // Block ESC
                              if (e.keyCode === 27) {
                                  e.preventDefault();
                                  return false;
                              }
                              // Block semua keyboard input lainnya
                              e.preventDefault();
                              return false;
                          });

                          // Disable mouse wheel
                          $(document).on('mousewheel.loading DOMMouseScroll.loading', function(e) {
                              e.preventDefault();
                              return false;
                          });

                          // Disable text selection
                          $('body').css({
                              'user-select': 'none',
                              '-webkit-user-select': 'none',
                              '-moz-user-select': 'none',
                              '-ms-user-select': 'none'
                          });

                          // Add overlay untuk block semua klik
                          if ($('#loading-overlay').length === 0) {
                              $('body').append(
                                  '<div id="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;cursor:not-allowed;background:rgba(0,0,0,0.3);"></div>'
                              );
                          }
                      }

                      // Fungsi untuk enable kembali interaksi (backup, case jika ada error)
                      function enableAllInteractions() {
                          isSubmitting = false;
                          $(document).off('.loading');
                          $('body').css({
                              'user-select': '',
                              '-webkit-user-select': '',
                              '-moz-user-select': '',
                              '-ms-user-select': ''
                          });
                          $('#loading-overlay').remove();
                      }

                      // Ganti tipe input antara master & manual
                      $('.tipe-input-penilai').on('change', function() {
                          var index = $(this).data('penilai-index');
                          var tipe = $(this).val();
                          let $row = $(this).closest('tr');
                          // Patch: Jika locked, block event
                          if ([3, 4, 5].includes(Number(index))) {
                              // Balikin ke default value:
                              var defaultVal = $(this).find('option[selected]').val() || $(this).data('default');
                              $(this).val(defaultVal).trigger('change.select2');
                              return false;
                          }
                          if (tipe === 'Master') {
                              $row.find('.form-master-penilai[data-penilai-index="' + index + '"]').show();
                              $row.find('.form-manual-penilai[data-penilai-index="' + index + '"]').hide();

                              var option = $row.find('.penilai-select').find('option:selected');
                              var email = option.data('email') || '';
                              var jabatan = option.data('jabatanid') || '';
                              var departemen = option.data('departemenid') || '';

                              $row.find('input.email-penilai-input').val(email);

                              $row.find('.jabatan-penilai')
                                  .val(jabatan)
                                  .prop('disabled', false)
                                  .css({
                                      'pointer-events': '',
                                      'background-color': '',
                                      'cursor': ''
                                  })
                                  .trigger('change');
                              $row.find('.departemen-penilai').val(departemen).trigger('change');
                          } else {
                              $row.find('.form-master-penilai[data-penilai-index="' + index + '"]').hide();
                              $row.find('.form-manual-penilai[data-penilai-index="' + index + '"]').show();
                              $row.find('input.email-penilai-input').val('');
                              $row.find('.jabatan-penilai')
                                  .val('')
                                  .prop('disabled', false)
                                  .css({
                                      'pointer-events': '',
                                      'background-color': '',
                                      'cursor': ''
                                  })
                                  .trigger('change');
                              $row.find('.departemen-penilai').val('').trigger('change');
                          }
                      });

                      // Sync email, jabatan, departemen saat pilih dari master
                      $('.penilai-select').on('change', function(e) {
                          var index = $(this).attr('data-penilai-index');
                          if ([3, 4, 5].includes(Number(index))) {
                              // Block change di locked urutan
                              e.preventDefault();
                              return false;
                          }
                          var option = $(this).find('option:selected');
                          var email = option.data('email') || '';
                          var jabatan = option.data('jabatanid') || '';
                          var departemen = option.data('departemenid') || '';

                          $('input.email-penilai-input[data-penilai-index="' + index + '"]').val(email);

                          var $jabatan = $('select.jabatan-penilai[data-penilai-index="' + index + '"]');
                          $jabatan.val(jabatan)
                              .prop('disabled', false)
                              .css({
                                  'pointer-events': '',
                                  'background-color': '',
                                  'cursor': ''
                              })
                              .trigger('change');
                          $('select.departemen-penilai[data-penilai-index="' + index + '"]').val(departemen).trigger(
                              'change');
                      });

                      // Default: Jika master, set email/jabatan/departemen otomatis sesuai pilihan nama penilai
                      $('.tipe-input-penilai').each(function() {
                          var $select = $(this);
                          var index = $select.data('penilai-index');
                          var $row = $select.closest('tr');
                          if ($select.val() === 'Master') {
                              var option = $row.find('.penilai-select').find('option:selected');
                              var email = option.data('email') || '';
                              var jabatan = option.data('jabatanid') || '';
                              var departemen = option.data('departemenid') || '';

                              $row.find('input.email-penilai-input').val(email);
                              $row.find('.jabatan-penilai')
                                  .val(jabatan)
                                  .prop('disabled', false)
                                  .css({
                                      'pointer-events': '',
                                      'background-color': '',
                                      'cursor': ''
                                  })
                                  .trigger('change');
                              $row.find('.departemen-penilai').val(departemen).trigger('change');
                          } else {
                              $row.find('.jabatan-penilai')
                                  .prop('disabled', false)
                                  .css({
                                      'pointer-events': '',
                                      'background-color': '',
                                      'cursor': ''
                                  });
                          }
                      });

                      // Lock form element urutan 3,4,5 sekali lagi setelah semua default sync
                      lockUrutan345Fields();

                      // SweetAlert konfirmasi submit dengan loading super ketat
                      $('#formPenilai').on('submit', function(e) {
                          e.preventDefault();

                          // Jika sudah submitting, return
                          if (isSubmitting) {
                              return false;
                          }

                          var form = this;

                          Swal.fire({
                              title: 'Konfirmasi Ajukan?',
                              text: 'Apakah Anda yakin data penilai sudah benar dan ingin mengirim HTA ke email penilai?',
                              icon: 'question',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Ya, ajukan!',
                              cancelButtonText: 'Batal'
                          }).then((result) => {
                              if (result.isConfirmed) {
                                  // Aktifkan semua proteksi
                                  disableAllInteractions();

                                  // Tampilkan loading yang tidak bisa ditutup
                                  Swal.fire({
                                      title: 'Mengirim Email...',
                                      html: '<div style="margin: 20px 0;"><i class="fa fa-envelope fa-3x" style="color: #3085d6;"></i></div>' +
                                          'Mohon tunggu, sedang mengirim email ke penilai.<br>' +
                                          '<strong style="color: #d33; margin-top: 15px; display: block;">JANGAN tutup atau refresh halaman ini!</strong><br>' +
                                          '<small>Waktu tunggu: <b id="timer-counter">0</b> detik</small>',
                                      icon: 'info',
                                      allowOutsideClick: false,
                                      allowEscapeKey: false,
                                      allowEnterKey: false,
                                      showConfirmButton: false,
                                      showCancelButton: false,
                                      didOpen: () => {
                                          Swal.showLoading();

                                          // Timer untuk menampilkan waktu tunggu
                                          let seconds = 0;
                                          const timerInterval = setInterval(() => {
                                              seconds++;
                                              const counterEl = document.getElementById(
                                                  'timer-counter');
                                              if (counterEl) {
                                                  counterEl.textContent = seconds;
                                              }
                                          }, 1000);

                                          // Simpan interval
                                          Swal.getPopup().timerInterval = timerInterval;
                                      },
                                      willClose: () => {
                                          if (Swal.getPopup().timerInterval) {
                                              clearInterval(Swal.getPopup().timerInterval);
                                          }
                                      }
                                  });

                                  // Submit form setelah delay kecil untuk memastikan UI update
                                  setTimeout(function() {
                                      form.submit();
                                  }, 100);
                              }
                          });

                          return false;
                      });
                  });
              </script>
          @endpush
