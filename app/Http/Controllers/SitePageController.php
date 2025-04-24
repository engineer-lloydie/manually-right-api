<?php

namespace App\Http\Controllers;

use App\Http\Requests\SitePageRequest;
use App\Models\SitePage;
use Exception;
use Illuminate\Http\Request;

class SitePageController extends Controller
{
    public function fetchSitePages(Request $request) {
        try {
            return SitePage::with('metaTags')->where('status', 'active')
                ->when($request->has('sortBy'), function($query) use ($request) {
                    $params = json_decode($request->query('sortBy'));

                    $query->orderBy($params->key, $params->order);
                }, function ($query) {
                    $query->orderBy('id', 'desc');
                })
                ->when(!empty($request->has('search')), function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->query('search') . '%');
                })
                ->paginate($request->query(('itemsPerPage')));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createSitePage(SitePageRequest $request) {
        try {
            SitePage::create([
                'name' => $request->input('pageName'),
                'url_slug' => $request->input('urlSlug'),
                'status' => $request->input('status')
            ]);

            return response()->json([
                'message' => 'A site page has been added successfully.'
            ], 201);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateSitePage(SitePageRequest $request, $sitePageId) {
        try {
            $sitePage = SitePage::findOrFail($sitePageId);

            $sitePage->update([
                'name' => $request->input('pageName'),
                'url_slug' => $request->input('urlSlug'),
                'status' => $request->input('status')
            ]);

            return response()->json([
                'message' => 'A site page has been updated successfully.'
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteSitePage($sitePageId) {
        try {
            SitePage::findOrFail($sitePageId)->delete();

            return response()->json([
                'message' => 'A site page has been deleted successfully.'
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
