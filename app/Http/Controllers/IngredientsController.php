<?php

namespace App\Http\Controllers; 
use App\Http\Resources\IngredientsCollection;
use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use App\Models\Ingredients;
use App\Models\UsedIngredients;
use App\Models\IngredientsAmount;
use DB;

class IngredientsController extends Controller
{

   
    public function saveUsedIngredients(Request $request){
        $data = $request->all();
        $ing = new UsedIngredients;
        $getID = DB::table('ingredients_amount')
            ->select('id')
            ->where('ingredients_name', '=', $data['availableIngredients'])
            ->first()->id;

        try {
            $ing->ingredients_id = $getID;
            $ing->used_ingredients_amount = $data['usedIngredientsAmount'];
            $ing->save();

            $result = DB::table('ingredients')
                ->select('ingredients_remaining')->where('ingredients_amount_id','=', $getID)->get();
            foreach($result as $item){
                $usedQty = intval($data['usedIngredientsAmount']);

                $res = Ingredients::where('ingredients_amount_id', $getID )
                ->update([
                    'ingredients_remaining' => $item->ingredients_remaining - $usedQty,
                ]);
            }
            $this->checkStatus(); 
        } catch(\Excetion $e){
            return response()->json($e);
        }
        return 'sucess';
    }

    public function editStockIngredients()
    {
    //   $post = IngredientsView::find(1);
    $post = DB::table('ingredients')
        ->join('ingredients_amount', 'ingredients_amount.id', '=', 'ingredients.ingredients_amount_id')
        ->select(
            'ingredients_amount.id',
            'ingredients.ingredients_remaining',
            'ingredients.ingredients_status',
            'ingredients_amount.ingredients_name'
        )
        ->where('ingredients_amount.id', 1)
        ->get();
        $obj = [];
    foreach($post as $item){
        $obj['id'] = $item->id;
        $obj['ingredients_remaining'] = $item->ingredients_remaining;
        $obj['ingredients_status'] = $item->ingredients_status;
        $obj['ingredients_name'] = $item->ingredients_name;
    }
    dd($post);
      return response()->json($post);
    }

    public function updateStockIngredients(Request $request)
    {
   
      $post = Ingredients::firstOrCreate(['id' => $request->id]);
    //   $post->ingredients_name= $request['ingredients_name'];
      $post->ingredients_remaining= $request['ingredients_remaining'];
      $post->ingredients_status = $request['ingredients_status'];
      $post->save();
      $this->checkStatus();
      return response()->json(compact('post'));
    }

    public function fetchStock(Request $request)
    {
        $this->checkStatus();
        $posts = DB::table('ingredients_amount')
            ->leftjoin('used_ingredients', 'ingredients_amount.id', '=', 'used_ingredients.ingredients_id')
            ->join('ingredients','ingredients_amount.id', '=','ingredients.ingredients_amount_id')
            ->select(
                'ingredients_amount.id',
                'ingredients_amount.ingredients_name',
                'ingredients_amount.ingredients_need_amount',
                'used_ingredients.used_ingredients_amount',
                'ingredients.ingredients_remaining',
                'ingredients.ingredients_status',
            DB::raw('sum(used_ingredients.used_ingredients_amount)as total'))
            ->groupBy(
                'ingredients_amount.id',
                'used_ingredients.used_ingredients_amount',
                'ingredients_amount.ingredients_name',
                'ingredients_amount.ingredients_need_amount',
                'ingredients.ingredients_remaining',
                'ingredients.ingredients_status'
                )
            ->get();

            $results = array();
            $i = 0; 
        
            foreach($posts as $item){
                if(array_key_exists('id', $posts->toArray())){
                    return response()->json([
                        'message' => 'New post created'
                    ]);
                } else{
                    $item->total = $this->total($item->id);
                }
                
                continue;
                $i++;
            }
        return response()->json($posts);
        return response()->json([
            'message' => 'New post created'
        ]);
        
    }

    public function addEstimatedAmount(Request $request){
        $posts = new  IngredientsAmount;   
        $data = $request->all();
        $posts->ingredients_name=$data['ingredientsName'];
        $posts->ingredients_need_amount=$data['ingredientsEstimatedAmount'];
        $posts->ingredients_category=$data['ingredientsCategory'];
        $posts->save();
    }


    public function total($id) {
        $data = DB::table('used_ingredients')->where('ingredients_id', $id)->get();
        $results= array();
        $i = 0;
        $total = 0;
        foreach($data as $item){
            $total += $item->used_ingredients_amount;
            $i++;
        }
        return $total;
    }



    public function checkStatus(){
        $message = '';
        try{
        $data = DB::table('ingredients')
        ->join('ingredients_amount', 'ingredients.ingredients_amount_id','=','ingredients_amount.id')
        ->select('ingredients.id',
                'ingredients.ingredients_remaining',
                'ingredients_amount.ingredients_need_amount',
                'ingredients.ingredients_status'
                )
        ->get();
       
      
        $i = 0; 
        foreach($data as $item){
           
            if($item->ingredients_remaining > ($item->ingredients_need_amount + 20)){
               
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Good Level of Stock',
                    ]);
            }else if($item->ingredients_remaining == ($item->ingredients_need_amount + 20)){
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Warning! Stock level is almost running out low',
                    ]);
            }else{
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Alert! Stock is Very Low',
                    ]);
            }
        }
            }catch(Exception $e){
                $message = 'failed';
                return response()->json(['error'=>$e]);

            }
     
    }

    public function newIngredients(Request $request){
        $data = $request->all();
        $message = '';
        $getID = DB::table('ingredients_amount')
            ->select('id')
            ->where('ingredients_amount.ingredients_name', '=', $data['ingredientsName'])
            ->first()->id;
            // dd($getID);
        try {
            $test = DB::table('ingredients')
            ->select('*')
            ->where('ingredients_amount_id', '=', $getID)
            ->get();
            
            if(sizeof($test) == 0){
                $res = new Ingredients;
                $res->ingredients_amount_id = $getID;
                $res->ingredients_remaining = $data['ingredientsUnit'];
                $res->ingredients_status = $data['stockStatus'];
                $res->save();
                $message = 'not existed';
                $this->checkStatus();
            
            } elseif(sizeof($test) > 0){
                $message = 'existed';
            }
        }catch(\Exception $e){
            return response()->json(['error'=>$e]);
        }
        return response()->json($message);
    }

    public function fetchEstimatedValue(){
        $entireTable = ingredientsAmount::all();
        return $entireTable;

    }

    public function editEstimatedValue($id){
        $post = ingredientsAmount::find($id);
        return response()->json($post);
    }

    public function updateEstimatedValue(Request $request){
      $post = ingredientsAmount::firstOrCreate(['id' => $request->id]);
      $post->ingredients_name= $request['ingredients_name'];
      $post->ingredients_need_amount= $request['ingredients_need_amount'];
      $post->ingredients_category= $request['ingredients_category'];
      $post->save();
      return response()->json(compact('post'));
    }
}

