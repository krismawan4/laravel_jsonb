<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTable extends Model
{
    use SoftDeletes;

    protected $table = 'master_table';

    protected $fillable = [
        'name',
    ];
}
