<?php

namespace App\Http\Controllers;

use App\Models\MasterTable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MasterController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Master/Index');
    }

    public function master_table_column(Request $request): Response
    {
        $table = MasterTable::findOrFail($request->master_table_id);
        // dd($table->name);

        return Inertia::render('MasterDataColumn/Index', [
            'master_table_id' => $request->master_table_id,
            'master_table_name' => ucwords($table->name),
        ]);
    }

    public function master_table_data(Request $request): Response
    {
        return Inertia::render('MasterDataTable/Index', [
            'master_table_id' => $request->master_table_id,
        ]);
    }
}
