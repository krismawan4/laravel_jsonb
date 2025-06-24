<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTableValidation extends Model
{
    protected $table = 'master_table_validation';

    protected $fillable = [
        'key',
        'value',
        'message',
    ];
}
