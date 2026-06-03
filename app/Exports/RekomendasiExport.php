<?php

namespace App\Exports;

use App\Models\Rekomendasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

/**
 * Layout baris:
 *   1  → Judul laporan
 *   2  → Periode (tanggal awal s/d tanggal akhir)
 *   3  → Dicetak pada
 *   4  → Garis dekoratif (separator)
 *   5  → Heading tabel  ← WithCustomStartCell mulai di sini
 *   6+ → Data
 */
class RekomendasiExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithCustomStartCell,
    WithEvents
{
    protected $filters;
    protected $collection;
    protected $totalHargaNego = 0;
    protected $totalBarang = 0; // Tambahan untuk jumlah barang/jasa

    const LAST_COL = 'M'; // Update dari 'L' ke 'M'
    const TABLE_START = 'A5';   // tabel (heading + data) mulai baris 5
    const HEADING_ROW = 5; // headeing table nya
    const FIRST_DATA = 6; // data pertama

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // ── Data source ──────────────────────────────────────────────────────────

    public function collection()
    {
        $query = Rekomendasi::with(['getRekomedasiDetail.getBarang.getMerk', 'getRekomedasiDetail.getNamaVendor', 'getPengajuan', 'getPerusahaan'])
            ->whereNotNull('DisetujuiPada')
            ->when(!empty($this->filters['tanggal_awal']), fn($q) => $q->whereDate('DisetujuiPada', '>=', $this->filters['tanggal_awal']))
            ->when(!empty($this->filters['tanggal_akhir']), fn($q) => $q->whereDate('DisetujuiPada', '<=', $this->filters['tanggal_akhir']))
            ->when(!empty($this->filters['perusahaan']), fn($q) => $q->where('KodePerusahaan', $this->filters['perusahaan']))
            ->when(!empty($this->filters['namaBarang']) && is_array($this->filters['namaBarang']), function ($q) {
                $q->whereHas('getRekomedasiDetail', function ($sub) {
                    $sub->whereIn('NamaPermintaan', $this->filters['namaBarang'])
                        ->where('Rekomendasi', 1);
                });
            });


        $totalHarga = 0;
        $totalBarang = 0;
        $this->collection = $query->get()->flatMap(function ($item, $index) use (&$totalHarga, &$totalBarang) {
            $pengajuan = $item->getPengajuan;
            $perusahaan = $item->getPerusahaan;
            $details = $item->getRekomedasiDetail;
            $rows = [];

            foreach ($details as $detailIndex => $detail) {
                if ($detail->Rekomendasi != 1) {
                    continue; // skip if not recommended
                }
                $hargaNego = $detail ? $detail->HargaNego : 0;
                $hargaAwal = $detail ? $detail->HargaAwal : 0; // Tambahkan HargaAwal
                $totalHarga += $hargaNego;
                $totalBarang++; // Tambahkan setiap ada detail direkomendasikan

                $rows[] = (object) [
                    'No' => $index + 1,
                    'Kode' => $pengajuan ? $pengajuan->KodePengajuan : '-',
                    'AsalPengajuan' => $perusahaan ? $perusahaan->NamaLengkap : '-',
                    'TanggalPengajuan' => $pengajuan ? $pengajuan->DiajukanPada : '-',
                    'NamaBarang' => ($detail && $detail->getBarang) ? $detail->getBarang->Nama : '-',
                    'Merek' => optional(optional($detail?->getBarang)->getMerk)->Nama ?? '-',
                    'Tipe' => $detail && $detail->getBarang ? ($detail->getBarang->Tipe ?? '-') : '-',
                    'Vendor' => $detail && $detail->getNamaVendor ? $detail->getNamaVendor->Nama : '-',
                    'NamaPic' => $pengajuan ? $pengajuan->getVendor[0]->NamaPic : null,
                    'KontakPic' => $pengajuan ? $pengajuan->getVendor[0]->KontakPic : null,
                    'HargaAwal' => $detail ? 'Rp ' . number_format($detail->HargaAwal, 0, ',', '.') : '-', // HargaAwal
                    'HargaRekomendasi' => $detail ? 'Rp ' . number_format($detail->HargaNego, 0, ',', '.') : '-',
                    'HargaNegoRaw' => $hargaNego,
                    'Status' => 'Rekomendasi Telah Dikeluarkan',
                ];
            }

            return $rows;
        })->values();

        $this->totalHargaNego = $totalHarga;
        $this->totalBarang = $totalBarang;
        return $this->collection;
    }

    public function startCell(): string
    {
        return self::TABLE_START;   // heading tabel di-render mulai baris 5
    }

    public function headings(): array
    {
        return [
            'No.',
            'Kode Pengajuan',
            'Asal Pengajuan',
            'Tanggal Pengajuan',
            'Nama Barang / Jasa',
            'Merek',
            'Tipe',
            'Vendor',
            'Nama PIC',
            'Kontak',
            'Harga Awal',
            'Harga Rekomendasi',
            'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $row->No,
            $row->Kode,
            $row->AsalPengajuan,
            $row->TanggalPengajuan,
            $row->NamaBarang,
            $row->Merek,
            $row->Tipe,
            $row->Vendor,
            $row->NamaPic,
            $row->KontakPic,
            $row->HargaAwal, // Harga Awal baru
            $row->HargaRekomendasi,
            $row->Status,
        ];
    }

    // ── Styles (dipanggil setelah data di-render) ────────────────────────────

    public function styles(Worksheet $sheet)
    {
        $total = $this->collection ? $this->collection->count() : 0;
        $lastRow = self::HEADING_ROW + $total;   // baris terakhir data
        $lastCol = self::LAST_COL;

        // Heading tabel
        $sheet->getStyle(self::HEADING_ROW . ':' . self::HEADING_ROW)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(self::HEADING_ROW)->setRowHeight(28);

        // Data rows: warna selang-seling + tinggi baris
        for ($r = self::FIRST_DATA; $r <= $lastRow; $r++) {
            $bg = ($r % 2 === 0) ? 'FFE8EEF7' : 'FFFFFFFF';
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'font' => ['name' => 'Arial', 'size' => 10],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        if ($total > 0) {
            // Alignment per kolom
            foreach (['A', 'B', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M'] as $col) { // Tambah kolom 'M'
                // Sesuaikan heading baris dan kolom: A-M
                if ($col === 'L' || $col === 'K') { // HargaAwal (K), HargaRekomendasi (L) align right
                    $sheet->getStyle("{$col}" . self::FIRST_DATA . ":{$col}{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } else {
                    $sheet->getStyle("{$col}" . self::FIRST_DATA . ":{$col}{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }
        }

        // Border seluruh tabel
        $sheet->getStyle("A" . self::HEADING_ROW . ":{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFB0BEC5']],
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']],
            ],
        ]);

        return [];
    }

    // ── Events ───────────────────────────────────────────────────────────────

    public function registerEvents(): array
    {
        return [
                // BeforeSheet: tulis baris header info (1-4) SEBELUM tabel di-render
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = self::LAST_COL;

                // Merge semua kolom untuk baris 1-4
                foreach ([1, 2, 3, 4] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                }

                // ── Baris 1: Judul ──────────────────────────────────────
                $sheet->setCellValue('A1', 'LAPORAN REKOMENDASI PENGADAAN BARANG / JASA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'name' => 'Arial', 'color' => ['argb' => 'FF1F3864']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // ── Baris 2: Periode ────────────────────────────────────
                $awal = !empty($this->filters['tanggal_awal'])
                    ? Carbon::parse($this->filters['tanggal_awal'])->translatedFormat('d F Y')
                    : 'Semua Tanggal';
                $akhir = !empty($this->filters['tanggal_akhir'])
                    ? Carbon::parse($this->filters['tanggal_akhir'])->translatedFormat('d F Y')
                    : 'Semua Tanggal';

                $periode = ($awal === $akhir)
                    ? "Periode : {$awal}"
                    : "Periode : {$awal}  s/d  {$akhir}";

                $sheet->setCellValue('A2', $periode);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['argb' => 'FF374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ── Baris 3: Timestamp cetak ────────────────────────────
                $now = Carbon::now()->translatedFormat('d F Y, H:i');
                $sheet->setCellValue('A3', "Dicetak pada : {$now} WIB");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'name' => 'Arial', 'color' => ['argb' => 'FF6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // ── Baris 4: Garis separator ────────────────────────────
                // $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
                //     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
                // ]);
                $sheet->getRowDimension(4)->setRowHeight(5);
            },

                // AfterSheet: tambah footer total + pengaturan halaman
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $total = $this->collection ? $this->collection->count() : 0;
                $lastRow = self::HEADING_ROW + $total;
                $lastCol = self::LAST_COL;

                // ── Footer: total data ───────────────────────────────────
                if ($total > 0) {
                    $footerRow = $lastRow + 1;
                    $sheet->mergeCells("A{$footerRow}:{$lastCol}{$footerRow}");
                    $sheet->setCellValue("A{$footerRow}", "Total Data : {$total} Record");
                    $sheet->getStyle("A{$footerRow}:{$lastCol}{$footerRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['argb' => 'FFFFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D5499']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']]],
                    ]);
                    $sheet->getRowDimension($footerRow)->setRowHeight(22);

                    // Tambah baris total harga nego
                    $footerRowTotalHarga = $footerRow + 1;
                    $sheet->mergeCells("A{$footerRowTotalHarga}:{$lastCol}{$footerRowTotalHarga}");
                    $sheet->setCellValue("A{$footerRowTotalHarga}", "Total Harga Rekomendasi : Rp " . number_format($this->totalHargaNego, 0, ',', '.'));
                    $sheet->getStyle("A{$footerRowTotalHarga}:{$lastCol}{$footerRowTotalHarga}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['argb' => 'FF1F3864']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE3ECFE']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF1F3864']]],
                    ]);
                    $sheet->getRowDimension($footerRowTotalHarga)->setRowHeight(22);

                    // Tambahkan baris jumlah barang/jasa total
                    // $footerRowTotalBarang = $footerRowTotalHarga + 1;
                    // $sheet->mergeCells("A{$footerRowTotalBarang}:{$lastCol}{$footerRowTotalBarang}");
                    // $sheet->setCellValue("A{$footerRowTotalBarang}", "Jumlah Barang/Jasa Direkomendasikan : " . $this->totalBarang . " item");
                    // $sheet->getStyle("A{$footerRowTotalBarang}:{$lastCol}{$footerRowTotalBarang}")->applyFromArray([
                    //     'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['argb' => 'FF1F3864']],
                    //     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEDFBF6']],
                    //     'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    //     'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF1F3864']]],
                    // ]);
                    // $sheet->getRowDimension($footerRowTotalBarang)->setRowHeight(20);
                }

                // ── Freeze pane: beku di bawah heading tabel ─────────────
                $sheet->freezePane('A' . self::FIRST_DATA);

                // ── Lebar kolom ──────────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(20);
                $sheet->getColumnDimension('I')->setWidth(22);
                $sheet->getColumnDimension('J')->setWidth(34);
                $sheet->getColumnDimension('K')->setWidth(34); // Harga Awal
                $sheet->getColumnDimension('L')->setWidth(34); // Harga Rekomendasi
                $sheet->getColumnDimension('M')->setWidth(34); // Status

                // ── Page setup (cetak) ───────────────────────────────────
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(1.0)->setBottom(1.0)->setLeft(0.7)->setRight(0.7);

                $sheet->getHeaderFooter()
                    ->setOddHeader('&C&B Laporan Rekomendasi Pengadaan')
                    ->setOddFooter('&LDicetak: ' . Carbon::now()->format('d/m/Y H:i') . '&RHalaman &P dari &N');
            },
        ];
    }

    public function title(): string
    {
        return 'Laporan Rekomendasi';
    }
}
