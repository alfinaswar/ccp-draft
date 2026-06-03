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
                                              @endphp
                                              <tr>
                                                  <td>Penilai {{ $i + 1 }}</td>
                                                  <td>
                                                      <select name="TipeInputPenilai[]"
                                                          class="form-select tipe-input-penilai"
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
                                                  </td>
                                                  <td>
                                                      <div class="form-master-penilai"
                                                          data-penilai-index="{{ $i + 1 }}"
                                                          @if ($defaultType != 'Master') style="display:none;" @endif>
                                                          <select name="NamaPenilai[]"
                                                              class="form-select select2 penilai-select"
                                                              data-penilai-index="{{ $i + 1 }}">
                                                              <option value="" data-email="" data-jabatanid=""
                                                                  data-departemenid="">
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
                                                      </div>
                                                      <div class="form-manual-penilai"
                                                          data-penilai-index="{{ $i + 1 }}"
                                                          @if ($defaultType != 'Manual') style="display:none;" @endif>
                                                          <input type="text" name="NamaPenilaiManual[]"
                                                              class="form-control" value="{{ $namaText }}"
                                                              placeholder="Nama Penilai {{ $i + 1 }}">
                                                      </div>
                                                  </td>
                                                  <td>
                                                      <input type="email" name="EmailPenilai[]"
                                                          class="form-control email-penilai-input"
                                                          data-penilai-index="{{ $i + 1 }}"
                                                          value="{{ isset($app->Email) ? $app->Email : '' }}"
                                                          placeholder="Email Penilai {{ $i + 1 }}" required>
                                                  </td>
                                                  <td>
                                                      <select name="JabatanId[]"
                                                          class="form-select select2 jabatan-penilai"
                                                          data-penilai-index="{{ $i + 1 }}">
                                                          <option value="">Pilih Jabatan</option>
                                                          @foreach ($jabatan as $jab)
                                                              <option value="{{ $jab->id }}"
                                                                  @if (old('JabatanId.' . $i, $jabatanId) == $jab->id) selected @endif>
                                                                  {{ isset($jab->Nama) ? $jab->Nama : (isset($jab->Nama) ? $jab->Nama : '') }}
                                                              </option>
                                                          @endforeach
                                                      </select>
                                                  </td>
                                                  <td>
                                                      <select name="DepartemenId[]"
                                                          class="form-select select2 departemen-penilai"
                                                          data-penilai-index="{{ $i + 1 }}">
                                                          <option value="">Pilih Departemen</option>
                                                          @foreach ($departemen as $dep)
                                                              <option value="{{ $dep->id }}"
                                                                  @if (old('DepartemenId.' . $i, $departemenId) == $dep->id) selected @endif>
                                                                  {{ isset($dep->Nama) ? $dep->Nama : (isset($dep->nama) ? $dep->nama : '') }}
                                                              </option>
                                                          @endforeach
                                                      </select>
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
                          if (tipe === 'Master') {
                              $row.find('.form-master-penilai[data-penilai-index="' + index + '"]').show();
                              $row.find('.form-manual-penilai[data-penilai-index="' + index + '"]').hide();

                              var option = $row.find('.penilai-select').find('option:selected');
                              var email = option.data('email') || '';
                              var jabatan = option.data('jabatanid') || '';
                              var departemen = option.data('departemenid') || '';

                              $row.find('input.email-penilai-input').val(email);

                              // AKU MAU TETAP BISA DIGANTI: Jabatan & Departemen TIDAK diblok/pointer-events: none
                              $row.find('.jabatan-penilai')
                                  .val(jabatan)
                                  .prop('disabled', false) // <-- tidak disabled!
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
                      $('.penilai-select').on('change', function() {
                          var index = $(this).attr('data-penilai-index');
                          var option = $(this).find('option:selected');
                          var email = option.data('email') || '';
                          var jabatan = option.data('jabatanid') || '';
                          var departemen = option.data('departemenid') || '';

                          $('input.email-penilai-input[data-penilai-index="' + index + '"]').val(email);

                          // AKU MAU TETAP BISA DIGANTI: Jabatan & Departemen TIDAK diblok/pointer-events: none
                          var $jabatan = $('select.jabatan-penilai[data-penilai-index="' + index + '"]');
                          $jabatan.val(jabatan)
                              .prop('disabled', false) // <-- tidak disabled!
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
                                  .prop('disabled', false) // <-- tidak disabled!
                                  .css({
                                      'pointer-events': '',
                                      'background-color': '',
                                      'cursor': ''
                                  })
                                  .trigger('change');
                              $row.find('.departemen-penilai').val(departemen).trigger('change');
                          } else {
                              // Make sure for manual: enable and clean up style
                              $row.find('.jabatan-penilai')
                                  .prop('disabled', false)
                                  .css({
                                      'pointer-events': '',
                                      'background-color': '',
                                      'cursor': ''
                                  });
                          }
                      });

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
