<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterTable;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class MasterTableController extends Controller
{
    use SendResponse;

    public function index()
    {
        $data = MasterTable::orderBy('id', 'asc')->get();

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:master_table,name',
                'data' => 'required|array',
            ]);

            $data = MasterTable::create($validated);

            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {
            return $this->sendExceptionResponse($e);
        }
    }

    public function show($id)
    {
        $data = MasterTable::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $data = MasterTable::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:master_table,name,'.$id,
            'data' => 'sometimes|required|array',
        ]);

        $data->update($validated);

        return $this->sendResponse($data, 'Data updated successfully');
    }

    public function destroy($id)
    {
        $data = MasterTable::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $data->delete();

        return $this->sendResponse([], 'Data deleted successfully');
    }
}
