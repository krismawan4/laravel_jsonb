<?php

use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\API\MasterTableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/master/table', MasterTableController::class);
Route::apiResource('/master/data/{master_table_name}/records', MasterDataController::class)
    ->parameters([
        'records' => 'id', // agar tidak pakai {{records}} di URL
    ]);
