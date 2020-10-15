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

    public function updateStockAmount(Request $request)
    {
        $post = new Ingredients;
        $data = $request->all();
        $post->ingredients_name= $data['availableIngredients'];
        $post->ingredients_remaining= $data['usedIngredientsAmount'];
       
     $isExist = Ingredients::select("*")
                        ->where("ingredients_name",$data['availableIngredients'])
                        ->exists();
        if ($isExist) {
            $findId = DB::table('ingredients')
            ->select('id')
            ->where('ingredients_name', '=', $data['availableIngredients'])
            ->get();
            
            $post = Ingredients::find($findId[0]->id);
            $newAdded = intval($data['usedIngredientsAmount']);
            $post->ingredients_remaining -= $newAdded;
            $post->save();

            $id = DB::table('ingredients')
            ->select('id')
            ->where('ingredients_name', '=', $data['availableIngredients'])
            ->first()->id;
            $this->saveUsedIngredients($id,$data['usedIngredientsAmount']);

            return response()->json($post);
        }else{
            return response()->json([
                'message' => 'not existed'
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

    public function editStockIngredients($id)
    {
      $post = Ingredients::find($id);
      return response()->json($post);
    }

    public function updateStockIngredients(Request $request)
    {
   
      $post = Ingredients::firstOrCreate(['id' => $request->id]);
      $post->ingredients_name= $request['ingredients_name'];
      $post->ingredients_remaining= $request['ingredients_remaining'];
      $post->ingredients_status = $request['ingredients_status'];
      $post->save();
      $this->checkStatus();
      return response()->json(compact('post'));
    }

    public function fetchStock(Request $request)
    {
        $this->checkStatus();
        $posts = DB::table('ingredients')
            ->leftjoin('used_ingredients', 'ingredients.id', '=', 'used_ingredients.ingredients_id')
            ->select('ingredients.*','used_ingredients.used_ingredients_amount',
            DB::raw('sum(used_ingredients.used_ingredients_amount)as total'))
            ->groupBy(
                    'ingredients.id',
                    'ingredients.ingredients_amount_id',
                    'used_ingredients.used_ingredients_amount',
                    'ingredients.ingredients_name',
                    'ingredients.ingredients_remaining',
                    'ingredients.ingredients_status',
                    'ingredients.created_at',
                    'ingredients.updated_at'
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
        $posts->save();
    }


    public function saveUsedIngredients($id,$amount){
        $ing = new UsedIngredients;
        $ing->ingredients_id = $id;
        $ing->used_ingredients_amount = $amount;
        $ing->save();
        $this->checkStatus();
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
        ->join('ingredients_amount', 'ingredients.ingredients_name','=','ingredients_amount.ingredients_name')
        ->select('ingredients.id',
                'ingredients.ingredients_name',
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
                        'ingredients_name' => $item->ingredients_name,
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Good Level of Stock',
                    ]);
            }else if($item->ingredients_remaining == ($item->ingredients_need_amount + 20)){
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_name' => $item->ingredients_name,
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Warning! Stock level is almost running out low',
                    ]);
            }else{
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_name' => $item->ingredients_name,
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
      
        try {
            $test = DB::table('ingredients')
            ->select('*')
            ->where('ingredients_name', '=', $data['ingredientsName'])
            ->get();
            if(sizeof($test) == 0){
                $res = new Ingredients;
                $res->ingredients_name = $data['ingredientsName'];
                $res->ingredients_status = $data['stockStatus'];
                $res->ingredients_remaining = $data['ingredientsUnit'];
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
      $post->save();
      return response()->json(compact('post'));
    }
}

