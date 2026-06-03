<div class="modal fade" id="modalPermintaanPembelian" tabindex="-1" aria-labelledby="modalPermintaanPembelianLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="modalPermintaanPembelianLabel">Permintaan Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table datanew cell-border compact stripe" id="permintaanTable" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nomor</th>
                                <th>Barang</th>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Perusahaan</th>
                                <th>Departemen</th>
                                <th>Diajukan Oleh</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permintaan as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->NomorPermintaan ?? '-' }}</td>
                                    <td>{{ $item->getDetail[0]->getBarang->Nama ?? '-' }}</td>
                                    <td>{{ $item->getJenisPermintaan->Nama ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->Tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ optional($item->getPerusahaan)->Nama ?? '-' }}</td>
                                    <td>{{ optional($item->getDepartemen)->Nama ?? '-' }}</td>
                                    <td>{{ optional($item->getDiajukanOleh)->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('ajukan.create', $item->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-check"></i> Pilih
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
