<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainCategoryController extends Controller
{
    public function getCategories(Request $request) {
        return DB::table('main_categories')
            ->when($request->has('sortBy'), function($query) use ($request) {
                $params = json_decode($request->query('sortBy'));

                $query->orderBy($params->key, $params->order);
            }, function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($request->query(('itemsPerPage')));
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
