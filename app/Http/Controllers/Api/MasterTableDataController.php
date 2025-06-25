<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterTable;
use App\Models\MasterTableData;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class MasterTableDataController extends Controller
{
    use SendResponse;

    public function index($master_table_id)
    {
        $data = MasterTableData::where('master_table_id', $master_table_id)
            ->orderBy('id','desc')
            ->get()
            ->map(function ($item) {
                $newData = $item->data;
                $newData['id'] = $item->id;
                return $newData; // ambil isi kolom data-nya saja
            });

        return $this->sendResponse($data->toArray(), 'Data retrieved successfully');
    }

    public function store($master_table_name, Request $request)
    {
        try {
            $validated = $request->validate([
                'master_table_id' => 'required|exists:master_table,id',
                'data' => 'required|array',
            ]);

            $master_table = MasterTable::findOrFail($validated['master_table_id']);
            $validated['master_table_name'] = $master_table->name;

            $data = MasterTableData::create($validated);

            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {
            return $this->sendExceptionResponse($e);
        }
    }

    public function show($master_table_name, $id)
    {
        $data = MasterTableData::select('data')->find($id);

        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        return $this->sendResponse($data['data'], 'Data retrieved successfully');
    }

    public function update($master_table_name, Request $request, $id)
    {
        $data = MasterTableData::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $validated = $request->validate([
            'master_table_id' => 'required|exists:master_table,id',
            'data' => 'required|array',
        ]);

        $master_table = MasterTable::findOrFail($validated['master_table_id']);
        $validated['master_table_name'] = $master_table->name;

        $data->update($validated);

        return $this->sendResponse($data->toArray(), 'Data updated successfully');
    }

    public function destroy($master_table_name, $id)
    {
        $data = MasterTableData::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $data->delete();

        return $this->sendResponse([], 'Data deleted successfully');
    }
}
