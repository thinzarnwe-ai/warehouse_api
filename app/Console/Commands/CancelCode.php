<?php

namespace App\Console\Commands;

use App\Models\SubDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelCode extends Command
{

    protected $signature = 'remove:cancel';


    protected $description = 'Command description';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $conn           = DB::connection('pg_global');
        $sub_docs       = SubDocument::get();
        $product_codes  = $sub_docs->pluck('product_code')->toArray();
        $product_codes  = "('" . implode("','", $product_codes) . "')";
        $data           = $conn->select("select product_name1,product_code FROM masterdata.master_product where product_code in $product_codes and inactive='I'
        ");
        
        Log::info($data);
    }
}
