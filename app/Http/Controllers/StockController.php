<?php

namespace App\Http\Controllers; 
use App\Http\Resources\StockCollection;

use Illuminate\Http\Request;
use App\Models\Stock;
use DB;

class StockController extends Controller
{

    private function exist(Request $request){
        $data=$request->all();
        $post = Stock::where('delivery_date',Input::get('delivery_date'))->first();
        if (is_null($post)) {
        print_r("deliveryDate is exists");
        }
        print_r("deliveryDate is not exists");
    }


    public function createStock(Request $request)
    {
        $post = new Stock;
        $data = $request->all();
        $array = array(
            50 => '1',
            100 => '2',
            150 => '3'
                
        );
        // $good = $this->postOrdered($data['deliveryDate']);
        // $post->total_quantity_order = intval($good);
        $post->ube_kilo = $data['ubeKilo'];
        $key = $data['ubeKilo'];
        $post->delivery_date = $data['deliveryDate'];
        $post->stock_status = $data['stockStatus'];
        
        $value =  array_search( $key, $array );
        $post->expected_output = $value;
        $isExist = Stock::select("*")
                        ->where("delivery_date", $data['deliveryDate'])
                        ->exists();
        if ($isExist) {
            return response()->json([
                'message' => 'already'
            ]);
        }else{
            $post->save();
            return response()->json([
                'message' => 'not'
            ]);
        }
    }

    public function postOrdered($date){
        $posts = DB::table('orders')
        ->select('delivery_date', DB::raw('count(*) as countOrder'), 
            DB::raw('sum(orders.order_quantity) as total'))
            ->where([
                ['order_status', 'On order'],
                ['delivery_date', '=', $date]
            ])
            ->groupBy('delivery_date')
            ->get();
        return $posts[0]->total;
    }

    public function fectchByGroup(Request $request){
        $posts = DB::table('orders')
                    ->select('delivery_date', DB::raw('count(*) as orders'), 
                    DB::raw('sum(order_quantity) as total'))
                     ->where('order_status', 'On order')
                     ->groupBy('delivery_date')
                     ->get();
        return response()->json($posts);
    }


    public function fetchStock(Request $request)
    {
        $post = new Stock;
        $posts = Stock::orderBy('created_at', 'asc')->get();
        return response()->json($posts);
        return response()->json([
            'message' => 'New post created'
        ]);
    }
}
