<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterTableColumn;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class MasterTableColumnController extends Controller
{
    use SendResponse;

    public function index($master_column_name)
    {
        $data = MasterTableColumn::query()
            ->where('master_table_name', '=', $master_column_name)
            ->orderBy('id', 'asc')->get();

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'master_table_id' => 'required',
                'master_table_name' => 'required',
                'data' => 'required',
            ]);

            dd($validated);

            $data = MasterTableColumn::create($validated);

            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {
            dd('kk');

            return $this->sendExceptionResponse($e);
        }
    }
}
