<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function fetchCarts(Request $request) {
        try {
            $carts = Cart::leftJoin('manuals', 'carts.manual_id', '=', 'manuals.id')
                ->leftJoin(DB::raw('(SELECT manual_id, filename FROM manual_thumbnails ORDER BY id ASC LIMIT 1) as thumbnails'), 'manuals.id', '=', 'thumbnails.manual_id');

            if ($request->query('userId')) {
                $carts->where('carts.user_id', $request->query('userId'));
            } else {
                $carts->where('carts.guest_id', $request->query('guestId'));
            }

            $carts = $carts->where('carts.status', 'pending')
                ->select(
                    'carts.*',
                    'manuals.title',
                    'thumbnails.filename'
                )
                ->orderBy('carts.id', 'desc')
                ->get()
                ->map(function ($cart) {
                    $filePath = 'documents/thumbnails/' . $cart->filename;
                    $expiry = now()->addMinutes(15);
                    $url = Storage::temporaryUrl($filePath, $expiry);
                    $cart->thumbnail = $url;

                    return $cart;
                });
            
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
        try {
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
        } catch (Exception $e) {
            throw $_GET;
        }
    }

    public function deleteCart($cartId) {
        Cart::find($cartId)->delete();

        return response()->json([
            'message' => 'Cart has been deleted successfully.'
        ]);
    }

    public function transferCart(Request $request) {
        try {
            Cart::whereIn('id', $request->input('cartIds'))
                ->update([
                    'user_id' => $request->input('userId'),
                    'guest_id' => null
                ]);

            return response()->json([
                'message' => 'Carts have been transferred successfully.'
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
