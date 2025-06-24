<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTableValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder untuk master_table_validation
        $validationData = [
            [
                'key' => 'regex',
                'value' => '^[a-zA-Z0-9 ]+$',
                'message' => 'Format tidak valid',
            ],
            [
                'key' => 'max_length',
                'value' => '255',
                'message' => 'Maksimal 255 karakter',
            ],
            [
                'key' => 'min_length',
                'value' => '1',
                'message' => 'Minimal 1 karakter',
            ],
            [
                'key' => 'is_required',
                'value' => 'true',
                'message' => 'Input wajib diisi',
            ],
            [
                'key' => 'is_unique',
                'value' => 'true',
                'message' => 'Input harus unique',
            ],
        ];

        foreach ($validationData as $validation) {
            DB::table('master_table_validation')->insert([
                'key' => $validation['key'],
                'value' => $validation['value'],
                'message' => $validation['message'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
