<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterValidation extends Model
{
    use SoftDeletes;

    protected $table = 'master_validation';

    protected $fillable = [
        'master_table_id',
        'name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
