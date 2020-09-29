<?php

namespace App\Http\Controllers; 
use App\Http\Resources\IngredientsCollection;

use Illuminate\Http\Request;
use App\Models\Ingredients;
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
        $array = array(
            50 => '1',
            100 => '2',
            150 => '3'
                
        );
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
            $post = Ingredients::find($findId[0]->id);
            $newAdded = intval($data['ingredientsUnit']);
            $post->ingredients_unit += $newAdded;
            $post->save();
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

    public function addOrder(Request $request, $id)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $request->id]);
      $post->order_status = 'Delivered';
      $post->update();
      return response()->json(compact('post'));
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


    public function fetchStock(Request $request)
    {
        $post = new Ingredients;
        $posts = Ingredients::orderBy('created_at', 'asc')->get();
        return response()->json($posts);
        return response()->json([
            'message' => 'New post created'
        ]);
    }
}




// return response()->json($sum);
                // $posts = DB::table('orders')
        //             ->select('delivery_date', DB::raw('count(*) as orders'), 
        //              DB::raw('sum(order_quantity) as total'))
        //              ->where('order_status', 'On order')
        //              ->groupBy('delivery_date')
        //              ->get();
        // return response()->json($posts);
