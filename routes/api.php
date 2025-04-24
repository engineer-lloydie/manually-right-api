<?php

use App\Http\Controllers\Admin\MainCategoryController;
use App\Http\Controllers\Admin\ManualController;
use App\Http\Controllers\Admin\ManualFileController;
use App\Http\Controllers\Admin\ManualThumbnailController;
use App\Http\Controllers\Admin\SubCategoryContoller;
use App\Http\Controllers\Auth\TokenAuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MetaTagController;
use App\Http\Controllers\OrderCompletionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\SitemapUrlController;
use App\Http\Controllers\SitePageController;
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

    Route::prefix('/site-pages')->group(function () {
        Route::get('/', [SitePageController::class, 'fetchSitePages']);
        Route::post('/', [SitePageController::class, 'createSitePage']);
        Route::put('/{sitePageId}', [SitePageController::class, 'updateSitePage']);
        Route::delete('/{sitePageId}', [SitePageController::class, 'deleteSitePage']);
    });

    Route::prefix('/meta-tags')->group(function () {
        Route::post('/{metaableId}', [MetaTagController::class, 'createMetaTag']);
        Route::put('/{metaableId}', [MetaTagController::class, 'updateMetaTag']);
    });
})->middleware(['auth:sanctum']);

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
Route::post('/carts/transfer', [CartController::class, 'transferCart']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [TokenAuthController::class, 'login']);
Route::post('/register', [TokenAuthController::class, 'register']);
Route::middleware(['auth:sanctum'])->post('/logout', [TokenAuthController::class, 'logout']);

Route::post('/create-order', [PayPalController::class, 'createOrder']);
Route::post('/capture-order', [PayPalController::class, 'captureOrder']);

Route::post('/complete-order', [OrderCompletionController::class, 'completeOrder']);
Route::get('/orders/lists', [OrderController::class, 'getOrderLists']);
Route::get('/admin/orders/lists', [OrderController::class, 'getAdminOrderLists']);

Route::post('/download-files', [ManualFileController::class, 'downloadZip']);

Route::get('/check-order', [OrderController::class, 'checkOrder']);

Route::prefix('/items')->group(function () {
    Route::get('/best-selling', [ManualController::class, 'getBestSetting']);
    Route::get('/latest', [ManualController::class, 'getLatestProducts']);
    Route::get('/search', [ManualController::class, 'searchManuals']);
});

Route::prefix('/banners')->group(function () {
    Route::post('/', [BannerController::class, 'createBanner']);
    Route::get('/', [BannerController::class, 'fetchAdminBanners']);
    Route::get('/client', [BannerController::class, 'fetchClientBanner']);
    Route::delete('/{bannerId}', [BannerController::class, 'deleteBanner']);
    Route::get('/{bannerId}/preview', [BannerController::class, 'previewBanner']);
});

Route::get('/sitemap/pages/urls', [SitemapUrlController::class, 'getURLs']);

Route::post('/inquiries/message', [InquiryController::class, 'sendMessage']);