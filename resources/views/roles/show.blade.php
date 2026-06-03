@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Detail Role</h4>
                    <p class="card-text mb-0">
                        Berikut adalah daftar role yang tersedia.
                    </p>
                </div>
                <div class="card-body">
                    <a class="btn btn-secondary mb-3" href="{{ route('roles.index') }}">Kembali</a>
                    <div class="form-group mb-3">
                        <label for="name"><strong>Nama Role:</strong></label>
                        <input type="text" class="form-control" id="name" value="{{ $role->name }}" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label><strong>Permission:</strong></label>
                        <div class="row">
                            @if(!empty($rolePermissions) && count($rolePermissions) > 0)
                                @foreach($rolePermissions as $v)
                                    <div class="col-md-4 mb-2">
                                        <span class="badge bg-success">{{ $v->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <span class="text-muted">Tidak ada permission</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
