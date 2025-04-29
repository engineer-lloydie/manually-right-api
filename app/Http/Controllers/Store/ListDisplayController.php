<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\Manual;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListDisplayController extends Controller
{
    public function getMainCategories() {
        return response()->json([
            'data' =>  MainCategory::all()
        ]);
    }

    public function getMainCategory(Request $request) {
        return response()->json([
            'data' =>  MainCategory::where('url_slug', $request->query('urlSlug'))->first()
        ]);
    }

    public function getSubCategories($mainCategoryId) {
        return response()->json([
            'data' =>  SubCategory::where('main_category_id', $mainCategoryId)->get()
        ]);
    }

    public function getSubCategory(Request $request) {
        return response()->json(
            SubCategory::where('url_slug', $request->query('urlSlug'))->first()
        );
    }

    public function getManuals(Request $request, $subCategoryId) {
        try {
            $manuals = Manual::with('thumbnails')
                ->where('sub_category_id', $subCategoryId)
                ->whereHas('thumbnails')
                ->paginate($request->query('itemsPerPage'));

                $manuals->getCollection()
                    ->transform(function ($manual) {
                        $manual->thumbnail = null;

                        if ($manual->thumbnails->isNotEmpty()) {
                            $manualThumbnail = $manual->thumbnails->first();

                            $filePath = 'documents/thumbnails/' . $manualThumbnail->filename;
                            $expiry = now()->addMinutes(15);
                            $url = Storage::temporaryUrl($filePath, $expiry);

                            $manual->thumbnail = $url;
                        }

                        unset($manual->thumbnails);

                        return $manual;
                    });

            return response()->json(
                $manuals
            ); 
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getManualDetails(Request $request) {
        $manual = Manual::with(['files', 'thumbnails', 'metaTags'])
            ->where('url_slug', $request->query('urlSlug'))
            ->first();

            $manual->thumbnails = $manual->thumbnails->transform(function ($thumbnail) {
                $filePath = 'documents/thumbnails/' . $thumbnail->filename;
                $expiry = now()->addMinutes(15);
                $url = Storage::temporaryUrl($filePath, $expiry);
                $thumbnail->file_url = $url;

                return $thumbnail;
            });

        return response()->json([
            'data' =>  $manual
        ]); 
    }
}
