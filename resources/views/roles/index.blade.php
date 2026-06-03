@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Manajemen Role</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Role</li>
                </ul>
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
    <div class="row mb-3">
        <div class="col text-end">
            <a class="btn btn-primary" href="{{ route('roles.create') }}">Buat Role Baru</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Daftar Role</h4>
                    <p class="card-text mb-0">
                        Berikut adalah daftar role yang tersedia.
                    </p>
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
