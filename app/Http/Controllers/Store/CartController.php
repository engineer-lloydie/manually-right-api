<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        Cart::create([
            'user_id',
            'guest_id',
            'manual_id' => $request->input('manualId'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Cart has been added successfully.'
        ]);
    }
}
