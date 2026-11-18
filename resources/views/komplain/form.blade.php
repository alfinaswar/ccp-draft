<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formulir Evaluasi BMHP Umum</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #111;
            background: #f2f4f7;
            margin: 0;
            padding: 24px 12px;
            line-height: 1.5;
        }

        .sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 48px 56px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, .12);
        }

        .kop {
            font-size: 26px;
            font-weight: bold;
            color: #d21f26;
            letter-spacing: .5px;
            margin-bottom: 24px;
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

        .upload-box {
            border: 1px dashed #888;
            padding: 12px;
            border-radius: 4px;
            background: #fafafa;
        }

        .upload-hint {
            font-size: 12px;
            color: #555;
            margin-top: 4px;
        }

        .preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .preview .item {
            width: 120px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 6px;
            background: #fff;
            text-align: center;
            font-size: 11px;
            word-break: break-all;
        }

        .preview .item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            display: block;
            margin-bottom: 4px;
        }

        .preview .item .pdf {
            font-size: 28px;
            color: #c0392b;
            line-height: 80px;
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
        <div class="kop">RS AWAL BROS</div>
        <h1>Formulir Evaluasi BMHP Umum</h1>

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
                            <option value="{{ $p->Kode }}"
                                {{ old('KodePerusahaan') == $p->Kode ? 'selected' : '' }}>{{ $p->Nama }}</option>
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

            <div style="margin-top:16px;">
                <div class="label">&bull;&nbsp; Data Pendukung</div>
                <div class="value">
                    <div class="upload-box">
                        <input type="file" name="Lampiran[]" id="Lampiran" multiple
                            accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <div class="upload-hint">
                            Bisa foto atau PDF, maksimal 5 file, tiap file maksimal 5 MB.
                        </div>
                    </div>
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

        // Preview file pendukung sebelum dikirim.
        (function() {
            var input = document.getElementById('Lampiran');
            var box = document.getElementById('previewLampiran');
            if (!input || !box) return;

            input.addEventListener('change', function() {
                box.innerHTML = '';

                Array.prototype.forEach.call(input.files, function(file) {
                    var item = document.createElement('div');
                    item.className = 'item';

                    if (file.type === 'application/pdf') {
                        var ikon = document.createElement('div');
                        ikon.className = 'pdf';
                        ikon.textContent = 'PDF';
                        item.appendChild(ikon);
                    } else {
                        var img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.onload = function() {
                            URL.revokeObjectURL(img.src);
                        };
                        item.appendChild(img);
                    }

                    var nama = document.createElement('div');
                    nama.textContent = file.name;
                    item.appendChild(nama);

                    box.appendChild(item);
                });
            });
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
                    if (opt.getAttribute('data-perusahaan') !== kode) return;

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
