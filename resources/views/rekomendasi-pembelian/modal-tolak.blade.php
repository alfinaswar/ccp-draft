<div class="modal fade" id="modal-batalkan" tabindex="-1" aria-labelledby="modalBatalkanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBatalkanLabel">Konfirmasi Penolakan Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h3 class="mb-3">Masukkan Alasan Penolakan</h3>
                </div>
                <form id="form-batalkan" action="{{ route('ajukan.update-status', $data->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="Status" value="Ditolak">
                    <div class="mb-3">
                        <textarea class="ckeditor" name="Keterangan" id="alasanTolak" rows="6" required
                            placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-tolak">Tolak Pengajuan</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(function() {
            $('#btn-confirm-tolak').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Penolakan',
                    text: 'Apakah Anda yakin ingin menolak pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Tolak Pengajuan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#form-batalkan').submit();
                    }
                });
            });
        });
    </script>
@endpush
