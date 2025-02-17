<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manual;
use App\Models\ManualFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManualFileController extends Controller
{
    public function getDocumentFiles(Request $request) {
        return DB::table('manual_files')->when($request->has('sortBy'), function($query) use ($request) {
                $params = json_decode($request->query('sortBy'));

                $query->orderBy($params->key, $params->order);
            }, function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($request->query(('itemsPerPage')));
    }

    public function addDocumentFile(Request $request, $manualId)
    {
        try {
            $filename = null;

            if ($request->hasFile('document')) {
                $manual = Manual::find($manualId);
                
                $file = $request->file('document');
                $filename = $manual->url_slug . '-' . date('m-d-Y-His') . '.' . $file->getClientOriginalExtension();
                $storedFile = $file->storeAs('documents/files', $filename, 'backblaze');

                if (!$storedFile) {
                    throw new Exception('Unable to upload file');
                }
            }
    
            ManualFile::create([
                'manual_id' => $manualId,
                'title' => $request->input('title'),
                'filename' => $filename,
                'status' => $request->input('status')
            ]);
    
            return response()->json([
                'message' => 'Manual document file has been added successfully.'
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    public function deleteDocumentFile($manualId, $documentFileId) {
        try {
            $manualFile = ManualFile::findOrFail($documentFileId);

            if ($manualFile) {
                Storage::disk('backblaze')->delete('documents/files/' . $manualFile->filename);
                
                $manualFile->delete();
            }

            return response()->json([
                'message' => 'Manual document file has been deleted successfully.'
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
