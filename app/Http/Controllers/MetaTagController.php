<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetaTagRequest;
use App\Models\Manual;
use App\Models\SitePage;
use App\Models\SubCategory;
use Exception;

class MetaTagController extends Controller
{
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
