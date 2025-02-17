<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manual;
use App\Models\ManualThumbnail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManualThumbnailController extends Controller
{
    public function getThumbnails(Request $request) {
        return DB::table('manual_thumbnails')->when($request->has('sortBy'), function($query) use ($request) {
                $params = json_decode($request->query('sortBy'));

                $query->orderBy($params->key, $params->order);
            }, function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($request->query(('itemsPerPage')));
    }

    public function addThumbnail(Request $request, $manualId)
    {
        try {
            $filename = null;

            if ($request->hasFile('thumbnail')) {
                $manual = Manual::find($manualId);
                
                $file = $request->file('thumbnail');
                $filename = $manual->url_slug . '-' . date('m-d-Y-His') . '.' . $file->getClientOriginalExtension();
                $storedFile = $file->storeAs('documents/thumbnails', $filename, 'backblaze');

                if (!$storedFile) {
                    throw new Exception('Unable to upload file');
                }
            }
    
            ManualThumbnail::create([
                'manual_id' => $manualId,
                'filename' => $filename,
                'status' => $request->input('status')
            ]);
    
            return response()->json([
                'message' => 'Manual thumbnail file has been added successfully.'
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    public function deleteThumbnail($manualId, $thumbnailFileId) {
        try {
            $manualThumbnail = ManualThumbnail::findOrFail($thumbnailFileId);

            if ($manualThumbnail) {
                Storage::disk('backblaze')->delete('documents/thumbnails/' . $manualThumbnail->filename);
                
                $manualThumbnail->delete();
            }

            return response()->json([
                'message' => 'Manual thumbnail file has been deleted successfully.'
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
