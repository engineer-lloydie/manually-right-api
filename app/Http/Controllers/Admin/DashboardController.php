<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Manual;
use App\Models\OrderMaster;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function calculateTodaysSales() {
        try {
            $todaysSales = Cart::whereDate('created_at', Carbon::today())->sum('price');
            
            return response()->json([
                'status' => 'success',
                'data' => $todaysSales
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function countAppSummary() {
        try {
            $totalUsers = User::where('role', 'user')->count();
            $totalOrders = OrderMaster::where('payment_status', 'paid')->count();
            $totalSales = Cart::where('status', 'sold')->sum('price');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_users' => $totalUsers,
                    'total_orders' => $totalOrders,
                    'total_sales' => round($totalSales, 2)
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function countOrderHistory() {
        try {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'today' => OrderMaster::whereDate('created_at', Carbon::today())->count(),
                    'this_week' => OrderMaster::whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])->count(),

                    'this_month' => OrderMaster::whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ])->count(),

                    'last_month' => OrderMaster::whereBetween('created_at', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ])->count()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getTopSellingProducts() {
        try {
            $data = Manual::select('manuals.id as manual_id', 'manuals.title as manual_title')
                ->join('carts', function($join) {
                    $join->on('manuals.id', '=', 'carts.manual_id')
                        ->where('carts.status', 'sold');
                })
                ->selectRaw('SUM(carts.price) as total_price, COUNT(carts.id) as total_orders')
                ->groupBy('manuals.id', 'manuals.title')
                ->orderByDesc('total_price')
                ->limit(4)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
