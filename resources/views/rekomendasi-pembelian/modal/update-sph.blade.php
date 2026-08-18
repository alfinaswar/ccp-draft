    <!-- Modal Update SPH -->
    <div class="modal fade" id="modalUpdateSph-{{ $vIdx }}" tabindex="-1"
        aria-labelledby="modalUpdateSphLabel-{{ $vIdx }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('rekomendasi.updateSph', ['vendor' => $Vendor->NamaVendor]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateSphLabel-{{ $vIdx }}">Update Surat
                            Penawaran Vendor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="sphFile-{{ $vIdx }}" class="form-label">Upload Surat Penawaran Baru
                                (PDF,
                                max 5MB)</label>
                            <input class="form-control" type="file" name="SuratPenawaranVendor"
                                id="sphFile-{{ $vIdx }}" accept="application/pdf" required>
                        </div>
                        <input type="hidden" name="IdPengajuan" value="{{ $data->id }}">
                        <input type="hidden" name="IdVendor" value="{{ $Vendor->NamaVendor }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Update
                            SPH</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
