<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket Trouble</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .ticket-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }

        .ticket-header {
            background: linear-gradient(135deg, #f6c23e, #dda20a);
            color: white;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card ticket-card">
                <div class="ticket-header">
                    <h3 class="mb-1">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Ticket Trouble
                    </h3>
                    <p class="mb-0">Kode Ticket: <strong>{{ $tiketTrouble->KodeTiket }}</strong></p>
                </div>

                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ticket.update', $tiketTrouble->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h5 class="mb-3">Informasi Ticket</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Nama" class="form-label">Nama</label>
                                <input
                                    type="text"
                                    name="Nama"
                                    id="Nama"
                                    value="{{ old('Nama', $tiketTrouble->Nama) }}"
                                    class="form-control @error('Nama') is-invalid @enderror"
                                    required
                                >
                                @error('Nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="NoHp" class="form-label">
                                    No HP <small class="text-muted">(Opsional)</small>
                                </label>
                                <input
                                    type="text"
                                    name="NoHp"
                                    id="NoHp"
                                    value="{{ old('NoHp', $tiketTrouble->NoHp) }}"
                                    class="form-control @error('NoHp') is-invalid @enderror"
                                >
                                @error('NoHp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="KodePerusahaan" class="form-label">
                                    Kode Perusahaan <small class="text-muted">(Opsional)</small>
                                </label>
                                <input
                                    type="text"
                                    name="KodePerusahaan"
                                    id="KodePerusahaan"
                                    value="{{ old('KodePerusahaan', $tiketTrouble->KodePerusahaan) }}"
                                    class="form-control @error('KodePerusahaan') is-invalid @enderror"
                                >
                                @error('KodePerusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="Prioritas" class="form-label">Prioritas</label>
                                <select
                                    name="Prioritas"
                                    id="Prioritas"
                                    class="form-select @error('Prioritas') is-invalid @enderror"
                                    required
                                >
                                    <option value="Rendah" {{ old('Prioritas', $tiketTrouble->Prioritas) == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="Sedang" {{ old('Prioritas', $tiketTrouble->Prioritas) == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Tinggi" {{ old('Prioritas', $tiketTrouble->Prioritas) == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="Darurat" {{ old('Prioritas', $tiketTrouble->Prioritas) == 'Darurat' ? 'selected' : '' }}>Darurat</option>
                                </select>
                                @error('Prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="Status" class="form-label">Status</label>
                                <select
                                    name="Status"
                                    id="Status"
                                    class="form-select @error('Status') is-invalid @enderror"
                                    required
                                >
                                    <option value="Open" {{ old('Status', $tiketTrouble->Status) == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In Progress" {{ old('Status', $tiketTrouble->Status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ old('Status', $tiketTrouble->Status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Closed" {{ old('Status', $tiketTrouble->Status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                                @error('Status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="Judul" class="form-label">Judul Masalah</label>
                            <input
                                type="text"
                                name="Judul"
                                id="Judul"
                                value="{{ old('Judul', $tiketTrouble->Judul) }}"
                                class="form-control @error('Judul') is-invalid @enderror"
                                required
                            >
                            @error('Judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="Deskripsi" class="form-label">Deskripsi Masalah</label>
                            <textarea
                                name="Deskripsi"
                                id="Deskripsi"
                                rows="5"
                                class="form-control @error('Deskripsi') is-invalid @enderror"
                                required
                            >{{ old('Deskripsi', $tiketTrouble->Deskripsi) }}</textarea>
                            @error('Deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">File Pendukung Saat Ini</label>
                            <div>
                                @if ($tiketTrouble->FilePendukung)
                                    <a href="{{ asset('storage/' . $tiketTrouble->FilePendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Lihat File Pendukung
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada file pendukung.</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="FilePendukung" class="form-label">
                                Ganti File Pendukung <small class="text-muted">(Opsional)</small>
                            </label>
                            <input
                                type="file"
                                name="FilePendukung"
                                id="FilePendukung"
                                class="form-control @error('FilePendukung') is-invalid @enderror"
                            >
                            <small class="text-muted">
                                Jika diisi, file pendukung lama akan diganti.
                            </small>
                            @error('FilePendukung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Respon Admin / Tim IT</h5>

                        <div class="mb-3">
                            <label for="Respon" class="form-label">Respon</label>
                            <textarea
                                name="Respon"
                                id="Respon"
                                rows="4"
                                class="form-control @error('Respon') is-invalid @enderror"
                                placeholder="Tuliskan respon atau tindak lanjut ticket..."
                            >{{ old('Respon', $tiketTrouble->Respon) }}</textarea>
                            @error('Respon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="DiresponOleh" class="form-label">Direspon Oleh</label>
                                <input
                                    type="text"
                                    name="DiresponOleh"
                                    id="DiresponOleh"
                                    value="{{ old('DiresponOleh', $tiketTrouble->DiresponOleh) }}"
                                    class="form-control @error('DiresponOleh') is-invalid @enderror"
                                    placeholder="Nama petugas / tim"
                                >
                                @error('DiresponOleh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lampiran Respon Saat Ini</label>
                                <div>
                                    @if ($tiketTrouble->LampiranRespon)
                                        <a href="{{ asset('storage/' . $tiketTrouble->LampiranRespon) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark-arrow-down"></i> Lihat Lampiran Respon
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada lampiran respon.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="LampiranRespon" class="form-label">
                                Ganti Lampiran Respon <small class="text-muted">(Opsional)</small>
                            </label>
                            <input
                                type="file"
                                name="LampiranRespon"
                                id="LampiranRespon"
                                class="form-control @error('LampiranRespon') is-invalid @enderror"
                            >
                            <small class="text-muted">
                                Jika diisi, lampiran respon lama akan diganti.
                            </small>
                            @error('LampiranRespon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('ticket.index') }}" class="btn btn-light">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
