<?php

namespace App\Http\Controllers;

use App\Models\GuestOrder;
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
                if ($request->query('orderMasterIds')) {
                    $orders->whereIn('id', json_decode($request->query('orderMasterIds')));
                } else {
                    $orders->where('guest_id', $request->query('guestId'));
                }
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

    public function getAdminOrderLists(Request $request) {
        try {
            return OrderMaster::with(['carts', 'orderDetails'])
                ->where('payment_status', 'paid')
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

    public function checkOrder(Request $request) {
        try {
            $orderMasterIds = [];

            if ($request->input('type') == 'email-address') {
                $orderMasterIds = GuestOrder::select('order_master_id')
                    ->where('email_address', $request->query('searchQuery'))
                    ->pluck('order_master_id')
                    ->toArray();
            } else if ($request->input('type') == 'order-number') {
                $orderMaster = OrderMaster::where('order_number', $request->query('searchQuery'))->first();

                if ($orderMaster) {
                    $orderMasterIds = [$orderMaster->id];
                }
            } else {
                $orderMaster = OrderMaster::where('transaction_id', $request->query('searchQuery'))->first();
                
                if ($orderMaster) {
                    $orderMasterIds = [$orderMaster->id];
                }
            }

            return response()->json([
                'orderMasterIds' => $orderMasterIds
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
