@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Input Penilaian HTA</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">HTA</a></li>
                    <li class="breadcrumb-item active">Isi Penilaian HTA</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informasi Barang</h4>
                </div>
                <div class="card-body pb-2">
                    <div class="row mb-2">
                        <div class="col-md-2 fw-bold">Kode Barang</div>
                        <div class="col-md-4">: {{ $dataBarang?->KodeBarang ?? 'Demo Kode Barang' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 fw-bold">Nama Barang</div>
                        <div class="col-md-4">: {{ $dataBarang?->Nama ?? 'Demo Alat Kesehatan' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 fw-bold">Merek</div>
                        <div class="col-md-2">: {{ $dataBarang?->getMerk?->Nama ?? 'Demo Merek' }}</div>
                    </div>
                </div>
            </div>

            <form id="formHTA" action="{{ route('hta.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            HTA (Healt Technology Assesment)
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs tab-style-1 d-sm-flex d-block mb-3" id="vendorTab" role="tablist">
                            @foreach ($data->getPengajuan->getVendor as $vnIdx => $vendor)
                                <li class="nav-item">
                                    <a class="nav-link @if ($loop->first) active @endif"
                                        id="vendor{{ $vnIdx }}-tab" data-bs-toggle="tab"
                                        data-bs-target="#vendor{{ $vnIdx }}" href="#vendor{{ $vnIdx }}"
                                        role="tab" aria-controls="vendor{{ $vnIdx }}"
                                        @if ($loop->first) aria-selected="true" @else aria-selected="false" @endif>
                                        {{ $vendor->getNamaVendor->Nama ?? 'Vendor' . ($vnIdx + 1) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content" id="vendorTabContent">
                            @foreach ($data->getPengajuan->getVendor as $vnIdx => $vendor)
                                <div class="tab-pane fade @if ($loop->first) show active @endif"
                                    id="vendor{{ $vnIdx }}" role="tabpanel"
                                    aria-labelledby="vendor{{ $vnIdx }}-tab">
                                    <input type="hidden" name="vendor[{{ $vnIdx }}][IdPengajuan]"
                                        value="{{ $data->IdPengajuan }}">
                                    <input type="hidden" name="vendor[{{ $vnIdx }}][IdBarang]"
                                        value="{{ $data->IdBarang }}">
                                    <input type="hidden" name="vendor[{{ $vnIdx }}][IdHtaGpa]"
                                        value="{{ $data->id }}">
                                    <div class="mb-3">
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-2 fw-bold">Nama Vendor</div>
                                            <div class="col-md-10">
                                                : {{ $vendor->getNamaVendor->Nama ?? '-' }}
                                                <input type="hidden" name="vendor[{{ $vnIdx }}][NamaVendor]"
                                                    value="{{ $vendor->getNamaVendor->id ?? '-' }}" readonly>
                                                <input type="hidden" name="vendor[{{ $vnIdx }}][VendorKe]"
                                                    value="{{ $vnIdx + 1 ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-2 fw-bold">Alamat</div>
                                            <div class="col-md-10">
                                                : {{ $vendor->getNamaVendor->Alamat ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-2 fw-bold">Email</div>
                                            <div class="col-md-10">
                                                : {{ $vendor->getNamaVendor->Email ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-2 fw-bold">Telepon</div>
                                            <div class="col-md-10">
                                                : {{ $vendor->getNamaVendor->Telepon ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:18%;">Kriteria / Parameter</th>
                                                <th>Deskripsi Penilaian</th>
                                                <th style="width:400px;">Nilai (Masing-masing Penilai, Maks: 5)</th>
                                                <th style="width:120px;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($Parameter->getForm->Parameter as $i => $param)
                                                <tr>
                                                    <td>
                                                        .  <select class="form-select"
                                                            name="vendor[{{ $vnIdx }}][parameter][{{ $i }}][Parameter]"
                                                            style="width:100%; background-color:#e9ecef; pointer-events:none;"
                                                            tabindex="-1">
                                                            <option value="">Pilih Parameter</option>
                                                            @foreach ($MasterParameter as $mp)
                                                                <option value="{{ $mp->id }}"
                                                                    @if ($i == $loop->index) selected @endif>
                                                                    {{ $mp->Nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <style>
                                                            select.not-selectable {
                                                                pointer-events: none;
                                                                user-select: none;
                                                                background-color: #e9ecef;
                                                            }
                                                        </style>
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control" name="vendor[{{ $vnIdx }}][parameter][{{ $i }}][Deskripsi]"
                                                            rows="5" placeholder="Tulis deskripsi penilaian..."></textarea>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @for ($pn = 1; $pn <= 5; $pn++)
                                                                <input type="number"
                                                                    class="form-control d-inline-block mb-1 vendor{{ $vnIdx }}-score-{{ $i }}"
                                                                    style="width: 70px" min="0" max="5"
                                                                    placeholder="N{{ $pn }} (maks:5)"
                                                                    name="vendor[{{ $vnIdx }}][parameter][{{ $i }}][Nilai{{ $pn }}]"
                                                                    oninput="updateSubtotal('vendor{{ $vnIdx }}', {{ $i }})"
                                                                    autocomplete="off">
                                                            @endfor
                                                        </div>
                                                        <div class="form-text" style="font-size:12px;">
                                                            Nilai maksimal untuk setiap penilai adalah <strong>5</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold"
                                                            id="vendor{{ $vnIdx }}-subtotal-{{ $i }}">0</span>
                                                        <input type="hidden"
                                                            name="vendor[{{ $vnIdx }}][parameter][{{ $i }}][Subtotal]"
                                                            id="vendor{{ $vnIdx }}-subtotal-input-{{ $i }}"
                                                            value="0">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-light">
                                                <td colspan="3"><span class="fw-bold">Total</span></td>
                                                <td>
                                                    <span class="fw-bold" id="vendor{{ $vnIdx }}-total">0</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Simpan Penilaian HTA</button>
                            <a href="#" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#4BCC1F',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#4BCC1F',
                });
            }, 500);
        </script>
    @endif

    <script>
        @php
            $rowCount = count($Parameter->getForm->Parameter);
            $penilaiMaxNilai = 5; // Jumlah penilai adalah 5
        @endphp

        function calcSubtotalsAndTotal(vendorId) {
            let total = 0;
            // Loop setiap row kriteria (baris parameter per vendor tab)
            for (let i = 0; i < {{ $rowCount }}; i++) {
                let subtotal = 0;
                // Ambil semua input nilai pada row ini untuk vendor tsb
                let inputs = document.querySelectorAll('.' + vendorId + '-score-' + i);
                // Pastikan nodeList bukan kosong
                if (inputs.length) {
                    inputs.forEach(inp => {
                        let val = Number(inp.value);
                        // Pastikan nilai maksimalnya adalah 5 (maksimal per penilai)
                        if (val > 5) {
                            val = 5;
                            inp.value = 5;
                        }
                        if (val < 0 || isNaN(val)) {
                            val = 0;
                            inp.value = '';
                        }
                        subtotal += val;
                    });
                }
                // Tampilkan subtotal di span dan hidden input
                let subtotalSpan = document.getElementById(vendorId + '-subtotal-' + i);
                if (subtotalSpan) subtotalSpan.textContent = subtotal;
                let subtotalInput = document.getElementById(vendorId + '-subtotal-input-' + i);
                if (subtotalInput) subtotalInput.value = subtotal;
                total += subtotal;
            }
            // Set total untuk tab vendor ini
            let totalElem = document.getElementById(vendorId + '-total');
            if (totalElem) totalElem.textContent = total;
        }

        function updateSubtotal(vendorId, idx) {
            calcSubtotalsAndTotal(vendorId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                let tabCount = document.querySelectorAll('#vendorTab .nav-link').length;
                for (let v = 0; v < tabCount; v++) {
                    calcSubtotalsAndTotal('vendor' + v);
                }
                // Atur input maximal attribute menjadi 5 (redundant karena HTML sudah ada, tapi tambah lagi)
                let allInputs = document.querySelectorAll('input[type=number][name*="[Nilai"]');
                allInputs.forEach(function(inp) {
                    inp.setAttribute('max', 5);
                });
            }, 200);
        });

        document.addEventListener('shown.bs.tab', function(e) {
            let idtab = e.target.getAttribute('id'); // id="vendorX-tab"
            if (idtab) {
                let vIdx = idtab.match(/vendor(\d+)-tab/);
                if (vIdx) {
                    calcSubtotalsAndTotal('vendor' + vIdx[1]);
                }
            }
        });
    </script>
