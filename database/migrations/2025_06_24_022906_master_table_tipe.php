<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_table_tipe', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // text, number, date, dll
            $table->timestamps();
            $table->softDeletes(); // menghasilkan kolom deleted_at bertipe timestamp nullable
        });

        // panggil artisan seeder
        Artisan::call('db:seed', [
            '--class' => 'MasterTableTipeSeeder',
        ]);

    }

    public function down(): void
    {
        Schema::dropIfExists('master_table_tipe');
    }
};
