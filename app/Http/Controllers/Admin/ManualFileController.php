<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manual;
use App\Models\ManualFile;
use App\Models\OrderDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ManualFileController extends Controller
{
    public function getDocumentFiles(Request $request, $manualId) {
        return DB::table('manual_files')
            ->where('manual_id', $manualId)
            ->when($request->has('sortBy'), function($query) use ($request) {
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

    public function downloadZip(Request $request) {
        try {
            $data = $request->validate([
                'manualId' => 'required',
                'orderMasterId' => 'required'
            ]);
            
            $files = $this->getFilesFromIds($data['manualId']);

            if ($files->empty()) {
                return response()->json(['error' => 'No files found. Please contact the administrator.'], 404);
            }
    
            $zipFileName = tempnam(sys_get_temp_dir(), 'zip');
            $zip = new ZipArchive();
    
            if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
                return response()->json(['error' => 'Cannot create zip file'], 500);
            }
    
            foreach ($files as $file) {
                $fileContent = file_get_contents($file['url']);
    
                if ($fileContent === false) {
                    continue;
                }
    
                $zip->addFromString($file['name'], $fileContent);
            }
    
            $zip->close();

            OrderDetail::where('order_master_id', $request->input('orderMasterId'))
                ->decrement('download_count');
    
            return response()->download($zipFileName, 'files.zip', [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="files.zip"',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function getFilesFromIds($manualId) {
        return ManualFile::where('manual_id', $manualId)
            ->select('filename')
            ->get()
            ->map(function ($manual) {
                $filePath = 'documents/files/' . $manual->filename;
                $expiry = now()->addMinutes(15);
                $url = Storage::temporaryUrl($filePath, $expiry);

                return [
                    'name' => $manual->filename,
                    'url' => $url
                ];
            });
    }
}
