<div class="modal fade" id="modalSinkronDepartemen" tabindex="-1" aria-labelledby="sinkronDepartemenLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Tambahkan modal-lg untuk membesarkan modal -->
        <div class="modal-content text-start"> <!-- Tambahkan text-start untuk rata kiri seluruh isi modal -->
            <form id="formSinkronDepartemen" method="POST" action="{{ route('departemen.sinkron') }}">
                @csrf
                <div class="modal-header text-start">
                    <h5 class="modal-title" id="sinkronDepartemenLabel">Sinkronisasi Data Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-start">
                    <div id="progressSinkronWrapper" style="display:none;">
                        <div class="progress mb-3">
                            <div id="progressSinkronBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 100%">Proses sinkronisasi, mohon tunggu...
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="kode_rs" class="form-label">Pilih RS <span class="text-danger">*</span></label>
                        <select class="form-select" name="Kode" id="kode_rs" required>
                            <option value="" selected disabled>-- Pilih RS --</option>
                            @foreach($perusahaan as $rs)
                                <option value="{{ $rs->Kode }}">{{ $rs->Nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer text-start">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitSinkron">Ya, Sinkronkan</button>
                </div>
            </form>


        </div>
    </div>
</div>
