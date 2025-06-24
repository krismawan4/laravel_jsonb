<?php

use App\Http\Controllers\Api\MasterTableColumnController;
use App\Http\Controllers\API\MasterTableController;
use App\Http\Controllers\Api\MasterTableDataController;
use App\Http\Controllers\Api\MasterTableTipeController;
use App\Http\Controllers\Api\MasterTableValidationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/master-table', MasterTableController::class);
Route::apiResource('/master-table-column/{master_table_id}/records', MasterTableColumnController::class)
    ->parameters(['records' => 'id'])
    ->names('column.records'); // <--- tambah prefix nama route
Route::get('/master-table-column-header/{master_table_id}/records', [MasterTableColumnController::class, 'tableHeader']);

Route::apiResource('/master-table-data/{master_table_id}/records', MasterTableDataController::class)
    ->parameters(['records' => 'id'])
    ->names('data.records'); // <--- beda nama dengan di atas
Route::apiResource('/master-table-tipe', MasterTableTipeController::class);
Route::apiResource('/master-table-validation', MasterTableValidationController::class);
