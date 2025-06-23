<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTableColumn extends Model
{
    protected $table = 'master_table_column';

    protected $fillable = [
        'master_table_id',
        'master_table_name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
