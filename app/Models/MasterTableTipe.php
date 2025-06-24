<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTableTipe extends Model
{
    protected $table = 'master_table_tipe';

    protected $fillable = [
        'name',
    ];
}
