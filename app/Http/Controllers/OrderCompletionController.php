<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderMail;
use App\Models\Cart;
use App\Models\GuestOrder;
use App\Models\OrderDetail;
use App\Models\OrderMaster;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrderCompletionController extends Controller
{
    public function completeOrder(CheckoutRequest $request) {
        try {
            $carts = Cart::with('manual')->whereIn('id', $request->input('cartIds'))->get();

            $orderMaster = OrderMaster::create([
                'user_id' => $request->input('userId'),
                'guest_id' => $request->input('guestId'),
                'order_number' => null,
                'transaction_id' => $request->input('transactionId'),
                'total_price' => $carts->sum('price'),
                'payment_method' => 1,
                'purchase_date' => Carbon::now(),
                'payment_status' => 'paid'
            ]);
    
            if ($orderMaster) {
                $orderMaster->updateOrderNo();
            }

            foreach ($carts as $key => $cart) {
                OrderDetail::create([
                    'cart_id' => $cart->id,
                    'order_master_id' => $orderMaster->id,
                    'item_number' => '00' . ($key + 1),
                    'subtotal' => $cart->price,
                    'download_count' => 5,
                    'status' => 'completed'
                ]);
            }

            $email_address = null;
            $first_name = null;

            if ($request->input('guestId')) {
                GuestOrder::create([
                    'guest_id' => $request->input('guestId'),
                    'order_master_id' => $orderMaster->id,
                    'email_address' => $request->input('emailAddress'),
                    'first_name' => $request->input('firstName')
                ]);

                $first_name = $request->input('firstName');
                $email_address = $request->input('emailAddress');
            } else {
                $user = User::find($request->input('userId'));
                $first_name = $user->first_name;
                $email_address = $user->email_address;
            }

            $carts->each(function ($cart) {
                $cart->update(['status' => 'sold']);
            });

            $items = $carts->map(function ($cart) {
                $filePath = 'documents/thumbnails/' . $cart->manual->thumbnails->first()->filename;
                $expiry = now()->addMinutes(15);
                $url = Storage::temporaryUrl($filePath, $expiry);

                return [
                    'thumbnail' => $url,
                    'manual_name' => $cart->manual->title,
                    'subtotal' => $cart->price,
                    'quantity' => $cart->quantity
                ];
            });

            $filePath = 'icons/partial-logo.png';
            $expiry = now()->addMinutes(15);
            $url = Storage::temporaryUrl($filePath, $expiry);

            Mail::to($email_address)->send(new OrderMail([
                'first_name' => $first_name,
                'email' => $email_address,
                'order_number' => $orderMaster->order_number,
                'purchase_date' => $orderMaster->purchase_date,
                'total_price' => $orderMaster->total_price,
                'items' => $items,
                'logo_url' => $url
            ]));

            return response()->json([
                'message' => 'The order has been completed.',
                'orderNumber' => $orderMaster->order_number
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
