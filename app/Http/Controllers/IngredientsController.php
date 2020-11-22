<?php

namespace App\Http\Controllers; 
use App\Http\Resources\IngredientsCollection;
use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use App\Models\Ingredients;
use App\Models\UsedIngredients;
use App\Models\IngredientsAmount;
use Carbon\Carbon;
use DB;

class IngredientsController extends Controller
{ 
    private function notFoundMessage()
    {

        return [
            'code' => 404,
            'message' => 'Note not found',
            'success' => false,
        ];

    }
    private function successfulMessage($code, $message, $status)
    {

        return [
            'code' => $code,
            'message' => $message,
            'success' => $status,
          
        ];

    }

   
    public function saveUsedIngredients(Request $request){
        try {
            $data = $request->all();
            $ing = new UsedIngredients;
            $getID = DB::table('ingredients_amount')
            ->select('id')
            ->where('ingredients_name', '=', $data['availableIngredients'])
            ->first()->id;

            $ing->ingredients_id = $getID;
            $ing->used_ingredients_amount = $data['usedIngredientsAmount'];
            $ing->ingredients_unit=$data['ingredientsUnit'];
            $ing->ingredients_name = $data['availableIngredients'];
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
            $this->checkStatus($getID); 
        } catch(\Excetion $e){
            return response()->json(['error'=>$e->getMessage()]);
        }
        return 'success';
    }

    public function fetchUsedIng(){
        try {
            $entireTable = UsedIngredients::all();
            return $entireTable;
        } catch(\Excetion $e){
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function editStockIngredients($id)
    {
        try {
            $post = Ingredients::find($id);
            return response()->json($post);
        } catch(\Excetion $e){
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function updateStockIngredients(Request $request)
    {
        $data = $request->all();
        try {
            $res = Ingredients::where('ingredients_amount_id', $data['id'] )
                ->update([
                    'ingredients_remaining' => $data['ingredients_remaining'],
                ]);
            $this->checkStatus($data['id']);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
        return 'success';
    }

    public function fetchStock(Request $request)
    {
        try {
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
                ->where('ingredients.deleted_at', null)
                ->groupBy(
                    'ingredients_amount.id',
                    'used_ingredients.used_ingredients_amount',
                    'ingredients_amount.ingredients_name',
                    'ingredients_amount.ingredients_need_amount',
                    'ingredients.ingredients_remaining',
                    'ingredients.ingredients_status'
                    )
                ->get();

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
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }



    public function softDeleteIngredients($id)
    {
        try {
            $post = IngredientsAmount::destroy($id);
            if ($post) {
                $response = $this->successfulMessage(200, 'Successfully deleted', true);
            } else {
                $response = $this->notFoundMessage();
            }
            return response($response);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
      
    }

    public function softDeleteStockIngredients(Request $request, $id)
    {
        try {
            $ing = Ingredients::find(['id' => $request->ingredients_amount_id]);
            $ing = Ingredients::destroy($id);
            if ($ing) {
                $response = $this->successfulMessage(200, 'Successfully deleted', true);
            } else {
                $response = $this->notFoundMessage();
            }
            return response($response);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
      
    }

    public function getHalayaIngredients(){
        try {
            $post = DB::table('ingredients_amount')
                ->join('ingredients', 'ingredients.ingredients_amount_id', '=', 'ingredients_amount.id')
                ->leftjoin('used_ingredients', 'ingredients_amount.id', '=', 'used_ingredients.ingredients_id')
                ->select(
                    'ingredients_amount.id',
                    'ingredients.ingredients_remaining',
                    'ingredients.ingredients_status',
                    'ingredients_amount.ingredients_name',
                    'ingredients_amount.ingredients_unit',
                    'used_ingredients.used_ingredients_amount',
                    'ingredients.deleted_at',
                    DB::raw('sum(used_ingredients.used_ingredients_amount)as total'))
                ->where('ingredients_amount.ingredients_category', 'Ube Halaya')
                ->where('ingredients.deleted_at','=', null)
                ->groupBy(
                    'ingredients_amount.id',
                    'ingredients.ingredients_remaining',
                    'ingredients.ingredients_status',
                    'ingredients_amount.ingredients_name',
                    'ingredients_amount.ingredients_unit',
                    'used_ingredients.used_ingredients_amount',
                    'ingredients.deleted_at'
                    )
                ->get();

                $i = 0; 
        
            foreach($post as $item){
                if(array_key_exists('id', $post->toArray())){
                    return response()->json([
                        'message' => 'New post created'
                    ]);
                } else{
                    $item->total = $this->total($item->id);
                }
                
                continue;
                $i++;
            }
            return $post;
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function fetchIngredientsName(){
        try{
            $post = DB::table('ingredients_amount')
            ->select('id','ingredients_name','ingredients_category')
            // ->where('ingredients.ingredients_status', '!=', 'Deleted')
            ->get();
            return $post;
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function getAllIngredients($name){
        
        try {
            $post = DB::table('ingredients_amount')
            ->select('id','ingredients_name','ingredients_category')
            ->where('ingredients_name', $name)
            ->get();
            foreach($post as $item){
                $data = new Ingredients;
                $data->ingredients_amount_id = $item->id;
                $data->ingredients_remaining = 0;
                $data->ingredients_status = 'Calculating...';
                $data->ingredients_category = $item->ingredients_category;
                $data->save();
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function addEstimatedAmount(Request $request){
        try {
            $posts = new  IngredientsAmount;   
            $data = $request->all();
            $posts->ingredients_name=$data['ingredientsName'];
            $posts->ingredients_need_amount=$data['ingredientsEstimatedAmount'];
            $posts->ingredients_unit=$data['ingredientsUnit'];
            $posts->ingredients_category=$data['ingredientsCategory'];
            $posts->save();
            $this->getAllIngredients($data['ingredientsName']);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }


    public function total($id) {
        try {
            $monthYear = Carbon::now();
            $data = DB::table('used_ingredients')
                ->where('ingredients_id', $id )
                ->whereMonth('created_at',$monthYear->month)
                ->whereYear('created_at',$monthYear->year)
                ->get();
                
            $i = 0;
            $total = 0;
            foreach($data as $item){
                $total += $item->used_ingredients_amount;
                $i++;
            }
            return $total;
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
        
    }

    public function checkStatus($id){
        $message = '';
        try{
        $data = DB::table('ingredients')
        ->join('ingredients_amount', 'ingredients.ingredients_amount_id','=','ingredients_amount.id')
        ->select('ingredients.id',
                'ingredients.ingredients_remaining',
                'ingredients_amount.ingredients_need_amount',
                'ingredients.ingredients_status'
                )
        ->where('ingredients_amount.id','=', $id)
        ->get();

        $i = 0; 
        foreach($data as $item){
           
            if($item->ingredients_remaining > ($item->ingredients_need_amount + 20)){
               
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Good Level',
                    ]);
            }else if(($item->ingredients_remaining <= ($item->ingredients_need_amount + 20)) && $item->ingredients_remaining > ($item->ingredients_need_amount)){
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Warning! Running Low',
                    ]);
            }else{
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_remaining' => $item->ingredients_remaining,
                        'ingredients_status' => 'Alert! Very Low',
                    ]);
            }
        }
            }catch (\Exception $e) {
                return response()->json(['error'=>$e->getMessage()]);
            }
     return $data;
    }

    public function newIngredients(Request $request){
        
        try {
            $data = $request->all();
            $message = '';
            $getID = DB::table('ingredients_amount')
                ->select('id')
                ->where('ingredients_amount.ingredients_name', '=', $data['ingredientsName'])
                ->first()->id;

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
        }catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
        return response()->json($message);
    }

    public function fetchEstimatedValue(){
        try {
            $entireTable = ingredientsAmount::all();
            return $entireTable;
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function editEstimatedValue($id){
        try {
            $post = ingredientsAmount::find($id);
            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function updateEstimatedValue(Request $request){
        try{
            $post = ingredientsAmount::firstOrCreate(['id' => $request->id]);
            $post->ingredients_name= $request['ingredients_name'];
            $post->ingredients_need_amount= $request['ingredients_need_amount'];
            $post->ingredients_unit= $request['ingredients_unit'];
            $post->ingredients_category= $request['ingredients_category'];
            $post->save();
            return response()->json(compact('post'));
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
      
    }

    public function deleteIngredients($id){
        try {
            $res = Ingredients::where('ingredients_amount_id', $id )
            ->update([
                'ingredients_status' => 'Deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
       
    }
}