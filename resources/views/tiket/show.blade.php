@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Detail Tiket</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ticket.index') }}">Daftar Tiket</a></li>
                    <li class="breadcrumb-item active">Detail Tiket</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Informasi Tiket</h4>
                    <p class="card-text mb-0">
                        Berikut adalah detail tiket yang Anda ajukan.
                    </p>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th width="25%">Kode Tiket</th>
                            <td>{{ $tiketTrouble->KodeTiket }}</td>
                        </tr>
                        <tr>
                            <th>Judul</th>
                            <td>{{ $tiketTrouble->Judul }}</td>
                        </tr>
                        <tr>
                            <th>Nama Pengaju</th>
                            <td>{{ $tiketTrouble->Nama }}</td>
                        </tr>
                        <tr>
                            <th>No HP</th>
                            <td>
                                @if($tiketTrouble->NoHp)
                                    @php
                                        // Handle no leading +/country code: assume Indonesia if starts with 0
                                        $nohp = preg_replace('/\D/', '', $tiketTrouble->NoHp); // Remove non-digit
                                        if (substr($nohp, 0, 1) === '0') {
                                            $waNo = '62' . substr($nohp, 1);
                                        } elseif (substr($nohp, 0, 2) === '62') {
                                            $waNo = $nohp;
                                        } else {
                                            $waNo = $nohp; // fallback
                                        }
                                        $waLink = 'https://wa.me/' . $waNo;
                                    @endphp
                                    {{ $tiketTrouble->NoHp }}
                                    <a href="{{ $waLink }}" target="_blank" class="btn btn-success btn-sm ms-2" title="Chat via WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Perusahaan</th>
                            <td>
                                @if($tiketTrouble->getPerusahaan && $tiketTrouble->getPerusahaan->NamaLengkap)
                                    {{ $tiketTrouble->getPerusahaan->NamaLengkap }}
                                @else
                                    {{ $tiketTrouble->KodePerusahaan }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $badgeClass = 'secondary';
                                    switch (strtolower($tiketTrouble->Status)) {
                                        case 'open':
                                            $badgeClass = 'warning';
                                            break;
                                        case 'progress':
                                        case 'in progress':
                                            $badgeClass = 'info';
                                            break;
                                        case 'closed':
                                        case 'done':
                                        case 'selesai':
                                            $badgeClass = 'success';
                                            break;
                                        case 'rejected':
                                        case 'ditolak':
                                            $badgeClass = 'danger';
                                            break;
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $tiketTrouble->Status }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Prioritas</th>
                            <td>{{ $tiketTrouble->Prioritas }}</td>
                        </tr>
                        <tr>
                            <th>File Pendukung</th>
                            <td>
                                @if($tiketTrouble->FilePendukung)
                                    <a href="{{ asset('storage/'.$tiketTrouble->FilePendukung) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa fa-download"></i> Unduh File
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada file</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ \Carbon\Carbon::parse($tiketTrouble->created_at)->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                    {{-- Deskripsi dipindah keluar tabel --}}
                    <div class="mt-4">
                        <h5><strong>Deskripsi</strong></h5>
                        <div class="border rounded p-3 bg-light">
                            {!! $tiketTrouble->Deskripsi !!}
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <a href="{{ route('ticket.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali ke Daftar Tiket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
