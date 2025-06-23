<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTableColumn extends Model
{
    use SoftDeletes;

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
