@extends('layouts.app')

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="card-title mb-0">Daftar Jenis Formulir Perusahaan</h4>
            </div>
            <div class="card-body">
                <!-- Tampilkan Nama Rumah Sakit -->
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert" style="font-size: 1.15rem;">
                    <i class="fa fa-hospital-o fa-2x me-3 text-primary"></i>
                    <div>
                        <strong>Rumah Sakit Terpilih:</strong>
                        <span class="ms-2 text-uppercase text-primary fw-bold">
                            {{ $perusahaan->Nama ?? '-' }}
                        </span>
                    </div>
                </div>
                @if ($form->count() == 0)
                    <div class="alert alert-warning">
                        Tidak ada nama formulir yang tersedia untuk perusahaan ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="perusahaanTable" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Nama Form</th>
                                    <th>Disetujui Oleh</th>
                                    <th style="width: 20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($form as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->Nama }}</td>
                                        <td>
                                            @if ($item->getApproval->count())
                                                <ol class="mb-0 ps-3">
                                                    @foreach ($item->getApproval->sortBy('Urutan') as $approval)
                                                        <li>
                                                            <span class="fw-bold">{{ $approval->Urutan }}.</span>
                                                            {{ $approval->getUser->name ?? 'Diatur Saat Pengajuan' }}
                                                            @if ($approval->getJabatan)
                                                                - <span
                                                                    class="text-muted">{{ $approval->getJabatan->Nama }}</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <span class="text-danger">Belum diatur</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('master-approval.ttd', [encrypt($item->id), encrypt($perusahaan->Kode)]) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-pen"></i> Atur Tanda Tangan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('js')
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ Session::get('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif
@endpush
