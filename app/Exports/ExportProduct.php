<?php

namespace App\Exports;

use App\Models\SubDocument;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExportProduct implements FromView,ShouldAutoSize,WithColumnWidths,WithEvents
{

    private $doc_id,$export_type;

    public function __construct($doc_id,$export_type)
    {
        $this->doc_id = $doc_id;
        $this->export_type = $export_type;
    }

        public function view(): View
        {
            // $data = SubDocument::where('main_doc_id', $this->doc_id)->get();
            $data = SubDocument::where('main_doc_id', $this->doc_id)->whereNull('deleted_at')->orderBy('price','desc')->get();

            // foreach ($data as $item) {
                
                $data->transform(function ($item) {
                    $item->product_code = "'" . $item->product_code;
                    return $item;
                });
                // $item->product_code = (string)$item->product_code; // Convert to string
            // }
            
            // dd($this->export_type);
            if($this->export_type =='admin')
            {
                return view('customers.exports.products',['products'=>$data]);
            }
            else{
                return view('customers.exports.user_products',['products'=>$data]);
            }


        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $sheet = $event->sheet;
                    //----------------------------------------------PRODUCTS--------------------------------------------
                    $first_rows = ['A1','F1','J1','N1','R1'];
                    foreach ($first_rows as $f_row)
                    {
                        // $first_col_range = $f_row. $sheet->getHighestDataRow();

                        $sheet->getStyle($f_row)->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '00000000'],
                                ],
                            ],

                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }

                    $userColumns = ['F','G','H','I'];
                    foreach ($userColumns as $column) {
                        $columnRange = $column . '2:' . $column . $sheet->getHighestDataRow();
                        $sheet->getStyle($columnRange)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '00000000'],
                                ],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'fffbeb'],
                            ],
                            // 'alignment' => [
                            //     'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            //     'vertical' => Alignment::VERTICAL_CENTER,
                            // ],
                        ]);
                    }

                    $bmColumns = ['J','K','L','M'];
                    foreach ($bmColumns as $bm_column) {
                        $bmcolumnRange = $bm_column . '2:' . $bm_column . $sheet->getHighestDataRow();
                        $sheet->getStyle($bmcolumnRange)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '00000000'],
                                ],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'a5f3fc'],
                            ],
                            // 'alignment' => [
                            //     'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            //     'vertical' => Alignment::VERTICAL_CENTER,
                            // ],
                        ]);
                    }

                    $opColumns = ['N','O','P','Q'];
                    foreach ($opColumns as $op_column) {
                        $opcolumnRange = $op_column . '2:' . $op_column . $sheet->getHighestDataRow();
                        $sheet->getStyle($opcolumnRange)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '00000000'],
                                ],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'fef08a'],
                            ],
                            // 'alignment' => [
                            //     'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            //     'vertical' => Alignment::VERTICAL_CENTER,
                            // ],
                        ]);
                    }

                    $opColumns = ['R','S','T','U'];
                    foreach ($opColumns as $op_column) {
                        $opcolumnRange = $op_column . '2:' . $op_column . $sheet->getHighestDataRow();
                        $sheet->getStyle($opcolumnRange)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '00000000'],
                                ],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'f5d0fe'],
                            ],
                            // 'alignment' => [
                            //     'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            //     'vertical' => Alignment::VERTICAL_CENTER,
                            // ],
                        ]);
                    }

                    $opColumns = ['G','K','O','S'];
                    foreach ($opColumns as $op_column) {
                        $opcolumnRange = $op_column . '2:' . $op_column . $sheet->getHighestDataRow();
                        $sheet->getStyle($opcolumnRange)->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }

                    $opColumns = ['F','H','I','J','L','M','N','P','Q','R','T','U'];
                    foreach ($opColumns as $op_column) {
                        $opcolumnRange = $op_column . '2:' . $op_column . $sheet->getHighestDataRow();
                        $sheet->getStyle($opcolumnRange)->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }

                    $sheet->getStyle('F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fffbeb');
                    $sheet->getStyle('J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('a5f3fc');
                    $sheet->getStyle('N1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fef08a');
                    $sheet->getStyle('R1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f5d0fe');
                },
            ];
        }

        public function columnWidths(): array
    {
        return [
            'B' => 55,
        ];
    }


}
