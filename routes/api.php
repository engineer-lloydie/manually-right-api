<?php

use App\Http\Controllers\MainCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/main-categories')->group(function () {
    Route::get('/', [MainCategoryController::class, 'getCategories']);
    Route::post('/', [MainCategoryController::class, 'addCategory']);
    Route::put('/{id}', [MainCategoryController::class, 'updateCategory']);
    Route::delete('/{id}', [MainCategoryController::class, 'deleteCategory']);
});
