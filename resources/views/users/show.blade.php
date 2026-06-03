@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header fw-bold">
            Update Profil User
        </div>
        <div class="card-body">
            <form action="{{ route('users.profile', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="name" class="form-label"><strong>Nama</strong></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            id="name" placeholder="Nama" value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label"><strong>Email</strong></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" placeholder="Email" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label"><strong>Password</strong></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        @error('password')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                    </div>

                    <div class="col-md-3">
                        <label for="departemen" class="form-label"><strong>Departemen</strong></label>
                        <select name="departemen" class="select2 form-control @error('departemen') is-invalid @enderror"
                            id="departemen">
                            <option value="">Pilih Departemen</option>
                            @foreach ($departemen as $dpt)
                                <option value="{{ $dpt->id }}"
                                    {{ old('departemen', $user->departemen) == $dpt->id ? 'selected' : '' }}>
                                    {{ $dpt->Nama }}
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
                                    {{ old('jabatan', $user->jabatan) == $jbt->id ? 'selected' : '' }}>
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

                    <div class="row mt-3">
                        <!-- FOTO -->
                        <div class="col-md-6 mb-4">
                            <label for="foto" class="form-label"><strong>Foto</strong>
                                <span class="badge bg-secondary ms-1" style="font-weight:normal;">Opsional</span>
                            </label>

                            <!-- Preview Area Foto -->
                            <div class="mb-3" id="foto-preview-container"
                                style="display: {{ $user->foto ? 'block' : 'none' }};">
                                <div class="text-center p-3 border rounded bg-light">
                                    <img id="foto-preview"
                                        src="{{ $user->foto ? asset('storage/upload/foto/' . $user->foto) : '' }}"
                                        alt="Preview Foto" class="rounded-circle shadow border border-2"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <small class="text-muted" id="foto-filename">
                                            {{ $user->foto ? $user->foto : '' }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <input type="file" name="foto" id="foto"
                                class="form-control @error('foto') is-invalid @enderror" accept="image/*">

                            <small class="text-muted ms-1 d-block mt-1">
                                <em>Format: JPG/PNG, Maksimal 2MB. Opsional, tidak wajib mengunggah foto.</em>
                            </small>
                            @error('foto')
                                <div class="text-danger mt-1">
                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- TANDA TANGAN -->
                        <div class="col-md-6 mb-4">
                            <label for="tandatangan" class="form-label"><strong>Tanda Tangan</strong>
                                <span class="badge bg-secondary ms-1" style="font-weight:normal;">Opsional</span>
                            </label>

                            <!-- Preview Area Tanda Tangan -->
                            <div class="mb-3" id="tandatangan-preview-container"
                                style="display: {{ $user->tandatangan ? 'block' : 'none' }};">
                                <div class="text-center p-3 border rounded bg-light">
                                    <img id="tandatangan-preview"
                                        src="{{ $user->tandatangan ? asset('storage/upload/tandatangan/' . $user->tandatangan) : '' }}"
                                        alt="Preview Tanda Tangan" class="rounded border shadow-sm"
                                        style="max-width: 300px; max-height: 100px; object-fit: contain;">
                                    <div class="mt-2">
                                        <small class="text-muted" id="tandatangan-filename">
                                            {{ $user->tandatangan ? $user->tandatangan : '' }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <input type="file" name="tandatangan" id="tandatangan"
                                class="form-control @error('tandatangan') is-invalid @enderror" accept="image/*">

                            <small class="text-muted ms-1 d-block mt-1">
                                <em>Format: JPG/PNG, Maksimal 2MB. Opsional, tidak wajib mengunggah tanda tangan.</em>
                            </small>
                            @error('tandatangan')
                                <div class="text-danger mt-1">
                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Update Profil
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times me-1"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    @if ($message = Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ $message }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup preview untuk Foto
            const fotoInput = document.getElementById('foto');
            const fotoPreview = document.getElementById('foto-preview');
            const fotoContainer = document.getElementById('foto-preview-container');
            const fotoFilename = document.getElementById('foto-filename');

            // Setup preview untuk Tanda Tangan
            const ttdInput = document.getElementById('tandatangan');
            const ttdPreview = document.getElementById('tandatangan-preview');
            const ttdContainer = document.getElementById('tandatangan-preview-container');
            const ttdFilename = document.getElementById('tandatangan-filename');

            // Function untuk preview image
            function previewImage(input, preview, container, filenameElement) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];

                    // Validasi tipe file
                    if (!file.type.match('image.*')) {
                        alert('File harus berupa gambar (JPG/PNG)');
                        input.value = '';
                        return;
                    }

                    // Validasi ukuran file (2MB = 2097152 bytes)
                    if (file.size > 2097152) {
                        alert('Ukuran file maksimal 2MB');
                        input.value = '';
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        container.style.display = 'block';
                        filenameElement.textContent = file.name;
                    };

                    reader.readAsDataURL(file);
                }
            }

            // Event listener untuk Foto
            if (fotoInput) {
                fotoInput.addEventListener('change', function() {
                    previewImage(this, fotoPreview, fotoContainer, fotoFilename);
                });
            }

            // Event listener untuk Tanda Tangan
            if (ttdInput) {
                ttdInput.addEventListener('change', function() {
                    previewImage(this, ttdPreview, ttdContainer, ttdFilename);
                });
            }
        });
    </script>
@endpush
