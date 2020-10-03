<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\IngredientsCollection;
use App\Models\Product;
use App\Models\Ingredients;
use IngredientsController;

use DB;

class ProductController extends Controller
{
    public function fetchExpectedProd(request $request){
        $getUbeKilo = DB::table('ingredients')
        ->select('ingredients_unit')
        ->where('ingredients_name', '=', 'Ube')
        ->get();
        $key = $getUbeKilo[0]->ingredients_unit;
        $expect = $key*10;
        $expected_output=$expect;
        return $expected_output;
  
    }

    public function orderSum(Request $request){
        $totalSum = DB::table('orders')
        ->select(DB::raw('sum(order_quantity) as total'))
        ->where('order_status', 'On order')
        ->get();
        $sum =intval($totalSum[0]->total);
        return $sum;
      
    }

    public function stockStatus(Request $request){
        $sumTotal = $this->orderSum($request);
        $expectedProduct = $this->fetchExpectedProd($request);
        if($expectedProduct >= $sumTotal){
             $status = 'Enough';
             return $status;
        } else{
            $status = 'Not enough';
            return $status;
        }
        
    }
}





// $sumTotal = $this->fectchSum($request);
// $post = new Product;
//  return response()->json($expected_output);
// $post->total_product_order = $sumTotal;
// $post->expected_product_output = $value;
// $post->save();
// return response()->json($value);

 // $array = array(
        //     25  => '1',
        //     50  => '2',
        //     75  => '3',
        //     100 => '4',
        //     150 => '5',   
        //     160 => '6', 
        // );
             // $value =  array_search($key, $array );
        // $expected_output=null;
        // $data=$request->all();
