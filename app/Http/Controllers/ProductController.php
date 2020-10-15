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

    // public function expectedUbeOutput(Request $request){
    //     $ubeAmount = DB::table('ingredients')
    //     ->select('ingredients_unit')
    //     ->where('ingredients_name', '=', 'Ube')->get();

    //     $Ube  = $ubeAmount[0]->ingredients_unit;
    //     $expected_ube = ($Ube * 1000) / $this->ube;
    //     // echo($expected_ube);
    //     return $expected_ube;
        
    // }

    // public function expectedCondensedOutput(Request $request){
    //     $condensedAmount = DB::table('ingredients')
    //     ->select('ingredients_unit')
    //     ->where('ingredients_name', '=', 'Condensed milk')->get();

    //     $Condensada = $condensedAmount[0]->ingredients_unit;
    //     $expected_condensed = ($Condensada * 390) / $this->condensed;
    //     echo($expected_condensed);
    //     return $expected_condensed;     
    // }

    // public function expectedEvapOutput(Request $request){
    //     $evapAmount = DB::table('ingredients')
    //     ->select('ingredients_unit')
    //     ->where('ingredients_name', '=', 'Evaporated milk')->get();

    //     $Evaporada = $evapAmount[0]->ingredients_unit;
    //     $expected_evap =  ($Evaporada * 370) / $this->evap;
    //     return $expected_evap;
    // }

    // public function expectedButterOutput(Request $request){
    //     $butterAmount = DB::table('ingredients')
    //     ->select('ingredients_unit')
    //     ->where('ingredients_name', '=', 'Butter')->get();

    //     $Butter = $butterAmount[0]->ingredients_unit;
    //     $expected_butter = ($Butter * 1000) / $this->butter; //by kilo ang pag input
    //     return $expected_butter;
    // }

    // public function expectedSugarOutput(Request $request){
    //     $sugarAmount = DB::table('ingredients')
    //     ->select('ingrediens_unit')
    //     ->where('ingredients_name', '=', 'Sugar')->get();

    //     $Sugar = $sugarAmount[0]->ingredients_unit;
    //     $expected_sugar = ($Sugar * 1000) / $this->sugar;
    //     return $expected_sugar;
    // }

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



