<?php

namespace App\Exports;

use App\Models\MasterVendor;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class MasterVendorExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        // Pastikan setiap sheet dibuat dengan No dimulai dari 1 (handled in sheet class below)
        return [
            new MasterVendorJenisSheet('Medis'),
            new MasterVendorJenisSheet('Umum'),
        ];
    }
}

// Sheet for Jenis Medis / Umum
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

class MasterVendorJenisSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithCustomStartCell,
    WithEvents
{
    protected $jenis;
    protected $collection;
    protected $no; // penomoran untuk sheet ini

    // 8 kolom: Jenis, Nama, Alamat, NoHp, Email, NamaPic, NoHpPic, Status
    const LAST_COL = 'H';
    const TABLE_START = 'A5';
    const HEADING_ROW = 5;
    const FIRST_DATA = 6;

    public function __construct($jenis)
    {
        $this->jenis = $jenis;
        $this->no = 1; // penomoran ulang setiap sheet
    }

    public function collection()
    {
        // ambil semua data vendor dengan jenis tertentu TANPA filter Status == Y
        $this->collection = MasterVendor::where('Jenis', $this->jenis)
            ->orderBy('Nama')
            ->get();
        return $this->collection;
    }

    public function startCell(): string
    {
        return self::TABLE_START;
    }

    public function headings(): array
    {
        return [
            'No',
            'Jenis',
            'Nama',
            'Alamat',
            'NoHp',
            'Email',
            'NamaPic',
            'NoHpPic',
            // 'Status',  // include if you want the status column visible
        ];
    }

    public function map($row): array
    {
        // gunakann property $this->no agar setiap sheet penomoran ulang dari 1
        return [
            $this->no++,
            $row->Jenis ?? '-',
            $row->Nama ?? '-',
            $row->Alamat ?? '-',
            $row->NoHp ?? '-',
            $row->Email ?? '-',
            $row->NamaPic ?? '-',
            $row->NoHpPic ?? '-',
            // $row->Status ?? '-',  // include if you want the status column visible
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $total = $this->collection ? $this->collection->count() : 0;
        $lastRow = self::HEADING_ROW + $total;
        $lastCol = self::LAST_COL;

        // Heading style
        $sheet->getStyle(self::HEADING_ROW . ':' . self::HEADING_ROW)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(self::HEADING_ROW)->setRowHeight(28);

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
            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $col) {
                $sheet->getStyle("{$col}" . self::FIRST_DATA . ":{$col}{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
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

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = self::LAST_COL;

                // Merge semua kolom untuk baris 1-4
                foreach ([1, 2, 3, 4] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                }

                // Row 1: Judul sesuai jenis sheet
                $sheet->setCellValue('A1', 'LAPORAN MASTER VENDOR JENIS ' . strtoupper($this->jenis));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'name' => 'Arial', 'color' => ['argb' => 'FF1F3864']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Row 2: Periode
                $periode = "Periode : Semua Data";
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

                // Footer
                if ($total > 0) {
                    $footerRow = $lastRow + 1;
                    $sheet->mergeCells("A{$footerRow}:{$lastCol}{$footerRow}");
                    $sheet->setCellValue("A{$footerRow}", "Total Vendor {$this->jenis} : {$total} Data");
                    $sheet->getStyle("A{$footerRow}:{$lastCol}{$footerRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['argb' => 'FFFFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D5499']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']]],
                    ]);
                    $sheet->getRowDimension($footerRow)->setRowHeight(22);
                }

                // Freeze heading
                $sheet->freezePane('A' . self::FIRST_DATA);

                // Lebar kolom
                $sheet->getColumnDimension('A')->setWidth(6);   // No
                $sheet->getColumnDimension('B')->setWidth(14);  // Jenis
                $sheet->getColumnDimension('C')->setWidth(26);  // Nama
                $sheet->getColumnDimension('D')->setWidth(32);  // Alamat
                $sheet->getColumnDimension('E')->setWidth(16);  // NoHp
                $sheet->getColumnDimension('F')->setWidth(22);  // Email
                $sheet->getColumnDimension('G')->setWidth(20);  // NamaPic
                $sheet->getColumnDimension('H')->setWidth(16);  // NoHpPic

                // Page setup
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(1.0)->setBottom(1.0)->setLeft(0.7)->setRight(0.7);

                $sheet->getHeaderFooter()
                    ->setOddHeader('&C&B Laporan Master Vendor Jenis ' . $this->jenis)
                    ->setOddFooter('&LDicetak: ' . Carbon::now()->format('d/m/Y H:i') . '&RHalaman &P dari &N');
            },
        ];
    }

    public function title(): string
    {
        return $this->jenis;
    }
}
