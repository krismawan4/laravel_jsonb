<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterTable;
use App\Models\MasterTableColumn;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterTableColumnController extends Controller
{
    use SendResponse;

    public function index($master_table_id)
    {
        $data = MasterTableColumn::query()
            ->where('master_table_id', '=', $master_table_id)
            ->orderBy('id', 'asc')->get();

        return $this->sendResponse($data->toArray(), 'Data retrieved successfully');
    }

    public function tableHeader($master_table_id)
    {
        $data = MasterTableColumn::query()
            ->select('data')
            ->where('master_table_id', '=', $master_table_id)
            ->orderBy('id', 'asc')
            ->get()
            ->pluck('data') // ambil hanya kolom data
            ->map(fn ($item) => $item['label']) // ambil label dari setiap item
            ->toArray();

        // dd($data);

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'master_table_id' => 'required',
                'data' => 'required',
            ]);

            $master_table = MasterTable::findOrFail($validated['master_table_id']);
            $validated['master_table_name'] = $master_table->name;
            $data = MasterTableColumn::create($validated);

            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {

            return $this->sendExceptionResponse($e);
        }
    }

    public function update(Request $request, $master_table_id, $id)
    {
        try {
            $validated = $request->validate([
                'master_table_id' => 'required',
                'data' => 'required',
            ]);

            $data = MasterTableColumn::where('master_table_id', $master_table_id)->find($id);
            if (! $data) {
                return $this->sendResponse([], 'Data not found', false, 404);
            }

            $master_table = MasterTable::find($data->master_table_id);
            if (! $master_table) {
                return $this->sendResponse([], 'Master table not found', false, 404);
            }

            $validated['master_table_name'] = $master_table->name;

            Log::info('Before update:', $data->toArray());

            $data->data = $validated['data'];
            $data->master_table_name = $validated['master_table_name'];
            $data->master_table_id = $validated['master_table_id'];
            $data->update();

            Log::info('After update:', $data->fresh()->toArray());

            return $this->sendResponse($data->toArray(), 'Data updated successfully');
        } catch (\Throwable $e) {

            return $this->sendExceptionResponse($e);
        }
    }

    public function destroy($master_table_id, $id)
    {
        $data = MasterTableColumn::where('master_table_id', $master_table_id)->find($id);
        if (! $data) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $data->delete();

        return $this->sendResponse([], 'Data deleted successfully');
    }
}
