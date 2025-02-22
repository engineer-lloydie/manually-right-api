<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Manual;
use App\Models\ManualFile;
use App\Models\ManualThumbnail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManualController extends Controller
{
    public function getManuals(Request $request) {
        $initialQuery = DB::table('manuals')
            ->join('sub_categories', 'sub_category_id', '=', 'sub_categories.id')
            ->select('manuals.*', 'sub_categories.name as category');

            if (!$request->has('page')) {
                return $initialQuery->get();
            } else {
                return $initialQuery->when($request->has('sortBy'), function($query) use ($request) {
                        $params = json_decode($request->query('sortBy'));
        
                        $query->orderBy($params->key, $params->order);
                    }, function ($query) {
                        $query->orderBy('id', 'desc');
                    })
                    ->paginate($request->query(('itemsPerPage')));
            }
    }

    public function addManual(Request $request) {
        Manual::create([
            'sub_category_id' => $request->input('categoryId'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'url_slug' => $request->input('urlSlug'),
            'status' => $request->input('status')
        ]);

        return response()->json([
            'message' => 'Manual has been added successfully.'
        ]);
    }

    public function updateManual(Request $request, $manualId) {
        Manual::find($manualId)
            ->update([
                'sub_category_id' => $request->input('categoryId'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'url_slug' => $request->input('urlSlug'),
                'status' => $request->input('status')
            ]);

        return response()->json([
            'message' => 'Manual has been updated successfully.'
        ]);
    }

    public function deleteManual($manualId) {
        Manual::find($manualId)->delete();

        return response()->json([
            'message' => 'Manual has been deleted successfully.'
        ]);
    }

    public function getSignedUrl(Request $request, $fileId) {
        $model = null;

        if ($request->query('path') == 'files') {
            $model = new ManualFile();
        } else {
            $model = new ManualThumbnail();
        }

        $filename = $model->findOrFail($fileId)->filename;

        $filePath = 'documents/' . $request->input('path') . '/' . $filename;
    
        $expiry = now()->addMinutes(15);
    
        $url = Storage::temporaryUrl($filePath, $expiry);
    
        return response()->json(['url' => $url]);
    }

    public function getLatestProducts() {
        $manuals = Manual::leftJoin(DB::raw('(SELECT manual_id, filename FROM manual_thumbnails ORDER BY id ASC LIMIT 1) as thumbnails'), 'manuals.id', '=', 'thumbnails.manual_id')
            ->leftJoin('sub_categories', 'manuals.id', '=', 'sub_categories.id')
            ->leftJoin('main_categories', 'sub_categories.main_category_id', '=', 'main_categories.id')
            ->select('manuals.*', 'thumbnails.filename', 'sub_categories.url_slug as sub_url_slug', 'main_categories.url_slug as main_url_slug')
            ->limit(4)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($manual) {
                $filePath = 'documents/thumbnails/' . $manual->filename;
                $expiry = now()->addMinutes(15);
                $url = Storage::temporaryUrl($filePath, $expiry);

                $manual->thumbnails->first()->url = $url;

                return $manual;
            });

        return response()->json([
            'data' => $manuals
        ]);
    }

    public function getBestSetting() {
        $manuals = [];

        $manualIds = Cart::where('status', 'sold')
            ->select('manual_id')
            ->orderBy('price', 'desc')
            ->distinct('manual_id')
            ->limit(4)
            ->get()
            ->pluck('manual_id')
            ->toArray();

        if (count($manualIds)) {
            $manuals = Manual::leftJoin(DB::raw('(SELECT manual_id, filename FROM manual_thumbnails ORDER BY id ASC LIMIT 1) as thumbnails'), 'manuals.id', '=', 'thumbnails.manual_id')
                ->leftJoin('sub_categories', 'manuals.id', '=', 'sub_categories.id')
                ->leftJoin('main_categories', 'sub_categories.main_category_id', '=', 'main_categories.id')
                ->whereIn('manuals.id', $manualIds)
                ->select('manuals.*', 'thumbnails.filename', 'sub_categories.url_slug as sub_url_slug', 'main_categories.url_slug as main_url_slug')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($manual) {
                    $filePath = 'documents/thumbnails/' . $manual->filename;
                    $expiry = now()->addMinutes(15);
                    $url = Storage::temporaryUrl($filePath, $expiry);

                    $manual->thumbnails->first()->url = $url;

                    return $manual;
                });
        }
        return response()->json([
            'data' => $manuals
        ]);
    }
}
