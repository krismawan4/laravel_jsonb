<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('master_data');
        Schema::create('master_data', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT
            $table->foreignId('master_table_id')->references('id')->on('master_table');
            $table->string('master_table_name', 100);
            $table->foreignId('parent_id')->nullable()->references('id')->on('master_data');
            $table->string('parent_table_name', 100)->nullable();
            $table->jsonb('data');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
