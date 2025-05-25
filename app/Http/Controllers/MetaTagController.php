<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetaTagRequest;
use App\Models\Manual;
use App\Models\MetaTag;
use App\Models\SitePage;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;

class MetaTagController extends Controller
{
        public function fetchMetaTags(Request $request) {
        try {
            return MetaTag::where('status', 'active')
                ->when($request->has('sortBy'), function($query) use ($request) {
                    $params = json_decode($request->query('sortBy'));

                    $query->orderBy($params->key, $params->order);
                }, function ($query) {
                    $query->orderBy('id', 'desc');
                })
                ->when(!empty($request->has('search')), function($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->query('search') . '%');
                })
                ->paginate($request->query(('itemsPerPage')));
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function createMetaTag(MetaTagRequest $request, $metaableId)
    {
        try {
            switch ($request->input('model')) {
                case 'site_page':
                    SitePage::findOrFail($metaableId)->metaTags()->create([
                        'title' => $request->input('metaTitle'),
                        'description' => $request->input('metaDescription'),
                        'metaable_type' => $request->input('model'),
                        'metaable_id' => $metaableId
                    ]);
                    break;
                case 'manual':
                    Manual::findOrFail($metaableId)->metaTags()->create([
                        'title' => $request->input('metaTitle'),
                        'description' => $request->input('metaDescription'),
                        'metaable_type' => $request->input('model'),
                        'metaable_id' => $metaableId
                    ]);
                    break;
                case 'category':
                    SubCategory::findOrFail($metaableId)->metaTags()->create([
                        'title' => $request->input('metaTitle'),
                        'description' => $request->input('metaDescription'),
                        'metaable_type' => $request->input('model'),
                        'metaable_id' => $metaableId
                    ]);
                    break;
                default:
                    throw new Exception('Invalid model type');
            }

            return response()->json([
                'message' => 'A meta tag has been added successfully.'
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function updateMetaTag(MetaTagRequest $request, $metaableId)
    {
        try {
            switch ($request->input('model')) {
                case 'site_page':
                    SitePage::findOrFail($metaableId)->metaTags()->update([
                        'title' => $request->input('metaTitle'),
                        'description' => $request->input('metaDescription'),
                    ]);
                    break;
                case 'manual':
                    Manual::findOrFail($metaableId)->metaTags()->update([
                        'title' => $request->input('metaTitle'),
                        'description' => $request->input('metaDescription'),
                    ]);
                    break;
                case 'category':
                    SubCategory::findOrFail($metaableId)->metaTags()->update([
                        'title' => $request->input('metaTitle'),
                        'description' => $request->input('metaDescription'),
                    ]);
                    break;
                default:
                    throw new Exception('Invalid model type');
            }

            return response()->json([
                'message' => 'A meta tag has been updated successfully.'
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
