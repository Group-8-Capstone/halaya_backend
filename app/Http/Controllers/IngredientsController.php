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
    private function exist(Request $request){
        $data=$request->all();
        $post = Ingredients::where('ingredients_name',Input::get('ingredients_name'))->first();
        if (is_null($post)) {
        print_r("ingredients_name is exists");
        }
        print_r("ingredients_name is not exists");
    }

    public function updateStockAmount(Request $request)
    {
        $post = new Ingredients;
        $data = $request->all();
        $post->ingredients_name= $data['availableIngredients'];
        $post->ingredients_status= 'Enough';                        //usbunon
        $post->ingredients_unit= $data['usedIngredientsAmount'];


       
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
            $post->ingredients_unit -= $newAdded;
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
      $post->ingredients_unit= $request['ingredients_unit'];
      $post->ingredients_status = $request['ingredients_status'];
      $post->save();
      $this->checkStatus();
      return response()->json(compact('post'));
    }

    public function fetchStock(Request $request)
    {
        $posts = DB::table('ingredients')
            ->leftjoin('used_ingredients', 'ingredients.id', '=', 'used_ingredients.ingredients_id')
            ->select('ingredients.*','used_ingredients.used_ingredients_amount',
            DB::raw('sum(used_ingredients.used_ingredients_amount)as total'))
            ->groupBy(
                    'ingredients.id',
                    'used_ingredients.used_ingredients_amount',
                    'ingredients.ingredients_name',
                    'ingredients.ingredients_unit',
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

     public function fetchIngredientsName(Request $request)
    {
        $post = new Ingredients;
        $post = $request->all();
        $posts = Ingredients::gBy('created_at', 'asc')->get();
        foreach ($posts as $key) {
            return response()->json($key->ingredients_name);
        }
    }

    public function saveUsedIngredients($id,$amount){
        $ing = new UsedIngredients;
        $ing->ingredients_id = $id;
        $ing->used_ingredients_amount = $amount;
        $ing->save();
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

    public function saveRealAmount(Request $request){
        $posts = new IngredientsAmount;
        $data = $request->all();
        $result = DB::table('ingredients')->select('id')
            ->where('ingredients_name',$data['ingredients_name'])
            ->get();
        $posts->ingredients_id = $result;
        $posts->ingredients_amount = $data['ingredients_amount'];
        $posts->save();
    }

    // public function ubeStatus($remainingAmount,$budgetAmount){
    //     if(($remainingAmount) > $budgetAmount + 20){
    //         return 'Good Level of Stock';
    //     }else if(($remainingAmount) == ($budgetAmount + 20)){
    //         return 'Warning! Stock level is almost running out low';
    //     }else{
    //         return 'Alert! Stock is Very Low';
    //     }
    // }

    public function checkStatus(){
        $data = DB::table('ingredients')
        ->join('ingredients_amount', 'ingredients.id', '=', 'ingredients_amount.ingredients_id')
        ->select('ingredients.id',
                'ingredients.ingredients_name',
                'ingredients.ingredients_unit',
                'ingredients_amount.ingredients_amount',
                'ingredients.ingredients_status'
                )
        ->get();
        $i = 0; 
        foreach($data as $item){
            if($item->ingredients_unit > ($item->ingredients_amount + 20)){
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_name' => $item->ingredients_name,
                        'ingredients_unit' => $item->ingredients_unit,
                        'ingredients_status' => 'Good Level of Stock',
                    ]);
            }else if($item->ingredients_unit == ($item->ingredients_amount + 20)){
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_name' => $item->ingredients_name,
                        'ingredients_unit' => $item->ingredients_unit,
                        'ingredients_status' => 'Warning! Stock level is almost running out low',
                    ]);
            }else{
                $res = Ingredients::where('id', $item->id )
                    ->update([
                        'ingredients_name' => $item->ingredients_name,
                        'ingredients_unit' => $item->ingredients_unit,
                        'ingredients_status' => 'Alert! Stock is Very Low',
                    ]);
            }
        }
        // return $data;
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
                $res->ingredients_unit = $data['ingredientsUnit'];
                $res->save();
                $message = 'not existed';
            } elseif(sizeof($test) > 0){
                // $unit = floatval($test[0]->ingredients_unit);
                // $res = Ingredients::where('ingredients_name', $data['ingredientsName'])
                // ->update([
                //     'ingredients_name' => $data['ingredientsName'],
                //     'ingredients_unit' => floatval($data['ingredientsUnit']) + $unit,
                //     'ingredients_status' => $data['stockStatus']
                // ]);
                $message = 'existed';
            }
        }catch(\Exception $e){
            return response()->json(['error'=>$e]);
        }
        $this->checkStatus();
        return response()->json($message);
    }
}

