<div class="modal fade" id="modalEditRkap" tabindex="-1" aria-labelledby="modalEditRkapLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditRkap" method="post" action="{{ route('rkap.update') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditRkapLabel">Edit RKAP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    {{-- The ID field is now hidden via style to "hide" it from view, but is still submitted --}}
                    <input type="hidden" id="editRkapId" name="id" style="display:none;">
                    <div class="mb-3">
                        <label for="editTahun" class="form-label">Tahun</label>
                        <select class="form-control select2" id="editTahun" name="Tahun" required
                            style="width: 100%;">
                            <option value="">Pilih Tahun</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editNominalMedis" class="form-label">Nominal RKAP Medis</label>
                        <input type="text" class="form-control format-rupiah" id="editNominalMedis"
                            name="NominalRkap" placeholder="Masukkan nominal Medis (misal: 1.000.000)"
                            autocomplete="off" required>
                        <div class="text-muted small mt-1" id="viewEditNominalMedis"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editNominalUmum" class="form-label">Nominal RKAP Umum</label>
                        <input type="text" class="form-control format-rupiah" id="editNominalUmum"
                            name="NominalRkapUmum" placeholder="Masukkan nominal Umum (misal: 1.500.000)"
                            autocomplete="off" required>
                        <div class="text-muted small mt-1" id="viewEditNominalUmum"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        function formatRupiah(angka, prefix) {
            let number_string = angka ? angka.replace(/[^,\d]/g, '').toString() : '',
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        // Populate Tahun options if not present (for demo, 5 years before and after current year)
        function populateTahunOptions(selectedTahun = null) {
            let tahunSelect = $('#editTahun');
            let tahunSekarang = (new Date()).getFullYear();
            let tahunList = [];
            for (let i = tahunSekarang - 5; i <= tahunSekarang + 5; i++) {
                tahunList.push(i);
            }
            let currentVal = selectedTahun;
            tahunSelect.empty();
            tahunSelect.append('<option value="">Pilih Tahun</option>');
            tahunList.forEach(function(tahun) {
                tahunSelect.append('<option value="' + tahun + '">' + tahun + '</option>');
            });

            if (currentVal) {
                tahunSelect.val(currentVal);
            }
        }

        $('#editNominalMedis').on('input', function() {
            let value = $(this).val();
            let formatted = formatRupiah(value, 'Rp');
            $(this).val(formatted);
            $('#viewEditNominalMedis').text(formatted);
        });

        $('#editNominalUmum').on('input', function() {
            let value = $(this).val();
            let formatted = formatRupiah(value, 'Rp');
            $(this).val(formatted);
            $('#viewEditNominalUmum').text(formatted);
        });

        $(document).on('click', '.btn-edit-rkap', function() {
            let encryptedId = $(this).data('id');
            let tahun = $(this).data('tahun');
            let nominalMedis = $(this).data('nominalmedis');
            let nominalUmum = $(this).data('nominalumum');

            // Hide the data-id visually, keep it in the hidden input
            $('#editRkapId').val(encryptedId).hide();

            // Pastikan opsi tahun sudah ada, lalu pilih tahun yang sesuai
            populateTahunOptions(tahun);

            // Format otomatis ke Rupiah untuk input
            let formattedMedis = formatRupiah(nominalMedis ? nominalMedis.toString() : '', 'Rp');
            let formattedUmum = formatRupiah(nominalUmum ? nominalUmum.toString() : '', 'Rp');

            $('#editNominalMedis').val(formattedMedis);
            $('#editNominalUmum').val(formattedUmum);

            // Tampilkan juga format preview jika ada elemen preview
            $('#viewEditNominalMedis').text(formattedMedis);
            $('#viewEditNominalUmum').text(formattedUmum);

            $('#modalEditRkap').modal('show');
        });
    </script>
@endpush
