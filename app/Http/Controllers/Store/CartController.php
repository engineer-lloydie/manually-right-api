<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function fetchCarts(Request $request) {
        try {
            $carts = Cart::leftJoin('manuals', 'carts.manual_id', 'manuals.id');

            if ($request->query('userId')) {
                $carts->where('user_id', $request->query('userId'));
            } else {
                $carts->where('guest_id', $request->query('guestId'));
            }

            $carts = $carts->select(
                    'carts.*',
                    'manuals.title'
                )
                ->orderBy('id', 'desc')
                ->get();
            
            return response()->json([
                'data' => $carts,
                'count' => $carts->count(),
                'total' => $carts->sum('price')
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function addToCart(Request $request) {
        Cart::create([
            'user_id' => $request->input('userId'),
            'guest_id' => $request->input('guestId'),
            'manual_id' => $request->input('manualId'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Cart has been added successfully.'
        ]);
    }

    public function deleteCart($cartId) {
        Cart::find($cartId)->delete();

        return response()->json([
            'message' => 'Cart has been deleted successfully.'
        ]);
    }
}
