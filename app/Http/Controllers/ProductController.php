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

    //Estimated amount of ingredients per jar
    private $ube = 100;  //grams
    private $condensed = 12.48; //grams
    private $evap = 11.84; //ml
    private $butter = 3.6; //grams
    private $sugar = 20; //grams


    // public function fetchExpectedProd(request $request){
    //     $getUbeKilo = DB::table('ingredients')
    //     ->select('ingredients_unit')
    //     ->where('ingredients_name', '=', 'Ube')
    //     ->get();
    //     $array = array(
    //         25  => '1',
    //         50  => '2',
    //         75  => '3',
    //         100 => '4',
    //         150 => '5',   
    //         160 => '6', 
    //     );
    //     $expected_output=null;
    //     $data=$request->all();
    //     $key = $getUbeKilo[0]->ingredients_unit;
    //     $value =  array_search($key, $array );
    //     $expected_output=$value;
    //     return $expected_output;
  
    // }

    public function expectedUbeOutput(Request $request){
        $ubeAmount = DB::table('ingredients')
        ->select('ingredients_unit')
        ->where('ingredients_name', '=', 'Ube')->get();

        $Ube  = $ubeAmount[0]->ingredients_unit;
        $expected_ube = ($Ube * 1000) / $this->ube;
        // echo($expected_ube);
        return $expected_ube;
        
    }

    public function expectedCondensedOutput(Request $request){
        $condensedAmount = DB::table('ingredients')
        ->select('ingredients_unit')
        ->where('ingredients_name', '=', 'Condensed milk')->get();

        $Condensada = $condensedAmount[0]->ingredients_unit;
        $expected_condensed = ($Condensada * 390) / $this->condensed;
        echo($expected_condensed);
        return $expected_condensed;     
    }

    public function expectedEvapOutput(Request $request){
        $evapAmount = DB::table('ingredients')
        ->select('ingredients_unit')
        ->where('ingredients_name', '=', 'Evaporated milk')->get();

        $Evaporada = $evapAmount[0]->ingredients_unit;
        $expected_evap =  ($Evaporada * 370) / $this->evap;
        return $expected_evap;
    }

    public function expectedButterOutput(Request $request){
        $butterAmount = DB::table('ingredients')
        ->select('ingredients_unit')
        ->where('ingredients_name', '=', 'Butter')->get();

        $Butter = $butterAmount[0]->ingredients_unit;
        $expected_butter = ($Butter * 1000) / $this->butter; //by kilo ang pag input
        return $expected_butter;
    }

    public function expectedSugarOutput(Request $request){
        $sugarAmount = DB::table('ingredients')
        ->select('ingrediens_unit')
        ->where('ingredients_name', '=', 'Sugar')->get();

        $Sugar = $sugarAmount[0]->ingredients_unit;
        $expected_sugar = ($Sugar * 1000) / $this->sugar;
        return $expected_sugar;
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
        $expectedUbe = $this->expectedUbeOutput($request);
        if($expectedUbe >= $sumTotal){
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
