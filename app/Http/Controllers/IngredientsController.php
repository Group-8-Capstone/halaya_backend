<?php

namespace App\Http\Controllers; 
use App\Http\Resources\IngredientsCollection;
use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use App\Models\Ingredients;
use App\Models\UsedIngredients;
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


    public function createStock(Request $request)
    {
        $post = new Ingredients;
        $data = $request->all();
        $post->ingredients_name = $data['ingredientsName'];
        $post->ingredients_status = $data['stockStatus'];
        $post->ingredients_unit = $data['ingredientsUnit'];
       
     $isExist = Ingredients::select("*")
                        ->where("ingredients_name",$data['ingredientsName'])
                        ->exists();
        if ($isExist) {
            $findId = DB::table('ingredients')
            ->select('id')
            ->where('ingredients_name', '=', $data['ingredientsName'])
            ->get();
            return response()->json([
                'message' => 'existed'
            ]);
        }else{
            $post->save();
            return response()->json([
                'message' => 'not existed'
            ]);
        }
    }

    public function updateStockAmount(Request $request)
    {
        $post = new Ingredients;
        $data = $request->all();
        $post->ingredients_name= $data['availableIngredients'];
        $post->ingredients_status= 'Enough';
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
      return response()->json(compact('post'));
    }

    // public function fetchStock(Request $request)
    // {
    //     $posts = Ingredients::orderBy('created_at', 'asc')->get();
    //     return response()->json($posts);
    //     return response()->json([
    //         'message' => 'New post created'
    //     ]);
    // }
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

    // public function stockStatus(Request $request){
    //     //Estimated ingredients amount for two weeks
    //     $ube = 200;             //kilos
    //     $condensed = 18720;     //grams 48 cans
    //     $evap = 17760;          //grams 48 cans
    //     $butter = 5.4;          //kilos
    //     $sugar = 30;            //kilos

    //     $data = DB::table('ingredients')
    //     ->join('used_ingredients', 'ingredients.id', '=', 'used_ingredients.ingredients_id')
    //     ->select('ingredients_name', 'ingredients_unit');

    //     print_r($data);
    //     if()
    // }
}

