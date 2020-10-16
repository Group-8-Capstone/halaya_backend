<?php

namespace App\Http\Controllers;
use App\Http\Resources\OrderCollection;

use Illuminate\Http\Request;
use App\Models\Order;
use DB;

class DeliveryController extends Controller {
    public function fetchDelivery(){
        return new OrderCollection(Order::where('order_status', 'On order')
        ->orWhere('delivery_date',DB::raw('CURDATE()'))
        ->orderBy('distance', 'asc')->get());
    }
}