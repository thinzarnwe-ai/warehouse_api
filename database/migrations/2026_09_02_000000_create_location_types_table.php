<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('sale'); // sale | warehouse
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $defaults = [
            ['TOP_MID_WALL', 'Top stock_Middle shelve & Wall shelve', 'sale', 1],
            ['PROMOTION_ZONE_GF', 'Promotion zone,Ground floor', 'sale', 2],
            ['STATIONARY_DIGITAL', 'Stationary & Digital-Displays', 'sale', 3],
            ['HARDWARE_TOOLS', 'Hardware & Tools Displays', 'sale', 4],
            ['DOOR_WINDOW', 'Door & Window Displays', 'sale', 5],
            ['PAINT_CHEMICAL', 'Paint & Chemical-Displays', 'sale', 6],
            ['STRUCTURE_DISPLAYS', 'Structure -Displays', 'sale', 7],
            ['GARDEN_ACCESSORIES', 'Garden & Accessories - Displays', 'sale', 8],
            ['SANITARY_WARE', 'Sanitary Ware -Displays', 'sale', 9],
            ['SURFACE_COVERING', 'Surface Covering- Displays', 'sale', 10],
            ['HOUSEWARE_KITCHEN', 'Houseware & Kitchen -Displays', 'sale', 11],
            ['HOME_APPLIANCE', 'Home Appliance- Display', 'sale', 12],
            ['ELECTRICAL_ACCESSORIES', 'Electrical & Accessories Displays', 'sale', 13],
            ['FURNITURE_BEDDING', 'Furniture & Bedding', 'sale', 14],
            ['OUTSIDE_STORE', 'Outside Store- Displays', 'sale', 15],
            ['RG_WAREHOUSE', 'RG Warehouse', 'warehouse', 16],
        ];

        foreach ($defaults as [$code, $name, $category, $sortOrder]) {
            DB::table('location_types')->insert([
                'code' => $code,
                'name' => $name,
                'category' => $category,
                'is_active' => true,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('location_types');
    }
};
