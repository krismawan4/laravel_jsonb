<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MasterTableTipe extends Model
{
    use SoftDeletes;
    protected $table = 'master_table_tipe';

    protected $fillable = [
        'name',
    ];
}
