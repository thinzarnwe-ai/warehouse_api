<?php

namespace App\Exports;

use App\Models\SubDocument;
use App\Models\MainDocument;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExcelImportTemplate implements FromView,ShouldAutoSize,WithColumnWidths
{
    private $doc_id;

    public function __construct($doc_id)
    {
        $this->doc_id = $doc_id;
    }

        public function view(): View
        {
            // dd('hi');
            $doc =MainDocument::whereId($this->doc_id)->first();
            $data = SubDocument::where('main_doc_id', $this->doc_id)->get();

            // Modify the product code column to add single quotes
            $data->transform(function ($item) {
                $item->product_code = "'" . $item->product_code;
                return $item;
            });
            return view('customers.exports.import_template',['products'=>$data,'doc'=>$doc]);
        }

        public function columnWidths(): array
    {
        return [
            'B' => 55,
        ];
    }
}
