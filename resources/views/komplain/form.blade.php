<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Komplain Vendor</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #111;
            background-color: #1a202c;
            background-image: linear-gradient(rgba(15, 23, 42, 0.45), rgba(15, 23, 42, 0.55)), url("{{ asset('assets/img/rs-awal-bros-pekanbaru.jpeg') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            margin: 0;
            padding: 32px 14px;
            line-height: 1.5;
            min-height: 100vh;
        }

        .sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px 48px 56px;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
        }

        .kop {
            margin-bottom: 24px;
        }

        .kop img {
            width: 220px;
            height: auto;
            max-width: 100%;
            display: block;
        }

        h1 {
            font-size: 17px;
            text-align: center;
            margin: 0 0 28px;
            text-transform: uppercase;
        }

        h2 {
            font-size: 14px;
            margin: 26px 0 10px;
            text-transform: uppercase;
        }

        .row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 4px;
        }

        .row .label {
            width: 210px;
            flex: 0 0 210px;
        }

        .row .sep {
            width: 12px;
            flex: 0 0 12px;
        }

        .row .value {
            flex: 1;
            min-width: 0;
        }

        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            border: 0;
            border-bottom: 1px solid #555;
            background: transparent;
            font: inherit;
            padding: 2px 4px;
            color: #111;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: 0;
            border-bottom-color: #d21f26;
            background: #fffbea;
        }

        textarea {
            border: 1px solid #555;
            padding: 6px;
            resize: vertical;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0;
        }

        .check input[type="checkbox"] {
            width: 15px;
            height: 15px;
            flex: 0 0 15px;
            margin: 0;
        }

        .check .inline {
            flex: 1;
            min-width: 0;
            max-width: 340px;
        }

        .hint {
            color: #a00;
            font-size: 12px;
            margin: 2px 0 0 23px;
        }

        .note {
            font-size: 12px;
            color: #555;
            margin-top: 6px;
        }

        .actions {
            margin-top: 36px;
            text-align: right;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        button {
            font: inherit;
            padding: 9px 26px;
            border: 0;
            border-radius: 4px;
            background: #d21f26;
            color: #fff;
            cursor: pointer;
        }

        button:hover {
            background: #b0191f;
        }

        .alert {
            background: #fdecea;
            border: 1px solid #f5c2c0;
            color: #842029;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 18px;
        }

        .alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .editor-wrap {
            border: 1px solid #555;
        }

        .upload-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .upload-section-title {
            font-weight: bold;
            color: #111;
        }

        .upload-counter {
            font-size: 12px;
            color: #495057;
            background: #e9ecef;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .upload-dropzone {
            border: 2px dashed #b0bec5;
            padding: 24px 16px;
            border-radius: 8px;
            background: #f8fafc;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s ease, background-color .2s ease;
            user-select: none;
        }

        .upload-dropzone:hover,
        .upload-dropzone.dragover {
            border-color: #d21f26;
            background: #fff5f5;
        }

        .upload-icon {
            margin-bottom: 8px;
            display: flex;
            justify-content: center;
        }

        .upload-text {
            font-size: 14px;
            color: #334155;
            margin-bottom: 6px;
        }

        .upload-text .browse-link {
            color: #d21f26;
            font-weight: 600;
            text-decoration: underline;
            cursor: pointer;
        }

        .upload-hint {
            font-size: 12px;
            color: #64748b;
        }

        .upload-alert {
            margin-top: 10px;
            padding: 9px 14px;
            background: #fdecea;
            border: 1px solid #f5c2c0;
            color: #842029;
            border-radius: 6px;
            font-size: 12px;
            line-height: 1.5;
        }

        .upload-alert ul {
            margin: 4px 0 0;
            padding-left: 20px;
        }

        .preview {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 14px;
        }

        .preview .item {
            position: relative;
            width: 130px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
            background: #fff;
            text-align: center;
            font-size: 11px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .preview .item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .preview .item .thumb-wrap {
            width: 100%;
            height: 80px;
            border-radius: 4px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .preview .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .preview .item .pdf-icon-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #d21f26;
        }

        .preview .item .pdf-icon-wrap svg {
            width: 32px;
            height: 32px;
        }

        .preview .item .pdf-badge {
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            background: #d21f26;
            padding: 1px 6px;
            border-radius: 3px;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }

        .preview .item .file-name {
            font-weight: 500;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            display: block;
        }

        .preview .item .file-size {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .preview .item .btn-remove {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #d21f26;
            color: #fff;
            border: 2px solid #fff;
            font-size: 14px;
            line-height: 1;
            text-align: center;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .preview .item .btn-remove:hover {
            background: #a51218;
            transform: scale(1.1);
        }

        @media (max-width: 640px) {
            .sheet {
                padding: 24px 18px 32px;
            }

            .row {
                display: block;
            }

            .row .label {
                width: auto;
                padding-top: 6px;
            }

            .row .sep {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="kop">
            <img src="{{ asset('assets/img/logo/logoawalbros.png') }}" alt="RS Awal Bros">
        </div>
        <h1>Form Komplain Vendor</h1>

        @if ($errors->any())
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('komplain.simpan') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="label">Nama RS</div>
                <div class="sep">:</div>
                <div class="value">
                    <select name="KodePerusahaan" id="KodePerusahaan" required>
                        <option value="">-- Pilih Rumah Sakit --</option>
                        @foreach ($perusahaan as $p)
                            <option value="{{ $p->id }}"
                                {{ (string) old('KodePerusahaan') === (string) $p->id ? 'selected' : '' }}>{{ $p->NamaLengkap ?? $p->Nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="label">Tanggal</div>
                <div class="sep">:</div>
                <div class="value"><input type="date" name="Tanggal" value="{{ old('Tanggal', date('Y-m-d')) }}"
                        required></div>
            </div>

            <h2>A. Identitas Pelapor</h2>
            <div class="row">
                <div class="label">&bull;&nbsp; Nama Pelapor</div>
                <div class="sep">:</div>
                <div class="value"><input type="text" name="NamaPelapor" value="{{ old('NamaPelapor') }}" required></div>
            </div>
            <div class="row">
                <div class="label">&bull;&nbsp; Jabatan</div>
                <div class="sep">:</div>
                <div class="value">
                    <select name="JabatanId" required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach ($jabatan as $j)
                            <option value="{{ $j->id }}" {{ old('JabatanId') == $j->id ? 'selected' : '' }}>
                                {{ $j->Nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="label">&bull;&nbsp; Unit Kerja</div>
                <div class="sep">:</div>
                <div class="value">
                    <select name="UnitId" id="UnitId" required>
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach ($unit as $u)
                            <option value="{{ $u->id }}" data-perusahaan="{{ $u->KodePerusahaan }}"
                                data-perusahaan-id="{{ optional($perusahaan->firstWhere('Kode', $u->KodePerusahaan))->id }}"
                                {{ old('UnitId') == $u->id ? 'selected' : '' }}>
                                {{ $u->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h2>B. Data BMHP Umum</h2>
            <div class="row">
                <div class="label">&bull;&nbsp; Nama Barang</div>
                <div class="sep">:</div>
                <div class="value"><input type="text" name="NamaBarang" value="{{ old('NamaBarang') }}" required></div>
            </div>
            <div class="row">
                <div class="label">&bull;&nbsp; Jenis/Kategori</div>
                <div class="sep">:</div>
                <div class="value"><input type="text" name="JenisKategori" value="{{ old('JenisKategori') }}" required></div>
            </div>
            <div class="row">
                <div class="label">&bull;&nbsp; Merk</div>
                <div class="sep">:</div>
                <div class="value"><input type="text" name="Merk" value="{{ old('Merk') }}" required></div>
            </div>
            <div class="row">
                <div class="label">&bull;&nbsp; Nama Distributor/Penyedia</div>
                <div class="sep">:</div>
                <div class="value"><input type="text" name="Distributor" value="{{ old('Distributor') }}" required></div>
            </div>

            <h2>C. Parameter Evaluasi BMHP Umum</h2>
            @foreach ($parameterEvaluasi as $index => $item)
                <div class="check">
                    <input type="checkbox" name="ParameterEvaluasi[]" id="pe{{ $index }}" value="{{ $item }}"
                        {{ in_array($item, old('ParameterEvaluasi', []), true) ? 'checked' : '' }}>
                    <label for="pe{{ $index }}">{{ $item }}@if ($item !== 'Lainnya'):@endif</label>
                    @if ($item === 'Lainnya')
                        <input type="text" class="inline" name="ParameterLainnya" value="{{ old('ParameterLainnya') }}"
                            placeholder="sebutkan">
                    @endif
                </div>
            @endforeach
            @error('ParameterEvaluasi')
                <div class="hint">{{ $message }}</div>
            @enderror

            <h2>D. Deskripsi Evaluasi</h2>
            <div class="row">
                <div class="label">&bull;&nbsp; Uraian Singkat</div>
                <div class="sep"></div>
                <div class="value"></div>
            </div>
            <textarea name="UraianSingkat" id="UraianSingkat" rows="8"
                placeholder="Jelaskan kronologi dan kondisi permasalahan">{{ old('UraianSingkat') }}</textarea>
            @error('UraianSingkat')
                <div class="hint">{{ $message }}</div>
            @enderror

            <div style="margin-top: 22px;">
                <div class="upload-section-header">
                    <div class="upload-section-title">&bull;&nbsp; Data Pendukung</div>
                    <span id="lampiranCount" class="upload-counter">0 / 10 file</span>
                </div>
                <div>
                    <div class="upload-dropzone" id="dropArea">
                        <input type="file" name="Lampiran[]" id="Lampiran" multiple
                            accept=".jpg,.jpeg,.png,.webp,.pdf" style="display: none;">
                        <div class="upload-icon">
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#d21f26" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div class="upload-text">
                            <strong>Tarik &amp; letakkan file di sini</strong>, atau <span class="browse-link">pilih dari perangkat</span>
                        </div>
                        <div class="upload-hint">
                            Format: JPG, JPEG, PNG, WEBP, PDF &bull; Maksimal 10 file &bull; Ukuran di bawah 5 MB per file
                        </div>
                    </div>

                    <div id="uploadErrorAlert" class="upload-alert" style="display: none;"></div>

                    @error('Lampiran')
                        <div class="hint">{{ $message }}</div>
                    @enderror
                    @error('Lampiran.*')
                        <div class="hint">{{ $message }}</div>
                    @enderror

                    <div class="preview" id="previewLampiran"></div>
                </div>
            </div>

            <p class="note">Kolom tanda tangan dan tindak lanjut diisi oleh Kepala Divisi, Group Head, Logistik
                Umum, dan CCP melalui sistem.</p>

            <div class="actions">
                <button type="submit">Kirim Formulir</button>
            </div>
        </form>
    </div>

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        // Editor rapi untuk Uraian Singkat. Toolbar dibatasi supaya hasilnya
        // tetap bersih dan aman saat dicetak.
        CKEDITOR.replace('UraianSingkat', {
            height: 220,
            removePlugins: 'elementspath,resize,cloudservices,easyimage',
            toolbarGroups: [{
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'blocks', 'align']
                },
                {
                    name: 'editing',
                    groups: ['find', 'selection']
                },
                {
                    name: 'tools',
                    groups: ['tools']
                },
                '/',
                {
                    name: 'styles'
                },
                {
                    name: 'document',
                    groups: ['mode']
                }
            ],
            removeButtons: 'Strike,Subscript,Superscript,CopyFormatting,Anchor,HorizontalRule,CreateDiv,Outdent,Indent,Blockquote,JustifyBlock,BidiLtr,BidiRtl,Language,Flash,Table,Image,Print,Preview,Save,NewPage,Templates,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Source,About'
        });

        // Upload & preview data pendukung (maksimal 10 file, tiap file < 5 MB).
        (function() {
            var input = document.getElementById('Lampiran');
            var box = document.getElementById('previewLampiran');
            var dropArea = document.getElementById('dropArea');
            var counter = document.getElementById('lampiranCount');
            var alertBox = document.getElementById('uploadErrorAlert');
            if (!input || !box || !dropArea) return;

            var MAX_FILES = 10;
            var MAX_SIZE = 5 * 1024 * 1024; // 5 MB
            var ALLOWED_EXTS = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            var selectedFiles = [];

            function formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            }

            function showAlert(messages) {
                if (!alertBox) return;
                if (!messages || messages.length === 0) {
                    alertBox.style.display = 'none';
                    alertBox.innerHTML = '';
                    return;
                }
                var html = '<strong>Peringatan File:</strong><ul>';
                messages.forEach(function(msg) {
                    html += '<li>' + msg + '</li>';
                });
                html += '</ul>';
                alertBox.innerHTML = html;
                alertBox.style.display = 'block';
            }

            function updateCounter() {
                if (counter) {
                    counter.textContent = selectedFiles.length + ' / ' + MAX_FILES + ' file';
                }
            }

            function syncInputFiles() {
                if (window.DataTransfer) {
                    try {
                        var dt = new DataTransfer();
                        selectedFiles.forEach(function(f) {
                            dt.items.add(f);
                        });
                        input.files = dt.files;
                    } catch (e) {
                        console.error('DataTransfer sync error:', e);
                    }
                }
                updateCounter();
                renderPreview();
            }

            function renderPreview() {
                box.innerHTML = '';

                selectedFiles.forEach(function(file, index) {
                    var item = document.createElement('div');
                    item.className = 'item';

                    // Tombol hapus
                    var btnRemove = document.createElement('button');
                    btnRemove.type = 'button';
                    btnRemove.className = 'btn-remove';
                    btnRemove.innerHTML = '&times;';
                    btnRemove.title = 'Hapus file ini';
                    btnRemove.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectedFiles.splice(index, 1);
                        syncInputFiles();
                    });
                    item.appendChild(btnRemove);

                    // Area thumbnail
                    var thumbWrap = document.createElement('div');
                    thumbWrap.className = 'thumb-wrap';

                    var ext = file.name.split('.').pop().toLowerCase();
                    if (file.type === 'application/pdf' || ext === 'pdf') {
                        var pdfWrap = document.createElement('div');
                        pdfWrap.className = 'pdf-icon-wrap';
                        pdfWrap.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' +
                            '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>' +
                            '<polyline points="14 2 14 8 20 8"></polyline>' +
                            '<line x1="16" y1="13" x2="8" y2="13"></line>' +
                            '<line x1="16" y1="17" x2="8" y2="17"></line>' +
                            '<polyline points="10 9 9 9 8 9"></polyline>' +
                            '</svg><span class="pdf-badge">PDF</span>';
                        thumbWrap.appendChild(pdfWrap);
                    } else {
                        var img = document.createElement('img');
                        var objectUrl = URL.createObjectURL(file);
                        img.src = objectUrl;
                        img.onload = function() {
                            URL.revokeObjectURL(objectUrl);
                        };
                        thumbWrap.appendChild(img);
                    }
                    item.appendChild(thumbWrap);

                    // Nama file
                    var nama = document.createElement('div');
                    nama.className = 'file-name';
                    nama.textContent = file.name;
                    nama.title = file.name;
                    item.appendChild(nama);

                    // Ukuran file
                    var ukuran = document.createElement('div');
                    ukuran.className = 'file-size';
                    ukuran.textContent = formatSize(file.size);
                    item.appendChild(ukuran);

                    box.appendChild(item);
                });
            }

            function handleFiles(files) {
                var errors = [];
                var fileArray = Array.prototype.slice.call(files);

                fileArray.forEach(function(file) {
                    var ext = file.name.split('.').pop().toLowerCase();

                    // Cek ekstensi
                    if (ALLOWED_EXTS.indexOf(ext) === -1) {
                        errors.push('File "' + file.name + '" memiliki format tidak didukung. Hanya JPG, PNG, WEBP, dan PDF yang diperbolehkan.');
                        return;
                    }

                    // Cek ukuran (< 5 MB)
                    if (file.size > MAX_SIZE) {
                        errors.push('File "' + file.name + '" (' + formatSize(file.size) + ') melebihi batas maksimal 5 MB.');
                        return;
                    }

                    // Cek duplikasi
                    var isDuplicate = selectedFiles.some(function(existing) {
                        return existing.name === file.name && existing.size === file.size && existing.lastModified === file.lastModified;
                    });
                    if (isDuplicate) {
                        return;
                    }

                    // Cek kuota jumlah file (maksimal 10)
                    if (selectedFiles.length >= MAX_FILES) {
                        errors.push('Batas maksimal ' + MAX_FILES + ' file telah tercapai. File "' + file.name + '" tidak ditambahkan.');
                        return;
                    }

                    selectedFiles.push(file);
                });

                showAlert(errors);
                syncInputFiles();
            }

            // Dropzone klik
            dropArea.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove')) return;
                input.click();
            });

            // Input change
            input.addEventListener('change', function() {
                if (input.files && input.files.length > 0) {
                    handleFiles(input.files);
                }
                // Reset input value supaya file yang sama bisa dipilih lagi jika sebelumnya dihapus
                input.value = '';
                // Kembalikan sync files ke input
                if (window.DataTransfer) {
                    try {
                        var dt = new DataTransfer();
                        selectedFiles.forEach(function(f) {
                            dt.items.add(f);
                        });
                        input.files = dt.files;
                    } catch (e) {}
                }
            });

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
                dropArea.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(function(eventName) {
                dropArea.addEventListener(eventName, function() {
                    dropArea.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(function(eventName) {
                dropArea.addEventListener(eventName, function() {
                    dropArea.classList.remove('dragover');
                }, false);
            });

            dropArea.addEventListener('drop', function(e) {
                var dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    handleFiles(dt.files);
                }
            }, false);
        })();

        // CKEditor perlu menyimpan isi editor ke textarea sebelum submit.
        document.querySelector('form').addEventListener('submit', function() {
            for (var name in CKEDITOR.instances) {
                if (CKEDITOR.instances.hasOwnProperty(name)) {
                    CKEDITOR.instances[name].updateElement();
                }
            }
        });

    </script>
    <script>
        // Unit Kerja hanya menampilkan unit milik RS yang dipilih.
        (function() {
            var rs = document.getElementById('KodePerusahaan');
            var unit = document.getElementById('UnitId');
            if (!rs || !unit) return;

            var semua = Array.prototype.slice.call(unit.options).slice(1);
            var terpilih = unit.value;

            function saring() {
                var kode = rs.value;
                unit.innerHTML = '';

                var kosong = document.createElement('option');
                kosong.value = '';
                kosong.textContent = kode ? '-- Pilih Unit Kerja --' : '-- Pilih Rumah Sakit dulu --';
                unit.appendChild(kosong);

                if (!kode) return;

                var cocok = 0;
                semua.forEach(function(opt) {
                    var perusahaanId = opt.getAttribute('data-perusahaan-id');
                    if (perusahaanId !== kode) return;

                    var salinan = opt.cloneNode(true);
                    salinan.selected = salinan.value === terpilih;
                    unit.appendChild(salinan);
                    cocok++;
                });

                if (cocok === 0) {
                    kosong.textContent = '-- Belum ada unit untuk RS ini --';
                }
            }

            rs.addEventListener('change', function() {
                terpilih = '';
                saring();
            });

            saring();
        })();
    </script>
</body>

</html>
