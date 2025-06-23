<?php

use App\Http\Controllers\Api\MasterTableColumnController;
use App\Http\Controllers\API\MasterTableController;
use App\Http\Controllers\Api\MasterTableDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/master-table', MasterTableController::class);
Route::apiResource('/master-table-column/{master_table_id}/records', MasterTableColumnController::class)
    ->parameters(['records' => 'id'])
    ->names('column.records'); // <--- tambah prefix nama route

Route::apiResource('/master-table-data/{master_table_id}/records', MasterTableDataController::class)
    ->parameters(['records' => 'id'])
    ->names('data.records'); // <--- beda nama dengan di atas
