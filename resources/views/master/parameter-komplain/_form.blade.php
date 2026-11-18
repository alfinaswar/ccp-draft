@php
    $data = $parameter ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="Jenis" class="form-label"><strong>Bagian Formulir</strong></label>
        <select name="Jenis" id="Jenis" class="form-select @error('Jenis') is-invalid @enderror">
            <option value="">-- Pilih Bagian --</option>
            @foreach ($jenisList as $kode => $label)
                <option value="{{ $kode }}" {{ old('Jenis', $data->Jenis ?? '') === $kode ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
        @error('Jenis')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="Nama" class="form-label"><strong>Nama Parameter</strong></label>
        <input type="text" name="Nama" id="Nama" class="form-control @error('Nama') is-invalid @enderror"
            value="{{ old('Nama', $data->Nama ?? '') }}">
        @error('Nama')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="Urutan" class="form-label"><strong>Urutan Tampil</strong></label>
        <input type="number" name="Urutan" id="Urutan" class="form-control" min="0"
            value="{{ old('Urutan', $data->Urutan ?? 0) }}">
    </div>
    <div class="col-md-6">
        <label for="Aktif" class="form-label"><strong>Status</strong></label>
        <select name="Aktif" id="Aktif" class="form-select">
            <option value="Y" {{ old('Aktif', $data->Aktif ?? 'Y') === 'Y' ? 'selected' : '' }}>Aktif</option>
            <option value="N" {{ old('Aktif', $data->Aktif ?? '') === 'N' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>
</div>
<p class="form-text text-muted mt-2">
    Parameter bernama <strong>Lainnya</strong> otomatis menampilkan kolom isian bebas di samping checkbox.
</p>
