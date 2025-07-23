<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonHelper;
use App\Http\Controllers\Controller;
use App\Models\MasterTable;
use App\Models\MasterTableColumn;
use App\Models\MasterTableData;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function tableForm($master_table_id)
    {
        $data = MasterTableColumn::query()
            ->select('data')
            ->where('master_table_id', '=', $master_table_id)
            ->orderBy('id', 'asc')
            ->get()
            ->pluck('data') // ambil hanya kolom data
            // ->map(fn ($item) => $item['label']) // ambil label dari setiap item
            ->toArray();

        // dd($data);

        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'master_table_id' => 'required',
                'data' => 'required',
            ]);

            $master_table = MasterTable::findOrFail($validated['master_table_id']);
            $validated['master_table_name'] = $master_table->name;
            $data = MasterTableColumn::create($validated);

            // Add field to existing master_table_data records with null value
            JsonHelper::addJsonField(
                'master_table_data',
                'data',
                $validated['data']['field_name'], // field name
                null, // default value
                'master_table_id',
                $validated['master_table_id']
            );

            DB::commit();
            return $this->sendResponse($data, 'Data created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
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
        $data_column = MasterTableColumn::where('master_table_id', $master_table_id)->find($id);

        if (! $data_column) {
            return $this->sendResponse([], 'Data not found', false, 404);
        }

        $field_name = $data_column->data['field_name'];

        DB::beginTransaction();
        try {
            JsonHelper::removeJsonField('master_table_data', 'data', $field_name, 'master_table_id', $master_table_id);

            $data_column->delete();

            DB::commit();
            return $this->sendResponse([], 'Data deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse([], 'Failed to delete data: ' . $e->getMessage(), false, 500);
        }
    }

}
