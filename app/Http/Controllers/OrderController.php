<?php

namespace App\Http\Controllers;

use App\Models\OrderMaster;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrderLists(Request $request) {
        try {
            $orders = OrderMaster::query()->with(['carts', 'orderDetails']);

            if ($request->query('userId')) {
                $orders->where('user_id', $request->query('userId'));
            } else {
                $orders->where('guest_id', $request->query('guestId'));
            }

            return $orders->where('payment_status', 'paid')
                ->when($request->has('sortBy'), function($query) use ($request) {
                    $params = json_decode($request->query('sortBy'));

                    $query->orderBy($params->key, $params->order);
                }, function ($query) {
                    $query->orderBy('id', 'desc');
                })
                ->paginate($request->query(('itemsPerPage')));
        } catch (Exception $e) {
            throw $e;
        }
    }
}
