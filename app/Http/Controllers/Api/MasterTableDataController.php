<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterTableData;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class MasterTableDataController extends Controller
{
    use SendResponse;

    public function index($master_table_name)
    {
        $data = MasterTableData::where('master_table_name', $master_table_name)
            ->get()
            ->map(function ($item) {
                $newData = $item->data;
                $newData['id'] = $item->id;
                $newData['master_table_id'] = $item->master_table_id;
                $newData['master_table_name'] = $item->master_table_name;
                $newData['parent_id'] = $item->parent_id;
                $newData['parent_table_name'] = $item->parent_table_name;
                // dd($newData);

                return $newData; // ambil isi kolom data-nya saja
            });

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function store($master_table_name, Request $request)
    {
        try {
            $validated = $request->validate([
                'master_table_id' => 'required|exists:master_table,id',
                'master_table_name' => 'required|exists:master_table,name',
                'parent_id' => 'nullable|exists:master_data,id',
                'parent_table_name' => 'nullable|exists:master_table,name',
                'data' => 'required|array',
            ]);

            $data = MasterTableData::create($validated);

            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {
            return $this->sendExceptionResponse($e);
        }
    }

    public function show($master_table_name, $id)
    {
        $data = MasterTableData::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function update($master_table_name, Request $request, $id)
    {
        $data = MasterTableData::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $validated = $request->validate([
            'master_table_id' => 'required|exists:master_table,id',
            'master_table_name' => 'required|exists:master_table,name',
            'parent_id' => 'nullable|exists:master_data,id',
            'parent_table_name' => 'nullable|exists:master_table,name',
            'data' => 'required|array',
        ]);

        $data->update($validated);

        return $this->sendResponse($data, 'Data updated successfully');
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
