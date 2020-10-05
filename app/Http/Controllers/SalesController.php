<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class SalesController extends Controller
{
    public function index(Request $request){
        $Date = date("Y-m-d");
        $Delivered = Order::select(\DB::raw('sum(order_quantity)as total'), 'delivery_date')
        ->where([
            ['order_status', '=','delivered'],
            ['delivery_date', '<=', $Date]
        ])
        ->groupBy('delivery_date')
        ->orderBy('delivery_date', 'ASC')
        ->get();
        // $Date = $Delivered[0]['delivery_date'];
        return response($Delivered);
        // return response()->json($Delivered);
    }
}
