<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SitemapUrlController extends Controller
{
    public function getURLs() {
        $url = collect();

        $baseUrls = DB::table('manuals')
            ->leftJoin('sub_categories', 'manuals.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('main_categories', 'sub_categories.main_category_id', '=', 'main_categories.id')
            ->select(
                'manuals.url_slug as manual_url',
                'sub_categories.url_slug as sub_category_url',
                'main_categories.url_slug as main_category_url'
            )
            ->get();

        $urls = $baseUrls->map(function ($item) {
                return [
                    'url' => "/manuals/categories/{$item->main_category_url}/{$item->sub_category_url}/{$item->manual_url}",
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'daily',
                    'priority' => '0.9'
                ];
            });

        $subCategoryUrls = $baseUrls->unique('sub_category_url')
            ->map(function ($item) {
                return [
                    'url' => "/manuals/categories/{$item->main_category_url}/{$item->sub_category_url}",
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'daily',
                    'priority' => '0.8'
                ];
            });

        $mainCategoryUrls = $baseUrls->unique('main_category_url')
            ->map(function ($item) {
                return [
                    'url' => "/manuals/categories/{$item->main_category_url}",
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'daily',
                    'priority' => '0.8'
                ];
            });

        return $urls->merge($subCategoryUrls)->merge($mainCategoryUrls);
    }
}
