<div class="modal fade" id="modalTanggalPresentasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formTanggalPresentasi" method="POST" action="">
            @csrf

            <input type="hidden" name="id" class="presentasi_id" />

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Tanggal Presentasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5 class="text-center mb-3">Silakan atur tanggal presentasi untuk pengajuan ini.</h5>

                    <div class="mb-2">
                        <label for="presentasi_kode" class="mb-2 font-weight-bold"><b>Kode Pengajuan :</b></label>
                        <input type="text" class="form-control presentasi_kode" id="presentasi_kode" readonly />
                    </div>

                    <div class="form-group mb-2">
                        <label for="TanggalPresentasi" class="mb-2 font-weight-bold"><b>Tanggal Presentasi</b></label>
                        <input type="date" name="TanggalPresentasi" id="TanggalPresentasi" class="form-control"
                            required />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary btn-batal" data-dismiss="modal">Batal</button>
                </div>
            </div>

        </form>
    </div>
</div>
@push('js')
    <script>
        $(document).ready(function() {
            var intervalLoading = null;
            var detik = 0;

            $(document).on('click', '.btn-set-presentasi', function() {
                let id = $(this).data('id');
                let kode = $(this).data('kode');

                $('.presentasi_id').val(id);
                $('.presentasi_kode').val(kode);
                let actionUrl = "{{ route('rekomendasi.update-tanggal-presentasi', ['id' => ':id']) }}";
                actionUrl = actionUrl.replace(':id', id);
                $('#formTanggalPresentasi').attr('action', actionUrl);

                $('#modalTanggalPresentasi').modal('show');
            });

            $('#modalTanggalPresentasi').on('hidden.bs.modal', function() {
                $('#formTanggalPresentasi')[0].reset();
                $('#formTanggalPresentasi').attr('action', '');
            });

            $(document).on('click', '.btn-batal', function() {
                $('#modalTanggalPresentasi').modal('hide');
            });

            $('#formTanggalPresentasi').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var tanggal = $('#TanggalPresentasi').val();

                // Validasi tanggal tidak boleh kosong
                if (!tanggal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Tanggal presentasi harus diisi!',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }

                // Format tanggal untuk ditampilkan (Indonesia)
                var tanggalFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Tampilkan SweetAlert2 Konfirmasi
                Swal.fire({
                    title: 'Konfirmasi Presentasi',
                    text: `Apakah Anda yakin ingin melakukan presentasi pada ${tanggalFormatted}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-paper-plane"></i> Ya, Simpan dan Kirim!',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        popup: 'swal-wide'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        detik = 0;
                        Swal.fire({
                            title: 'Sedang Mengirim Email',
                            html: `
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <h5 class="mb-3">Mohon tunggu sebentar...</h5>
                                    <div class="alert alert-info" style="font-size: 14px;">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Jangan refresh atau tutup halaman ini!</strong>
                                    </div>
                                    <p class="mt-3 mb-0">
                                        Email sedang dikirim ke <strong>Ir. H. Arfan Awaloeddin</strong>
                                    </p>
                                    <p class="mt-2 mb-0">
                                        Waktu berlalu: <span id="detik-timer-presentasi" style="font-weight: bold; color: #0d6efd; font-size: 20px;">0</span> detik
                                    </p>
                                </div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                if (intervalLoading) clearInterval(intervalLoading);
                                intervalLoading = setInterval(function() {
                                    detik++;
                                    $('#detik-timer-presentasi').text(detik);
                                }, 1000);
                                form.submit();
                            }
                        });
                    }
                });
            });

            $(window).on('beforeunload', function() {
                if (intervalLoading) {
                    clearInterval(intervalLoading);
                }
            });
        });
    </script>
@endpush
