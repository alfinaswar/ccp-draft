{{-- MODAL PILIH DIREKTUR (Bawaan Bootstrap) - versi besar & info dalam card --}}
<div class="modal fade" id="modalDirektur" tabindex="-1" aria-labelledby="modalDirekturLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg"> {{-- modal-lg membuat modal lebih besar --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDirekturLabel">Pilih Direktur Untuk Informasi Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="card mb-3">
                    <div class="card-body bg-info bg-opacity-10 d-flex align-items-start">
                        <span class="me-2 mt-1">
                            <i class="bi bi-info-circle-fill text-info" style="font-size: 1.4rem;"></i>
                        </span>
                        <div>
                            <p class="mb-0 fw-bold text-black">
                                Silakan pilih direktur yang akan diinformasikan terkait pembelian barang / jasa ini.<br>
                                <span class="fw-bold text-black">Pemilihan ini hanya untuk keperluan notifikasi/informasi.</span>
                            </p>
                        </div>
                    </div>
                </div>


                <div class="mb-3">
                    <label for="selectDirektur" class="form-label">Nama Direktur</label>
                    <select class="form-select select2" id="selectDirektur" required style="width: 100%;">
                        <option value="">-- Pilih Direktur --</option>
                        @foreach ($user as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="errorDirektur" style="display: none;">
                        Nama Direktur wajib dipilih sebelum menyimpan.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnLanjutSimpan">Lanjut Simpan & Kirim</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $('#modalDirektur').on('shown.bs.modal', function () {
        $('#selectDirektur').select2({
            dropdownParent: $('#modalDirektur'),
            width: '100%'
        });
        setTimeout(function(){
            $('#selectDirektur').select2('open');
        }, 350);
    });
    $('#modalDirektur').on('hidden.bs.modal', function () {
        $('#selectDirektur').select2('destroy');
    });
</script>
@endpush
