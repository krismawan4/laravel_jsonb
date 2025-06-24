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

    public function masterTableColumn(Request $request): Response
    {
        $table = MasterTable::findOrFail($request->master_table_id);
        // dd($table->name);

        return Inertia::render('MasterTableColumn/Index', [
            'master_table_id' => (int) $request->master_table_id,
            'master_table_name' => ucwords($table->name),
        ]);
    }

    public function masterTableData(Request $request): Response
    {
        $table = MasterTable::findOrFail($request->master_table_id);

        return Inertia::render('MasterTableData/Index', [
            'master_table_id' => (int) $request->master_table_id,
            'master_table_name' => ucwords($table->name),
        ]);
    }
}
