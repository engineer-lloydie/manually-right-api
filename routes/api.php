<?php

use App\Http\Controllers\Admin\MainCategoryController;
use App\Http\Controllers\Admin\ManualController;
use App\Http\Controllers\Admin\ManualFileController;
use App\Http\Controllers\Admin\ManualThumbnailController;
use App\Http\Controllers\Admin\SubCategoryContoller;
use App\Http\Controllers\Auth\TokenAuthController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\ListDisplayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin Routes
Route::prefix('/admin')->group(function () {
    Route::prefix('/main-categories')->group(function () {
        Route::get('/', [MainCategoryController::class, 'getCategories']);
        Route::post('/', [MainCategoryController::class, 'addCategory']);
        Route::put('/{id}', [MainCategoryController::class, 'updateCategory']);
        Route::delete('/{id}', [MainCategoryController::class, 'deleteCategory']);
    });
    
    Route::prefix('/sub-categories')->group(function () {
        Route::get('/', [SubCategoryContoller::class, 'getCategories']);
        Route::post('/', [SubCategoryContoller::class, 'addCategory']);
        Route::put('/{id}', [SubCategoryContoller::class, 'updateCategory']);
        Route::delete('/{id}', [SubCategoryContoller::class, 'deleteCategory']);
    });
    
    Route::prefix('/manuals')->group(function () {
        Route::get('/', [ManualController::class, 'getManuals']);
        Route::post('/', [ManualController::class, 'addManual']);
    
        Route::prefix('/{id}')->group(function () {
            Route::put('/', [ManualController::class, 'updateManual']);
            Route::delete('/', [ManualController::class, 'deleteManual']);
    
            Route::prefix('/files')->group(function () {
                Route::get('/', [ManualFileController::class, 'getDocumentFiles']);
                Route::post('/', [ManualFileController::class, 'addDocumentFile']);
                Route::delete('/{documentFileId}', [ManualFileController::class, 'deleteDocumentFile']);
            });
    
            Route::prefix('/thumbnails')->group(function () {
                Route::get('/', [ManualThumbnailController::class, 'getThumbnails']);
                Route::post('/', [ManualThumbnailController::class, 'addThumbnail']);
                Route::delete('/{thumbnailId}', [ManualThumbnailController::class, 'deleteThumbnail']);
            });
        });
        
        Route::get('file-signed-url/{id}', [ManualController::class, 'getSignedUrl']);
    });
});

// Client Routes
Route::prefix('/store')->group(function () {
    Route::get('/main-category', [ListDisplayController::class, 'getMainCategory']);
    Route::prefix('/main-categories')->group(function () {
        Route::get('/', [ListDisplayController::class, 'getMainCategories']);
        Route::get('/sub-category', [ListDisplayController::class, 'getSubCategory']);
        Route::get('/{id}/sub-categories', [ListDisplayController::class, 'getSubCategories']);
        Route::get('/sub-categories/{id}/manuals', [ListDisplayController::class, 'getManuals']);
        Route::get('/sub-categories/manual-details', [ListDisplayController::class, 'getManualDetails']);
    });
});

Route::get('/carts', [CartController::class, 'fetchCarts']);
Route::post('/carts', [CartController::class, 'addToCart']);
Route::delete('/carts/{id}', [CartController::class, 'deleteCart']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [TokenAuthController::class, 'login']);
Route::post('/register', [TokenAuthController::class, 'register']);
Route::middleware(['auth:sanctum'])->post('/logout', [TokenAuthController::class, 'logout']);