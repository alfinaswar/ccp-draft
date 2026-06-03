@extends('layouts.app')

@section('content')
    @push('css')
        <style>
            .custom-upload-area:hover,
            .custom-upload-area:focus-within {
                box-shadow: 0 0 8px #b8cdfa !important;
                background: linear-gradient(135deg, #eef4fe 20%, #e9f3fc 100%);
            }

            .custom-upload-area img {
                border: 2px solid #e3ebff;
                background: #fff;
            }
        </style>
    @endpush
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Manajemen Akun</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Buat User</li>
                </ul>
            </div>
        </div>
    </div>



    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Buat Akun</h4>
                    <p class="card-text mb-0">
                        Silakan isi data pengguna di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="name" class="form-label"><strong>Nama</strong></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                    placeholder="Nama" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label"><strong>Email</strong></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    placeholder="Email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label"><strong>Password</strong></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                    placeholder="Masukkan Password">
                                @error('password')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Wajib diisi untuk pembuatan akun. Simpan baik-baik email dan
                                    password ini untuk login ke sistem.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="kodeperusahaan" class="form-label"><strong>Kode Perusahaan</strong></label>
                                <select name="kodeperusahaan" id="kodeperusahaan"
                                    class="select2 @error('kodeperusahaan') is-invalid @enderror">
                                    <option value="">Pilih Kode Perusahaan</option>
                                    @foreach ($perusahaan as $prsh)
                                        <option value="{{ $prsh->Kode }}"
                                            {{ old('kodeperusahaan') == $prsh->Kode ? 'selected' : '' }}>
                                            {{ $prsh->Kode }} - {{ $prsh->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kodeperusahaan')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="departemen" class="form-label"><strong>Departemen</strong></label>
                                <select name="departemen"
                                    class="select2 form-control @error('departemen') is-invalid @enderror" id="departemen">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departemen as $dep)
                                        <option value="{{ $dep->id }}"
                                            {{ old('departemen') == $dep->id ? 'selected' : '' }}>
                                            {{ $dep->KodeDepartemen }} - {{ $dep->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('departemen')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="jabatan" class="form-label"><strong>Jabatan</strong></label>
                                <select name="jabatan" id="jabatan"
                                    class="select2 form-control @error('jabatan') is-invalid @enderror">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach ($jabatan as $jbt)
                                        <option value="{{ $jbt->id }}"
                                            {{ old('jabatan') == $jbt->id ? 'selected' : '' }}>
                                            {{ $jbt->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="roles" class="form-label"><strong>Peran (Roles)</strong></label>
                                <select name="roles[]" id="roles"
                                    class="select2 form-control @error('roles') is-invalid @enderror">
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            {{ collect(old('roles'))->contains($role) ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('roles')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted ms-1">
                                    Pilih satu atau lebih peran untuk user ini.
                                </small>
                            </div>



                            <div class="col-md-6 mb-4">
                                <label for="foto" class="form-label"><strong>Foto</strong></label>
                                <div class="custom-upload-area border rounded-4 shadow-sm p-3 d-flex flex-column align-items-center justify-content-center mb-2 position-relative"
                                    id="foto-drop-area"
                                    style="min-height: 130px; cursor: pointer; background: linear-gradient(135deg, #f7fafc 40%, #f0f4ff 100%); transition: box-shadow .2s;"
                                    onclick="document.getElementById('foto').click()" ondrop="handleDrop(event, 'foto')"
                                    ondragover="event.preventDefault()"
                                    onmouseenter="this.style.boxShadow='0 0 8px #e3ebff'"
                                    onmouseleave="this.style.boxShadow='none'">
                                    <i class="fa fa-camera fa-2x mb-2 text-primary" aria-hidden="true"></i>
                                    <span id="foto-preview-text" class="text-secondary" style="font-size: 1em;">
                                        <span class="fw-bold">Klik</span> atau <span class="fw-bold">drag & drop</span>
                                        file
                                        di sini
                                    </span>
                                    <img id="foto-preview" src="#" alt="Preview Foto"
                                        class="rounded-circle border mt-2"
                                        style="display: none; max-width: 100px; max-height: 100px; object-fit: cover;">
                                </div>
                                <small class="form-text text-muted ms-1">
                                    Gunakan foto wajah (jpg/png, max 2MB).
                                </small>
                                <input type="file" name="foto"
                                    class="form-control @error('foto') is-invalid @enderror" id="foto"
                                    style="display: none;" accept="image/*" onchange="previewFile('foto')">
                                @error('foto')
                                    <div class="text-danger mt-1">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="tandatangan" class="form-label"><strong>Tanda Tangan</strong></label>
                                <div class="custom-upload-area border rounded-4 shadow-sm p-3 d-flex flex-column align-items-center justify-content-center mb-2 position-relative"
                                    id="tandatangan-drop-area"
                                    style="min-height: 130px; cursor: pointer; background: linear-gradient(135deg,#f0f6fa 40%,#eef5fb 100%); transition: box-shadow .2s;"
                                    onclick="document.getElementById('tandatangan').click()"
                                    ondrop="handleDrop(event, 'tandatangan')" ondragover="event.preventDefault()"
                                    onmouseenter="this.style.boxShadow='0 0 8px #e3ebff'"
                                    onmouseleave="this.style.boxShadow='none'">
                                    <i class="fa fa-pen-fancy fa-2x mb-2 text-primary" aria-hidden="true"></i>
                                    <span id="tandatangan-preview-text" class="text-secondary" style="font-size: 1em;">
                                        <span class="fw-bold">Klik</span> atau <span class="fw-bold">drag & drop</span>
                                        file di sini
                                    </span>
                                    <img id="tandatangan-preview" src="#" alt="Preview Tanda Tangan"
                                        class="rounded border mt-2"
                                        style="display: none; max-width: 140px; max-height: 60px; object-fit: contain;">
                                </div>
                                <small class="form-text text-muted ms-1">
                                    Unggah tanda tangan sesuai dokumen resmi (jpg/png, max 2MB).
                                </small>
                                <input type="file" name="tandatangan"
                                    class="form-control @error('tandatangan') is-invalid @enderror" id="tandatangan"
                                    style="display: none;" accept="image/*" onchange="previewFile('tandatangan')">
                                @error('tandatangan')
                                    <div class="text-danger mt-1">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>





                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary me-2"><i
                                        class="fa fa-arrow-left"></i> Kembali</a>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan
                                    Akun</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function previewFile(inputId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(inputId + '-preview');
            const previewText = document.getElementById(inputId + '-preview-text');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    previewText.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = "#";
                preview.style.display = 'none';
                previewText.style.display = 'block';
            }
        }

        function handleDrop(event, inputId) {
            event.preventDefault();
            const input = document.getElementById(inputId);
            if (event.dataTransfer.files.length > 0) {
                input.files = event.dataTransfer.files;
                previewFile(inputId);
            }
        }
    </script>
@endpush
