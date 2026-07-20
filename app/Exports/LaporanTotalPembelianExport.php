<?php

namespace App\Exports;

use App\Models\MasterPerusahaan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanTotalPembelianExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithCustomStartCell,
    WithEvents
{
    protected $request;
    protected $collection;
    protected $no;

    // 6 Kolom: No, Kode RS, Nama RS, Harga Awal, Harga Nego, Selisih
    const LAST_COL = 'F';
    const TABLE_START = 'A5';
    const HEADING_ROW = 5;
    const FIRST_DATA = 6;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->no = 1;
    }

    public function collection()
    {
        $query = MasterPerusahaan::select([
            'master_perusahaans.Kode',
            'master_perusahaans.Nama',
            DB::raw('COALESCE(SUM(rekomendasi_details.HargaAwal), 0) as TotalHargaAwal'),
            DB::raw('COALESCE(SUM(rekomendasi_details.HargaNego), 0) as TotalHargaNego'),
            DB::raw('COALESCE(SUM(rekomendasi_details.HargaAwal - rekomendasi_details.HargaNego), 0) as TotalSelisih')
        ])
            ->leftJoin('rekomendasi_details', 'master_perusahaans.Kode', '=', 'rekomendasi_details.KodePerusahaan')
            ->whereNull('rekomendasi_details.deleted_at')
            ->where('rekomendasi_details.Rekomendasi', '1');

        if ($this->request->filled('start_month')) {
            $query->where('rekomendasi_details.created_at', '>=', Carbon::createFromFormat('Y-m', $this->request->start_month)->startOfMonth());
        }
        if ($this->request->filled('end_month')) {
            $query->where('rekomendasi_details.created_at', '<=', Carbon::createFromFormat('Y-m', $this->request->end_month)->endOfMonth());
        }

        $this->collection = $query->groupBy('master_perusahaans.id', 'master_perusahaans.Kode', 'master_perusahaans.Nama')->get();

        return $this->collection;
    }

    public function startCell(): string
    {
        return self::TABLE_START;
    }

    public function headings(): array
    {
        // Only provide headers up to column F
        return [
            'No',                                   // A
            'Kode RS',                              // B
            'Nama Rumah Sakit / Cisco',             // C
            'Total Belanja Pengajuan dari RS',      // D
            'Total Belanja Rekomendasi CCP',        // E
            'Total Selisih Harga'                   // F
        ];
    }

    public function map($row): array
    {
        return [
            $this->no++,
            $row->Kode ?? '-',
            $row->Nama ?? '-',
            'Rp ' . number_format($row->TotalHargaAwal, 0, ',', '.'),
            'Rp ' . number_format($row->TotalHargaNego, 0, ',', '.'),
            'Rp ' . number_format($row->TotalSelisih, 0, ',', '.')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $total = $this->collection ? $this->collection->count() : 0;
        $lastRow = self::HEADING_ROW + $total;
        $lastCol = self::LAST_COL;

        // 1. Heading style (Row 5)
        $sheet->getStyle("A" . self::HEADING_ROW . ":{$lastCol}" . self::HEADING_ROW)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']], // Dark Blue
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(self::HEADING_ROW)->setRowHeight(28);

        // 2. Data rows style (Zebra striping)
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
            // Alignment per kolom (No center, sisanya left)
            $sheet->getStyle("A" . self::FIRST_DATA . ":A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            foreach (['B', 'C', 'D', 'E', 'F'] as $col) {
                $sheet->getStyle("{$col}" . self::FIRST_DATA . ":{$col}{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
        }

        // 3. Border seluruh tabel
        $sheet->getStyle("A" . self::HEADING_ROW . ":{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFB0BEC5']],
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']],
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = self::LAST_COL;

                // Merge semua kolom untuk baris 1-4 (hanya sampai F)
                foreach ([1, 2, 3, 4] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                }

                // Row 1: Judul
                $sheet->setCellValue('A1', 'LAPORAN TOTAL PEMBELIAN PER RS (REKOMENDASI 1)');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'name' => 'Arial', 'color' => ['argb' => 'FF1F3864']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Row 2: Periode (Dinamis berdasarkan filter)
                $periode = "Semua Periode";
                if ($this->request->filled('start_month') && $this->request->filled('end_month')) {
                    $start = Carbon::createFromFormat('Y-m', $this->request->start_month)->translatedFormat('F Y');
                    $end = Carbon::createFromFormat('Y-m', $this->request->end_month)->translatedFormat('F Y');
                    $periode = "Periode : {$start} s/d {$end}";
                } elseif ($this->request->filled('start_month')) {
                    $periode = "Periode : Mulai " . Carbon::createFromFormat('Y-m', $this->request->start_month)->translatedFormat('F Y');
                } elseif ($this->request->filled('end_month')) {
                    $periode = "Periode : Sampai " . Carbon::createFromFormat('Y-m', $this->request->end_month)->translatedFormat('F Y');
                }

                $sheet->setCellValue('A2', $periode);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['argb' => 'FF374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // Row 3: Timestamp
                $now = Carbon::now()->translatedFormat('d F Y, H:i');
                $sheet->setCellValue('A3', "Dicetak pada : {$now} WIB");
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'name' => 'Arial', 'color' => ['argb' => 'FF6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // Row 4: Garis separator
                $sheet->getRowDimension(4)->setRowHeight(5);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $total = $this->collection ? $this->collection->count() : 0;
                $lastRow = self::HEADING_ROW + $total;
                $lastCol = self::LAST_COL;

                // Hitung Grand Total Selisih untuk Footer
                $grandTotal = $this->collection ? $this->collection->sum('TotalSelisih') : 0;
                $grandTotalFormatted = 'Rp ' . number_format($grandTotal, 0, ',', '.');

                // Footer Row
                $footerRow = $lastRow + 1;
                $sheet->mergeCells("A{$footerRow}:{$lastCol}{$footerRow}");
                $sheet->setCellValue("A{$footerRow}", "TOTAL PENGHEMATAN KESELURUHAN : {$grandTotalFormatted} ({$total} Data RS)");
                $sheet->getStyle("A{$footerRow}:{$lastCol}{$footerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D5499']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']]],
                ]);
                $sheet->getRowDimension($footerRow)->setRowHeight(26);

                // Freeze heading
                $sheet->freezePane('A' . self::FIRST_DATA);

                // Lebar kolom (Disesuaikan untuk data keuangan)
                $sheet->getColumnDimension('A')->setWidth(6);   // No
                $sheet->getColumnDimension('B')->setWidth(15);  // Kode RS
                $sheet->getColumnDimension('C')->setWidth(40);  // Nama RS
                $sheet->getColumnDimension('D')->setWidth(22);  // Harga Awal
                $sheet->getColumnDimension('E')->setWidth(22);  // Harga Nego
                $sheet->getColumnDimension('F')->setWidth(22);  // Selisih

                // Page setup (Landscape A4)
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(1.0)->setBottom(1.0)->setLeft(0.7)->setRight(0.7);

                // Header & Footer Excel Print
                $sheet->getHeaderFooter()
                    ->setOddHeader('&C&B Laporan Total Pembelian per RS (Rekomendasi 1)')
                    ->setOddFooter('&LDicetak: ' . Carbon::now()->format('d/m/Y H:i') . '&RHalaman &P dari &N');
            },
        ];
    }

    public function title(): string
    {
        return 'Total Pembelian';
    }
}
