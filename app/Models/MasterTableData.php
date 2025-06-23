<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTableData extends Model
{
    protected $table = 'master_table_data';

    protected $fillable = [
        'parent_id',
        'master_table_id',
        'master_table_name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
