<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Manual;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function countTotalUsers() {
        
    }
    
    public function countTodaysOrders() {
        
    }
    
    public function countTotalSales() {
        
    }
    
    public function countOrderHistory() {
        
    }
    
    public function countTopSellingProducts() {
        return Cart::where('status', 'sold')
            ->join('manuals', 'manuals.id', '=', 'carts.manual_id')
            ->select('manual_id', 'price')
            ->orderBy('price', 'desc')
            ->distinct('manual_id')
            ->limit(4)
            ->get()
            ->pluck('manual_id')
            ->toArray();
    }
}
