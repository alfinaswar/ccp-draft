<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formulir Terkirim</title>
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
            line-height: 1.55;
        }

        .sheet {
            max-width: 640px;
            margin: 40px auto;
            background: #fff;
            padding: 36px 40px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, .12);
            text-align: center;
        }

        .kop {
            font-size: 22px;
            font-weight: bold;
            color: #d21f26;
            letter-spacing: .5px;
        }

        h1 {
            font-size: 17px;
            margin: 18px 0 10px;
        }

        .kode {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
            background: #f4f6f8;
            border: 1px dashed #999;
            padding: 10px;
            margin: 18px 0;
        }

        p {
            text-align: left;
        }

        a {
            color: #d21f26;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="kop">RS AWAL BROS</div>
        <h1>Formulir Evaluasi BMHP Umum Telah Dikirim</h1>

        <div class="kode">{{ $komplain->KodeKomplain }}</div>

        <p>Terima kasih. Formulir Anda sudah kami terima dan akan diteruskan ke Kepala Divisi, Group Head, Logistik
            Umum, dan CCP untuk ditindaklanjuti.</p>

        <p>Simpan nomor di atas untuk menanyakan status penanganan.</p>

        <p><a href="{{ route('komplain.form') }}">Isi formulir baru</a></p>
    </div>
</body>

</html>
