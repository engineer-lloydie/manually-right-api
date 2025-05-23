<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryContoller extends Controller
{
    public function getCategories(Request $request) {
        $initialQuery = SubCategory::with('metaTags')
            ->join('main_categories', 'main_category_id', '=', 'main_categories.id')
            ->select('sub_categories.*', 'main_categories.name as main_category');

            if (!$request->has('page')) {
                return $initialQuery->orderBy('sub_categories.name')->get();
            } else {
                return $initialQuery->when($request->has('sortBy'), function($query) use ($request) {
                        $params = json_decode($request->query('sortBy'));
        
                        $query->orderBy($params->key, $params->order);
                    }, function ($query) {
                        $query->orderBy('sub_categories.id', 'desc');
                    })
                    ->when(!empty($request->has('search')), function($query) use ($request) {
                        $query->where('sub_categories.name', 'like', '%' . $request->query('search') . '%');
                    })
                    ->paginate($request->query(('itemsPerPage')));
            }
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
                'main_category_id' => $request->input('mainCategoryId'),
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
