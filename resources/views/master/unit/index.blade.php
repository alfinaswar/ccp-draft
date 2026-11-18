@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Master Unit</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-sitemap"></i>
                    Master Unit
                </h3>
                <p class="ph-subtitle">
                    Pilih perusahaan untuk mengisi daftar unit kerjanya.
                </p>
            </div>
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-sitemap"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Daftar Perusahaan</h4>
                    <small class="card-text text-muted">Pilih RS untuk melihat dan mengisi unit kerjanya.</small>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover cell-border compact">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Perusahaan</th>
                                    <th width="15%">Kode</th>
                                    <th width="15%">Jumlah Unit</th>
                                    <th width="18%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($perusahaan as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->Nama }}</td>
                                        <td>{{ $p->Kode }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $jumlah[$p->Kode] ?? 0 }} unit</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('unit.perusahaan', $p->Kode) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-list"></i> Kelola Unit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data perusahaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td colspan="2"><strong>Unit Tanpa RS (belum dipetakan)</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $jumlahBersama }} unit</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('unit.perusahaan', 'BERSAMA') }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-list"></i> Kelola Unit
                                        </a>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
