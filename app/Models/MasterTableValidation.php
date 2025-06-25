<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MasterTableValidation extends Model
{
    use SoftDeletes;
    protected $table = 'master_table_validation';

    protected $fillable = [
        'key',
        'value',
        'message',
    ];
}
