<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterTableTipe;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class MasterTableTipeController extends Controller
{
    use SendResponse;

    public function index()
    {
        $data = MasterTableTipe::orderBy('id', 'asc')->get();

        return $this->sendResponse($data->toArray(), 'Data retrieved successfully');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:master_table_tipe,name',
            ]);

            $data = MasterTableTipe::create($validated);

            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {
            return $this->sendExceptionResponse($e);
        }
    }

    public function show($id)
    {
        $data = MasterTableTipe::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        return $this->sendResponse($data->toArray(), 'Data retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $data = MasterTableTipe::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:master_table,name,'.$id,
        ]);

        $data->update($validated);

        return $this->sendResponse($data->toArray(), 'Data updated successfully');
    }

    public function destroy($id)
    {
        $data = MasterTableTipe::find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $data->delete();

        return $this->sendResponse([], 'Data deleted successfully');
    }
}
