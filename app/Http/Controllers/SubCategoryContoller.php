<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryContoller extends Controller
{
    public function getCategories(Request $request) {
        return DB::table('sub_categories')
            ->when($request->has('sortBy'), function($query) use ($request) {
                $params = json_decode($request->query('sortBy'));

                $query->orderBy($params->key, $params->order);
            }, function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($request->query(('itemsPerPage')));
    }

    public function addCategory(Request $request) {
        SubCategory::create([
            'main_category_id' => $request->input('mainCategoryId'),
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
        SubCategory::find($categoryId)
            ->update([
                'main_category_id' => $request->input('main_category_id'),
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
        SubCategory::find($categoryId)->delete();

        return response()->json([
            'message' => 'Category has been deleted successfully.'
        ]);
    }
}
