@php
    $data = $data ?? null;
@endphp

<input type="hidden" name="kembali" value="{{ $kembali ?? '' }}">

<div class="row g-3">
    <div class="col-md-6">
        <label for="nama_unit" class="form-label"><strong>Nama Unit</strong></label>
        <input type="text" name="nama_unit" id="nama_unit"
            class="form-control @error('nama_unit') is-invalid @enderror"
            value="{{ old('nama_unit', $data->nama_unit ?? '') }}">
        @error('nama_unit')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="kode_unit" class="form-label"><strong>Kode Unit</strong></label>
        <input type="text" name="kode_unit" id="kode_unit" class="form-control"
            value="{{ old('kode_unit', $data->kode_unit ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="KodePerusahaan" class="form-label"><strong>Perusahaan / RS</strong></label>
        <select name="KodePerusahaan" id="KodePerusahaan"
            class="form-select @error('KodePerusahaan') is-invalid @enderror">
            <option value="">Semua RS (berlaku untuk semua perusahaan)</option>
            @foreach ($perusahaan as $p)
                <option value="{{ $p->Kode }}"
                    {{ old('KodePerusahaan', $data->KodePerusahaan ?? request('perusahaan')) === $p->Kode ? 'selected' : '' }}>
                    {{ $p->Nama }}</option>
            @endforeach
        </select>
        @error('KodePerusahaan')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Kosongkan bila unit ini dipakai semua RS.</small>
    </div>
    <div class="col-md-6">
        <label for="parent_id" class="form-label"><strong>Unit Induk</strong></label>
        <select name="parent_id" id="parent_id" class="form-select">
            <option value="">Tanpa induk</option>
            @foreach ($unitList as $u)
                <option value="{{ $u->id }}"
                    {{ old('parent_id', $data->parent_id ?? '') == $u->id ? 'selected' : '' }}>
                    {{ $u->nama_unit }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="Aktif" class="form-label"><strong>Status</strong></label>
        <select name="Aktif" id="Aktif" class="form-select">
            <option value="Y" {{ old('Aktif', $data->Aktif ?? 'Y') === 'Y' ? 'selected' : '' }}>Aktif</option>
            <option value="N" {{ old('Aktif', $data->Aktif ?? '') === 'N' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>
</div>
