@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">

            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('roles.index') }}">Role</a></li>
                    <li class="active">Manajemen Role</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-users-cog"></i>
                    Manajemen Role
                </h3>
                <p class="ph-subtitle">Kelola role dan atur permission sesuai kebutuhan akses pengguna.</p>
            </div>

            <!-- KANAN: Icon + Tombol Kembali -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-id-badge"></i>
                </div>
                <a href="{{ route('home') }}" class="ph-btn">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>

        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert"
            style="font-size: 1.1rem;">
            <div class="d-flex align-items-center">
                <i class="fa fa-check-circle me-2" style="font-size: 1.5rem; color: #198754;"></i>
                <div>
                    <strong>Sukses!</strong> {{ $message }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-0">Daftar Role</h4>
                        <p class="card-text mb-0">
                            Berikut adalah daftar role yang tersedia.
                        </p>
                    </div>
                    <div class="text-end">
                        <a class="btn btn-primary" href="{{ route('roles.create') }}">
                            <i class="fa fa-plus"></i> Buat Role Baru
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama Role</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}">
                                                <i class="fa fa-eye"></i> Lihat
                                            </a>

                                            <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>

                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus role ini?')">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
