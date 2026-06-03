@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Manajemen Role</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role</a></li>
                    <li class="breadcrumb-item active">Buat Role Baru</li>
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
                    <h4 class="card-title mb-0 text-white">Formulir Role Baru</h4>
                    <p class="card-text mb-0 text-white-50">
                        Silakan isi data role dan pilih permission yang diinginkan.
                    </p>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'roles.store', 'method' => 'POST']) !!}
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
                                <div class="table-responsive" id="permission-list">
                                    @php
                                        $permissionsSorted = $permission
                                            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                                            ->values();
                                        $chunkSize = 3;
                                        $chunks = $permissionsSorted->chunk($chunkSize);
                                    @endphp
                                    <table class="table table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                @for ($c = 1; $c <= $chunkSize; $c++)
                                                    <th style="min-width:180px;">Permission</th>
                                                    <th style="width: 80px;">Pilih</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($chunks as $rowIdx => $chunk)
                                                <tr>
                                                    @foreach ($chunk as $perm)
                                                        <td class="permission-item">
                                                            <label class="form-check-label" for="perm_{{ $perm->id }}">
                                                                {{ $perm->name }}
                                                            </label>
                                                        </td>
                                                        <td class="text-center permission-item">
                                                            {{ Form::checkbox('permission[]', $perm->id, false, [
                                                                'class' => 'form-check-input permission-checkbox',
                                                                'id' => 'perm_' . $perm->id,
                                                                'data-perm-name' => strtolower($perm->name),
                                                            ]) }}
                                                        </td>
                                                    @endforeach
                                                    @if ($chunk->count() < $chunkSize)
                                                        @for ($i = 0; $i < $chunkSize - $chunk->count(); $i++)
                                                            <td class="permission-item"></td>
                                                            <td class="permission-item"></td>
                                                        @endfor
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
                            <i class="fa fa-save"></i> Simpan
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
                const permissionList = document.getElementById('permission-list');
                const noPermFound = document.getElementById('no-permission-found');

                // Helper to get ALL .permission-item td/checkbox -- each permission label/box has class .permission-item
                function getVisibleCheckboxes() {
                    // In each row, pairs of permission-item for label & checkbox
                    return Array.from(document.querySelectorAll('.permission-checkbox')).filter(cb => {
                        // cb is always inside td.permission-item, which is itself always displayed if visible
                        return cb.closest('td.permission-item, th.permission-item') && cb.closest(
                            'td.permission-item, th.permission-item').style.display !== 'none';
                    });
                }

                // Checklist all
                if (checkAll) {
                    checkAll.addEventListener('change', function() {
                        const visibleCheckboxes = getVisibleCheckboxes();
                        visibleCheckboxes.forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                }

                if (permSearch) {
                    permSearch.addEventListener('input', function() {
                        const keyword = this.value.trim().toLowerCase();
                        let countVisible = 0;

                        // Loop over all permission-item TDs
                        const rows = permissionList.querySelectorAll('tbody tr');
                        rows.forEach(row => {
                            let rowVisible = false;
                            // Get all permission label and checkbox <td>s (in pairs: label, checkbox, label, checkbox, ...)
                            const tds = row.querySelectorAll('td.permission-item');
                            for (let i = 0; i < tds.length; i += 2) {
                                const labelTd = tds[i];
                                const checkboxTd = tds[i + 1];
                                if (labelTd && checkboxTd) {
                                    const label = labelTd.querySelector('label');
                                    if (label && label.innerText.toLowerCase().includes(keyword)) {
                                        labelTd.style.display = '';
                                        checkboxTd.style.display = '';
                                        rowVisible = true;
                                        countVisible++;
                                    } else {
                                        // Not matched: hide both label & checkbox td
                                        labelTd.style.display = 'none';
                                        checkboxTd.style.display = 'none';
                                    }
                                } else if (labelTd) {
                                    // For empty <td> (filler), just hide
                                    labelTd.style.display = 'none';
                                }
                            }
                            // Hide row if no label in row is shown
                            row.style.display = rowVisible ? '' : 'none';
                        });

                        // Reset "check all" if search happens
                        if (checkAll) checkAll.checked = false;

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
