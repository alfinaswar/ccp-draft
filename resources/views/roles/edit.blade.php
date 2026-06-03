@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Manajemen Role</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role</a></li>
                    <li class="breadcrumb-item active">Ubah Role</li>
                </ul>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan!</strong> Silakan periksa input Anda.<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0 text-white">Formulir Edit Role</h4>
                    <p class="card-text mb-0 text-white-50">
                        Silakan ubah data role dan permission yang diinginkan.
                    </p>
                </div>
                <div class="card-body">
                    {!! Form::model($role, ['method' => 'PATCH', 'route' => ['roles.update', $role->id]]) !!}
                    <div class="mb-3">
                        <label for="name" class="form-label"><strong>Nama Role</strong></label>
                        {!! Form::text('name', null, ['placeholder' => 'Nama Role', 'class' => 'form-control', 'id' => 'name']) !!}
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Permission</strong></label>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center">
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAllPermissions">
                                            <label class="form-check-label fw-bold" for="checkAllPermissions">Checklist
                                                Semua</label>
                                        </div>
                                    </div>
                                    <div style="min-width:220px;max-width:350px;">
                                        <input type="text" id="permission-search" class="form-control"
                                            placeholder="Cari permission...">
                                    </div>
                                </div>
                                <div class="row" id="permission-list">
                                    @foreach ($permission->sortBy('name') as $value)
                                        <div class="col-md-4 mb-2 permission-item">
                                            <div class="form-check">
                                                {{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions ?? []) ? true : false, [
                                                    'class' => 'form-check-input permission-checkbox',
                                                    'id' => 'perm_' . $value->id,
                                                    'data-perm-name' => strtolower($value->name),
                                                ]) }}
                                                <label class="form-check-label" for="perm_{{ $value->id }}">
                                                    {{ $value->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="d-none" id="no-permission-found">
                                    <div class="text-muted text-center">Tidak ada permission yang sesuai.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkAll = document.getElementById('checkAllPermissions');
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                const permSearch = document.getElementById('permission-search');
                const permItems = document.querySelectorAll('.permission-item');
                const noPermFound = document.getElementById('no-permission-found');

                // Set CheckAll if all permissions are already checked
                function updateCheckAll() {
                    let checked = 0,
                        visible = 0;
                    checkboxes.forEach(cb => {
                        if (cb.closest('.permission-item').style.display !== "none") {
                            visible++;
                            if (cb.checked) checked++;
                        }
                    });
                    checkAll.checked = visible > 0 && checked === visible;
                }
                if (checkAll) {
                    checkAll.addEventListener('change', function() {
                        checkboxes.forEach(cb => {
                            if (cb.closest('.permission-item').style.display !== "none") {
                                cb.checked = this.checked;
                            }
                        });
                    });
                }
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateCheckAll);
                });
                updateCheckAll();

                if (permSearch) {
                    permSearch.addEventListener('input', function() {
                        const keyword = this.value.toLowerCase();
                        let countVisible = 0;
                        permItems.forEach(item => {
                            const labelText = item.querySelector('label').innerText.toLowerCase();
                            if (labelText.includes(keyword)) {
                                item.style.display = '';
                                countVisible++;
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        updateCheckAll();
                        if (countVisible === 0) {
                            noPermFound.classList.remove('d-none');
                        } else {
                            noPermFound.classList.add('d-none');
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection
