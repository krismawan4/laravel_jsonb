<?php

use App\Models\MasterTableColumn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_table_column', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_table_id');
            $table->string('master_table_name');
            $table->jsonb('data');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('master_table_id')->references('id')->on('master_table')->onDelete('cascade');
        });

        $data = [
            [
                'master_table_id' => 1,
                'master_table_name' => 'Category',
                'data' => [
                    'type' => 'text',
                    'label' => 'Nama',
                    'order' => 1,
                    'field_name' => 'nama',
                    'is_visible' => true,
                    'is_editable' => true,
                    'placeholder' => 'Masukan Nama Category',
                    'default_value' => null,
                    'component_props' => [
                        [
                            'key' => 'regex',
                            'value' => '^[a-zA-Z0-9 ]+$',
                            'validation_message' => 'Format tidak valid',
                        ],
                        [
                            'key' => 'max_length',
                            'value' => '255',
                            'validation_message' => 'Maksimal 255 karakter',
                        ],
                        [
                            'key' => 'min_length',
                            'value' => '5',
                            'validation_message' => 'Minimal 1 karakter',
                        ],
                        [
                            'key' => 'is_required',
                            'value' => 'true',
                            'validation_message' => 'Input wajib diisi',
                        ],
                        [
                            'key' => 'is_unique',
                            'value' => 'true',
                            'validation_message' => 'Input harus unique',
                        ],
                    ],
                ],
            ],
            [
                'master_table_id' => 1,
                'master_table_name' => 'Category',
                'data' => [
                    'type' => 'number',
                    'label' => 'Harga',
                    'order' => 2,
                    'field_name' => 'harga',
                    'is_visible' => true,
                    'is_editable' => true,
                    'placeholder' => 'Harga',
                    'default_value' => null,
                    'component_props' => [
                        [
                            'key' => 'is_required',
                            'value' => 'true',
                            'validation_message' => 'Input wajib diisi',
                        ],
                        [
                            'key' => 'min_length',
                            'value' => '1',
                            'validation_message' => 'Minimal 1 karakter',
                        ],
                        [
                            'key' => 'regex',
                            'value' => '^[a-zA-Z0-9 ]+$',
                            'validation_message' => 'Format tidak valid',
                        ],
                        [
                            'key' => 'max_length',
                            'value' => '255',
                            'validation_message' => 'Maksimal 255 karakter',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $item) {
            MasterTableColumn::create($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_table_column');
    }
};
