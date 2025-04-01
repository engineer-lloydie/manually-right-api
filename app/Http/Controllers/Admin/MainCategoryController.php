<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainCategoryController extends Controller
{
    public function getCategories(Request $request) {
        $initialQuery = DB::table('main_categories');

        if (!$request->has('page')) {
            return $initialQuery->orderBy('name')->get();
        } else {
            return $initialQuery->when($request->has('sortBy'), function($query) use ($request) {
                    $params = json_decode($request->query('sortBy'));

                    $query->orderBy($params->key, $params->order);
                }, function ($query) {
                    $query->orderBy('name', 'asc');
                })
                ->when(!empty($request->has('search')), function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->query('search') . '%');
                })
                ->paginate($request->query(('itemsPerPage')));
        }
    }

    public function addCategory(Request $request) {
        MainCategory::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'url_slug' => $request->input('urlSlug'),
            'status' => $request->input('status')
        ]);

        return response()->json([
            'message' => 'Category has been added successfully.'
        ]);
    }

    public function updateCategory(Request $request, $categoryId) {
        MainCategory::find($categoryId)
            ->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'url_slug' => $request->input('urlSlug'),
                'status' => $request->input('status')
            ]);

        return response()->json([
            'message' => 'Category has been updated successfully.'
        ]);
    }

    public function deleteCategory($categoryId) {
        MainCategory::find($categoryId)->delete();

        return response()->json([
            'message' => 'Category has been deleted successfully.'
        ]);
    }
}
