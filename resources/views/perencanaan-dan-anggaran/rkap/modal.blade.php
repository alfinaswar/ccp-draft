 <div class="modal fade" id="modalTambahRkap" tabindex="-1" aria-labelledby="modalTambahRkapLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <form id="formTambahRkap" action="{{ route('rkap.store', request()->route('id')) }}" method="post">
                 @csrf
                 <div class="modal-header">
                     <h5 class="modal-title" id="modalTambahRkapLabel">Tambah RKAP</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                 </div>
                 <div class="modal-body">
                     <div class="mb-3">
                         <label for="tahun" class="form-label">Tahun</label>
                         <select class="form-control" id="tahun" name="Tahun" required style="width: 100%;">
                             <option value="">Pilih Tahun</option>
                         </select>
                     </div>

                     <div class="mb-3">
                         <label for="nominalMedis" class="form-label">Nominal RKAP Medis</label>
                         <input type="text" class="form-control format-rupiah" id="nominalMedis" name="NominalRkap"
                             placeholder="Masukkan nominal Medis (misal: 1.000.000)" autocomplete="off" required>
                         <div class="text-muted small mt-1" id="viewNominalMedis"></div>
                     </div>
                     <div class="mb-3">
                         <label for="nominalUmum" class="form-label">Nominal RKAP Umum</label>
                         <input type="text" class="form-control format-rupiah" id="nominalUmum"
                             name="NominalRkapUmum" placeholder="Masukkan nominal Umum (misal: 1.500.000)"
                             autocomplete="off" required>
                         <div class="text-muted small mt-1" id="viewNominalUmum"></div>
                     </div>
                 </div>

                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                     <button type="submit" class="btn btn-primary">Simpan</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
