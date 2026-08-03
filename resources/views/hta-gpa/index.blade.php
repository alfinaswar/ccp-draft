@extends('layouts.app')

@section('content')
    @push('css')
        <style>
            #scrollToTopBtn,
            #scrollToBottomBtn {
                position: fixed;
                right: 30px;
                width: 48px;
                height: 48px;
                border: none;
                border-radius: 50%;
                background: #4BCC1F;
                color: white;
                font-size: 24px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                cursor: pointer;
                z-index: 999;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: opacity 0.2s;
                opacity: 0.8;
            }

            #scrollToTopBtn:hover,
            #scrollToBottomBtn:hover {
                opacity: 1;
            }

            #scrollToTopBtn {
                bottom: 90px;
                display: none;
            }

            #scrollToBottomBtn {
                bottom: 30px;
                display: none;
            }
        </style>
    @endpush
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Input Penilaian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Hta / Gpa</a></li>
                    <li class="breadcrumb-item active">Isi Penilaian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Penilaian HTA / GPA
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">Informasi Barang</h5>
                        <table class="table align-middle" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:180px;">Nama Barang</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
                                </tr>
                                </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center" role="alert" style="min-height: 70px;">
                        <i class="fa fa-exclamation-circle me-2" style="align-self: center; font-size: 1.6rem;"></i>
                        <div class="d-flex align-items-center" style="min-height: 50px;">
                            <ol class="mb-0 ps-2">
                                <li>Isi HTA secara lengkap untuk setiap Vendor terlebih dahulu. Setelah HTA Vendor ini
                                    diisi, baru bisa melanjutkan ke Vendor berikutnya.</li>
                                <li>Semua kolom HTA wajib diisi.</li>
                                <li>Jika {{ auth()->user()->name }} sedang sibuk, Anda dapat menyimpan data sebagai draft
                                    terlebih dahulu dan melanjutkan pengisian di lain waktu.</li>
                            </ol>
                        </div>
                    </div>
                    <form id="formHtaGpa" action="{{ route('htagpa.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs tab-style-1 d-sm-flex d-block" role="tablist" id="vendorTabs">
                            @foreach ($data->getVendor as $vIdx => $Vendor)
                                @php
                                    // Jangan biarkan tab vendor berikutnya aktif jika tab sebelumnya belum diisi (berdasarkan Nilai1)
                                    $disableTab = false;
                                    if ($vIdx >= 1) {
                                        $prevVendor = $data->getVendor[$vIdx - 1] ?? null;
                                        $prevNilai1 = $prevVendor->getHtaGpa->Nilai1[0] ?? null;
                                        $disableTab = is_null($prevNilai1);
                                    }
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $vIdx === 0 ? 'active' : '' }} {{ $disableTab ? 'disabled' : '' }}"
                                        id="vendor-tab-{{ $vIdx }}"
                                        @if (!$disableTab) data-bs-toggle="tab" href="#vendor-pane-{{ $vIdx }}" role="tab" aria-controls="vendor-pane-{{ $vIdx }}" aria-selected="{{ $vIdx === 0 ? 'true' : 'false' }}" @else tabindex="-1" aria-disabled="true" @endif
                                        style="{{ $disableTab ? 'pointer-events: none; opacity: 0.5;' : '' }}">
                                        {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="vendorTabPanes">
                            @foreach ($data->getVendor as $vIdx => $Vendor)
                                {{-- @php
                                    dd($Vendor);
                                @endphp --}}
                                <div class="tab-pane fade {{ $vIdx === 0 ? 'show active' : '' }}"
                                    id="vendor-pane-{{ $vIdx }}" role="tabpanel"
                                    aria-labelledby="vendor-tab-{{ $vIdx }}">
                                    <input type="hidden" name="vendor[{{ $vIdx }}][IdVendor]"
                                        value="{{ $Vendor->NamaVendor }}">

                                    <input type="hidden" name="vendor[{{ $vIdx }}][IdPengajuan]"
                                        value="{{ $data->id }}">
                                    <input type="hidden" name="vendor[{{ $vIdx }}][PengajuanItemId]"
                                        value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                                    <input type="hidden" name="vendor[{{ $vIdx }}][IdBarang]"
                                        value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
                                    {{-- Additional hidden data if needed --}}

                                    <table class="table align-middle nilai-table" style="width:100%;"
                                        data-vidx="{{ $vIdx }}">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width:10px;">No</th>
                                                <th class="text-center" style="width:17%">Parameter Penilaian</th>
                                                <th class="text-center" style="width:50%">Deskripsi</th>
                                                <th class="text-center" style="width:25%">
                                                    Penilaian
                                                    <br>
                                                    <small class="text-muted">(nilai paling baik = 5 dan nilai paling rendah
                                                        = 1)</small>
                                                </th>
                                                <th class="text-center">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- {{ dd($data->getHtaGpa->getDetailHta) }} --}}
                                            @foreach ($data->getJenisPermintaan->getForm->Parameter as $key => $pm)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <input type="text" value="{{ $parameter[$pm - 1]->Nama }}"
                                                            class="form-control"
                                                            name="vendor[{{ $vIdx }}][Parameter][]" readonly>
                                                        <input type="hidden" value="{{ $pm }}"
                                                            class="form-control"
                                                            name="vendor[{{ $vIdx }}][IdParameter][]" readonly>
                                                    </td>
                                                    <td>
                                                        @if ($pm == 11)
                                                            <input type="text" class="form-control currency-input-global"
                                                                name="vendor[{{ $vIdx }}][Deskripsi][]"
                                                                placeholder="Masukkan nominal (format rupiah)"
                                                                value="{{ isset($Vendor->TotalHarga) ? number_format($Vendor->TotalHarga, 0, ',', '.') : '' }}"
                                                                readonly>
                                                        @elseif($pm == 2)
                                                            <div style="position: relative;">
                                                                <div
                                                                    style="display: flex; justify-content: flex-end; margin-bottom: 4px;">
                                                                    <button type="button" onclick="aiRapikan(this)"
                                                                        data-target="ckeditor_{{ $vIdx }}_{{ $key }}"
                                                                        style="
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                color: white; border: none; padding: 5px 12px;
                border-radius: 6px; font-size: 12px; cursor: pointer;
                display: flex; align-items: center; gap: 6px;
                transition: opacity 0.2s;
            ">
                                                                        ✨ Rapikan
                                                                    </button>
                                                                </div>
                                                                <div id="ai-error-{{ $vIdx }}-{{ $key }}"
                                                                    style="min-height:22px;color:#e53e3e;font-size:12px;font-weight:500;margin-bottom: 2px;">
                                                                </div>
                                                                <textarea class="ckeditor" id="ckeditor_{{ $vIdx }}_{{ $key }}"
                                                                    name="vendor[{{ $vIdx }}][Deskripsi][]" rows="10" placeholder="Masukkan Spesifikasi">{!! $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key] ?? '' !!}</textarea>
                                                            </div>

                                                            @once
                                                                <style>
                                                                    @keyframes spin {
                                                                        to {
                                                                            transform: rotate(360deg);
                                                                        }
                                                                    }

                                                                    .btn-ai-loading {
                                                                        opacity: 0.6;
                                                                        cursor: not-allowed !important;
                                                                    }
                                                                </style>
                                                                <script>
                                                                    const AI_RAPIKAN_URL = "{{ route('ai.rapikan') }}";

                                                                    async function aiRapikan(btn) {
                                                                        const targetId = btn.getAttribute('data-target');

                                                                        let konten = '';
                                                                        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances[targetId]) {
                                                                            konten = CKEDITOR.instances[targetId].getData();
                                                                        } else {
                                                                            konten = document.getElementById(targetId)?.value || '';
                                                                        }

                                                                        if (!konten.trim()) {
                                                                            alert('Textarea masih kosong.');
                                                                            return;
                                                                        }

                                                                        btn.disabled = true;
                                                                        btn.classList.add('btn-ai-loading');
                                                                        btn.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                style="animation: spin 1s linear infinite;">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            Memproses...
        `;

                                                                        try {
                                                                            const res = await fetch(AI_RAPIKAN_URL, {
                                                                                method: 'POST',
                                                                                headers: {
                                                                                    'Content-Type': 'application/json',
                                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                                },
                                                                                body: JSON.stringify({
                                                                                    konten
                                                                                })
                                                                            });

                                                                            const data = await res.json();

                                                                            if (!res.ok || data.error) {
                                                                                // Tampilkan error langsung di bawah button
                                                                                showAiError(btn, `[${res.status}] ${data.error ?? data.message ?? JSON.stringify(data)}`);
                                                                                return;
                                                                            }

                                                                            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances[targetId]) {
                                                                                CKEDITOR.instances[targetId].setData(data.hasil);
                                                                            } else {
                                                                                document.getElementById(targetId).value = data.hasil;
                                                                            }

                                                                        } catch (err) {
                                                                            // Tampilkan error exception
                                                                            showAiError(btn, `Exception: ${err.message}`);
                                                                            console.error(err);
                                                                        } finally {
                                                                            btn.disabled = false;
                                                                            btn.classList.remove('btn-ai-loading');
                                                                            btn.innerHTML = '✨ Rapikan Otomatis';
                                                                        }
                                                                    }

                                                                    function showAiError(btn, message) {
                                                                        // Hapus error sebelumnya jika ada
                                                                        const existing = btn.parentElement.querySelector('.ai-error-msg');
                                                                        if (existing) existing.remove();

                                                                        const el = document.createElement('div');
                                                                        el.className = 'ai-error-msg';
                                                                        el.style.cssText = `
            margin-top: 6px;
            padding: 6px 10px;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            color: #dc2626;
            font-size: 12px;
            word-break: break-all;
        `;
                                                                        el.innerText = '❌ ' + message;

                                                                        // Auto hilang setelah 10 detik
                                                                        btn.parentElement.appendChild(el);
                                                                        setTimeout(() => el.remove(), 10000);
                                                                    }
                                                                </script>
                                                            @endonce
                                                        @else
                                                            <input type="text" class="form-control"
                                                                name="vendor[{{ $vIdx }}][Deskripsi][]"
                                                                placeholder="Masukkan deskripsi"
                                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key] ?? '' }}">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{-- @php
                                                            dd($data->getHtaGpa->getDetailHta[$vIdx]->Nilai1);
                                                        @endphp --}}
                                                        <div class="d-flex gap-1 align-items-end">
                                                            <div class="text-center">
                                                                <label class="form-label mb-1"
                                                                    style="font-size: 11px; font-weight: 600;">Penilai
                                                                    1</label>
                                                                <input type="number" min="0" max="5"
                                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai1[$key] ?? '' }}"
                                                                    class="form-control nilai-input"
                                                                    name="vendor[{{ $vIdx }}][Nilai1][]"
                                                                    style="max-width: 90px;"
                                                                    oninput="if(this.value>5)this.value=5;if(this.value<0)this.value=0;">
                                                            </div>
                                                            <div class="text-center">
                                                                <label class="form-label mb-1"
                                                                    style="font-size: 11px; font-weight: 600;">Penilai
                                                                    2</label>
                                                                <input type="number" min="0" max="5"
                                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai2[$key] ?? '' }}"
                                                                    class="form-control nilai-input"
                                                                    name="vendor[{{ $vIdx }}][Nilai2][]"
                                                                    style="max-width: 90px;"
                                                                    oninput="if(this.value>5)this.value=5;if(this.value<0)this.value=0;">
                                                            </div>
                                                            <div class="text-center">
                                                                <label class="form-label mb-1"
                                                                    style="font-size: 11px; font-weight: 600;">Penilai
                                                                    3</label>
                                                                <input type="number" min="0" max="5"
                                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai3[$key] ?? '' }}"
                                                                    class="form-control nilai-input"
                                                                    name="vendor[{{ $vIdx }}][Nilai3][]"
                                                                    style="max-width: 90px;"
                                                                    oninput="if(this.value>5)this.value=5;if(this.value<0)this.value=0;">
                                                            </div>
                                                            <div class="text-center">
                                                                <label class="form-label mb-1"
                                                                    style="font-size: 11px; font-weight: 600;">Penilai
                                                                    4</label>
                                                                <input type="number" min="0" max="5"
                                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai4[$key] ?? '' }}"
                                                                    class="form-control nilai-input"
                                                                    name="vendor[{{ $vIdx }}][Nilai4][]"
                                                                    style="max-width: 90px;"
                                                                    oninput="if(this.value>5)this.value=5;if(this.value<0)this.value=0;">
                                                            </div>
                                                            <div class="text-center">
                                                                <label class="form-label mb-1"
                                                                    style="font-size: 11px; font-weight: 600;">Penilai
                                                                    5</label>
                                                                <input type="number" min="0" max="5"
                                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai5[$key] ?? '' }}"
                                                                    class="form-control nilai-input"
                                                                    name="vendor[{{ $vIdx }}][Nilai5][]"
                                                                    style="max-width: 90px;"
                                                                    oninput="if(this.value>5)this.value=5;if(this.value<0)this.value=0;">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                            value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}"
                                                            class="form-control subtotal-input"
                                                            name="vendor[{{ $vIdx }}][SubTotal][]" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-end">Grand Total</th>
                                                <th>
                                                    <input type="text" class="form-control grandtotal-input"
                                                        name="vendor[{{ $vIdx }}][GrandTotal]" readonly
                                                        style="font-weight:bold;">
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="col-md-3 col-form-label fw-bold">Umur Ekonomis</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control"
                                                    name="vendor[{{ $vIdx }}][UmurEkonomis]"
                                                    placeholder="Masukkan Umur Ekonomis"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->UmurEkonomis ?? '' }}">
                                            </div>
                                            <label class="col-md-3 col-form-label fw-bold">Tarif Diusulkan</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control rupiah-input"
                                                    name="vendor[{{ $vIdx }}][TarifDiusulkan]"
                                                    placeholder="Masukkan Tarif Diusulkan"
                                                    value="{{ isset($data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan) && is_numeric($data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan) ? number_format((float) $data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan, 0, ',', '.') : '' }}"
                                                    oninput="this.value=formatRupiah(this.value)">
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-md-3 col-form-label fw-bold">Buyback Period</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control"
                                                    name="vendor[{{ $vIdx }}][BuybackPeriod]"
                                                    placeholder="Masukkan Buyback Period"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->BuybackPeriod ?? '' }}">
                                            </div>
                                            <label class="col-md-3 col-form-label fw-bold">Target Pemakaian Bulanan</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control"
                                                    name="vendor[{{ $vIdx }}][TargetPemakaianBulanan]"
                                                    placeholder="Masukkan Target Pemakaian Bulanan"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TargetPemakaianBulanan ?? '' }}">
                                            </div>
                                        </div>
                                        <label class="col-md-3 col-form-label fw-bold">Keterangan</label>
                                        <div class="col-md-12">
                                            <textarea class="form-control" name="vendor[{{ $vIdx }}][Keterangan]" rows="3"
                                                placeholder="Masukkan Keterangan">{!! $data->getHtaGpa->getDetailHta[$vIdx]->Keterangan ?? '' !!}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" name="action" value="draft" class="btn btn-warning me-2">
                                <i class="fa fa-save me-1"></i> Simpan Sebagai Draft
                            </button>
                            <!-- Ajukan Button trigger modal -->
                            @php
                                $showAjukan = false;
                                $filledFileCount = 0;
                                foreach ($data->getHtaGpa->getDetailHta ?? [] as $detail) {
                                    if (!empty($detail->File)) {
                                        $filledFileCount++;
                                    }
                                }
                                if ($filledFileCount >= 2) {
                                    // Cek kalau approval urutan 1 tokennya sudah ada, maka jangan tampilkan tombol
                                    $approvalUrutan1 = null;
                                    if (!empty($approval)) {
                                        foreach ($approval as $item) {
                                            if (($item->Urutan ?? null) == 1) {
                                                $approvalUrutan1 = $item;
                                                break;
                                            }
                                        }
                                    }
                                    if (empty($approvalUrutan1) || empty($approvalUrutan1->ApprovalToken)) {
                                        $showAjukan = true;
                                    }
                                }
                            @endphp

                            @if ($showAjukan)
                                <button type="button" id="btnAjukan" class="btn btn-success me-2"
                                    data-bs-toggle="modal" data-bs-target="#modalPenilai">
                                    <i class="fa fa-paper-plane me-1"></i> Ajukan & Kirim Email
                                </button>
                            @endif


                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <form id="formAjukanHtaGpa" action="{{ route('htagpa.ajukan') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="IdPengajuan" value="{{ $data->id }}">
                <input type="hidden" name="PengajuanItemId" value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                <input type="hidden" name="IdBarang" value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
                <input type="hidden" name="Status" value="Diajukan">
            </form>
            @include('hta-gpa.modal-kirim-email')
        </div>
    </div>


    <button id="scrollToTopBtn" title="Scroll to Top">
        <i class="fa fa-arrow-up"></i>
    </button>
    <button id="scrollToBottomBtn" title="Scroll to Bottom">
        <i class="fa fa-arrow-down"></i>
    </button>
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
    @if (Session::get('error'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#f44336',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#f44336',
                });
            }, 500);
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input.currency-input-global').forEach(function(inp) {
                inp.addEventListener('blur', function() {
                    let val = this.value.replace(/[^0-9]/g, '');
                    if (val) {
                        this.value = parseInt(val).toLocaleString('id-ID');
                    }
                });
                // auto-format on load if there is value
                if (inp.value) {
                    let n = inp.value.replace(/[^0-9]/g, '');
                    if (n) {
                        inp.value = parseInt(n).toLocaleString('id-ID');
                    }
                }
            });
        });
    </script>
    <script>
        function updateSubtotalsAndGrandTotal(vendorTable) {
            let grandTotal = 0;
            vendorTable.find("tbody tr").each(function() {
                let subtotal = 0;
                $(this).find('.nilai-input').each(function() {
                    let nilai = parseFloat($(this).val());
                    if (isNaN(nilai)) nilai = 0;
                    if (nilai > 5) {
                        $(this).val(5);
                        nilai = 5;
                    }
                    if (nilai < 0) {
                        $(this).val(0);
                        nilai = 0;
                    }
                    subtotal += nilai;
                });
                $(this).find('.subtotal-input').val(subtotal);
                grandTotal += subtotal;
            });
            vendorTable.find('.grandtotal-input').val(grandTotal);
        }

        $(document).ready(function() {
            $('.nilai-table').each(function() {
                let vendorTable = $(this);
                updateSubtotalsAndGrandTotal(vendorTable);

                vendorTable.on('input', '.nilai-input', function() {
                    updateSubtotalsAndGrandTotal(vendorTable);
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topBtn = document.getElementById('scrollToTopBtn');
            const bottomBtn = document.getElementById('scrollToBottomBtn');

            function toggleButtons() {
                if (window.scrollY > 150) {
                    topBtn.style.display = 'flex';
                } else {
                    topBtn.style.display = 'none';
                }
                if (window.innerHeight + window.scrollY < document.body.offsetHeight - 150) {
                    bottomBtn.style.display = 'flex';
                } else {
                    bottomBtn.style.display = 'none';
                }
            }

            window.addEventListener('scroll', toggleButtons);
            window.addEventListener('resize', toggleButtons);

            topBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            bottomBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
            });

            // Initial check
            toggleButtons();
        });
    </script>
    <script>
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.rupiah-input').forEach(function(el) {
                el.addEventListener('keyup', function(e) {
                    var caret = el.selectionStart;
                    var val = formatRupiah(this.value);
                    this.value = val;
                    el.setSelectionRange(caret, caret);
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnKonfirmasiKirimUlang');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Kirim Ulang Notifikasi?',
                        text: 'Anda yakin ingin mengirim ulang notifikasi approval yang masih pending?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Mengirim Notifikasi...',
                                html: `<b>Mohon tunggu, proses pengiriman email sedang berlangsung.</b>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal
                                        .showLoading();
                                    setTimeout(() => {
                                        window.location.href = btn.getAttribute(
                                            'href');
                                    }, 500);
                                }
                            });
                        }
                    });
                });
            }
        });
    </script>
@endpush
