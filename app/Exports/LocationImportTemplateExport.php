<?php

namespace App\Exports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LocationImportTemplateExport implements FromArray, WithEvents
{
    private const ORANGE = 'FFFFC000';

    private const LIGHT_BLUE = 'FFBDD7EE';

    private const RED = 'FFFF0000';

    private const YELLOW = 'FFFFFF00';

    public function __construct(private Branch $branch) {}

    public function array(): array
    {
        $name = $this->branch->branch_name;
        $short = strtoupper((string) ($this->branch->branch_short_name ?? ''));
        $saleType = 'Top stock_Middle shelve & Wall shelve';
        $buildCode = static fn (string $bay): string => "{$short}S_B_27_F_{$bay}_01";

        return [
            ['No', 'Branch', 'Location Type', 'Zone', 'Row', 'F/B', 'Bay', 'Level', '', 'Branch Short C', 'Type', 'Location Code'],
            [1, $name, $saleType, 'B', '27', 'F', '01', '01', '', $short, 'S', $buildCode('01')],
            [2, $name, $saleType, 'B', '27', 'F', '02', '01', '', $short, 'S', $buildCode('02')],
            [3, $name, $saleType, 'B', '27', 'F', '03', '01', '', $short, 'S', $buildCode('03')],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $headerFills = [
                    'A1' => self::ORANGE,
                    'B1' => self::ORANGE,
                    'C1' => self::ORANGE,
                    'D1' => self::ORANGE,
                    'E1' => self::ORANGE,
                    'F1' => self::ORANGE,
                    'G1' => self::ORANGE,
                    'H1' => self::ORANGE,
                    'I1' => self::LIGHT_BLUE,
                    'J1' => self::ORANGE,
                    'K1' => self::ORANGE,
                    'L1' => self::RED,
                ];

                foreach ($headerFills as $cell => $color) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => $color],
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['argb' => $cell === 'L1' ? 'FFFFFFFF' : 'FF000000'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFD9D9D9'],
                            ],
                        ],
                    ]);
                }

                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
                    for ($row = 2; $row <= 4; $row++) {
                        $styles = [
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => 'FFD9D9D9'],
                                ],
                            ],
                        ];

                        if ($col === 'D') {
                            $styles['fill'] = [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => self::YELLOW],
                            ];
                        }

                        $sheet->getStyle("{$col}{$row}")->applyFromArray($styles);
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(36);
                $sheet->getColumnDimension('D')->setWidth(8);
                $sheet->getColumnDimension('E')->setWidth(8);
                $sheet->getColumnDimension('F')->setWidth(8);
                $sheet->getColumnDimension('G')->setWidth(8);
                $sheet->getColumnDimension('H')->setWidth(8);
                $sheet->getColumnDimension('I')->setWidth(4);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getColumnDimension('K')->setWidth(8);
                $sheet->getColumnDimension('L')->setWidth(22);
                $sheet->freezePane('A2');
            },
        ];
    }
}
