<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTableTipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder untuk master_table_tipe
        $tipeData = ['text', 'number', 'date', 'radio', 'checkbox', 'email'];

        foreach ($tipeData as $tipe) {
            DB::table('master_table_tipe')->insert([
                'name' => $tipe,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
