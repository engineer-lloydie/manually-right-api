<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function fetchAdminBanners(Request $request) {
        try {
            return DB::table('banners')->when($request->has('sortBy'), function($query) use ($request) {
                    $params = json_decode($request->query('sortBy'));

                    $query->orderBy($params->key, $params->order);
                }, function ($query) {
                    $query->orderBy('id', 'desc');
                })
                ->paginate($request->query(('itemsPerPage')));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function fetchClientBanner() {
        try {
            $banner = DB::table('banners')->where('status', 'active')->latest()->first();

            if ($banner) {
                $filePath = 'banners/' . $banner->filename;
                $expiry = now()->addMinutes(15);
                $url = Storage::temporaryUrl($filePath, $expiry);

                $banner->url = $url;

                return response()->json([
                    'banners' => [$banner]
                ]);
            }

            return response()->json([
                'banners' => []
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteBanner($bannerId) {
        try {
            $banner = Banner::findOrFail($bannerId);

            if ($banner) {
                Storage::disk('backblaze')->delete('banners/' . $banner->filename);
                
                $banner->delete();
            }

            return response()->json([
                'message' => 'Banner file has been deleted successfully.'
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function previewBanner($bannerId) {
        try {
            $banner = Banner::findOrFail($bannerId);
            $url = null;

            if ($banner) {
                $filePath = 'banners/' . $banner->filename;
                $expiry = now()->addMinutes(15);
                $url = Storage::temporaryUrl($filePath, $expiry);
            }

            return response()->json([
                'bannerUrl' => $url
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createBanner(Request $request) {
        try {
            $filename = null;

            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $filename = 'banner-' . date('m-d-Y-His') . '.' . $file->getClientOriginalExtension();
                $storedFile = $file->storeAs('banners', $filename, 'backblaze');

                if (!$storedFile) {
                    throw new Exception('Unable to upload file');
                }
            }
    
            Banner::create([
                'title' => $request->input('title'),
                'filename' => $filename,
                'status' => $request->input('status')
            ]);
    
            return response()->json([
                'message' => 'Banner image has been added successfully.'
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
