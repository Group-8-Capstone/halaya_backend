<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\IngredientsCollection;
use App\Models\Product;
use App\Models\MakeProduct;
use App\Models\RecordedProduct;
use App\Models\Ingredients;
use IngredientsController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use DB;

class ProductController extends Controller
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

    //New

    public function fetchRecordedProduct(){
   
        $product = RecordedProduct::all();
        return response()->json(compact('product'));
    }


    
    public function fetchHalayaTub(){
        $product = Product::select('id','product_name','product_price','product_availability')->where('product_name','Ube Halaya Tub')->get();
        return response()->json(compact('product'));

    }

    public function fetchHalayaJar(){
        $product = Product::select('id','product_name','product_price','product_availability')->where('product_name','Ube Halaya Jar')->get();
        return response()->json(compact('product'));

    }

    public function editTub(Request $request,$id){
        $product = Product::findOrFail($id);
        $product->product_price = $request['product_price'];
        $product->product_availability = $request['product_availability'];
        $product->save();
    }

    public function editJar(Request $request,$id){
        $product = Product::findOrFail($id);
        $product->product_price = $request['product_price'];
        $product->product_availability = $request['product_availability'];
   
        $product->save();
    }

//    public function recordJars(Request $request){
//     try{
//     $post = new RecordedProduct ;
//     $data=$request->all();
//     $post->product_name = $data['product_name'];
//     $post->remaining_quantity = $data['remaining_quantity'];
//     $post->total_ordered = $data['total_ordered'];
//     $post->availability_status = $data['availability_status'];
//     $post->save();
//     return 'success';
//         } catch (\Exception $e){
//   return response()->json(['error'=>$e]);
//     }

//    }
   

   public function recordTubs(Request $request){
    try{
    $post = new RecordedProduct ;
    $data=$request->all();
    $post->product_name = $data['product_name'];
    $post->remaining_quantity = $data['remaining_quantity'];
    $post->total_ordered = $data['total_ordered'];
    $post->availability_status = $data['availability_status'];
    $post->save();
    return 'success';
        } catch (\Exception $e){
  return response()->json(['error'=>$e]);
    }

   }


   public function dailyRecords(Request $request){
    $data=$request->all();
    $productRecord = RecordedProduct::where('product_name', '=', $data['product_name'])
    ->whereDate('created_at', '=', Carbon::today()->toDateString())->exists();
    if($productRecord ==false){
        try{
            $post = new RecordedProduct ;
            $data=$request->all();
            $post->product_name = $data['product_name'];
            $post->remaining_quantity = $data['remaining_quantity'];
            $post->total_ordered = $data['total_ordered'];
            $post->availability_status = $data['availability_status'];
            $post->save();
            return 'success';
                } catch (\Exception $e){
          return response()->json(['error'=>$e]);
            }
        }else{
          return 'existed';
    }
   }
































//Old

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productName' => 'required|string|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        // $imageName = time().'.'.$request->image->getClientOriginalExtension();
        // $request->image->move(public_path('images'), $imageName);
        // $data = $request->all();
        // $product = new Product();
        // $product->image = 'images/'.$imageName;
        // $product->product_name = $data['productName'];
        // $product->save();
        
        // return response()->json(['success'=>'You have successfully upload image.']);
        try { 
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $data = $request->all();
            $product = new Product();
            $product->image = 'images/'.$imageName;
            $product->product_name = $data['productName'];
            $product->save();
            $this->getAllProduct($data['productName']);
            
        } catch ( \Exception $e)  {
            return response()->json($e);
        }
    }

    public function retrieveProduct(Request $request){
        $addProduct = Product::orderBy('id','ASC')->get();
        return response()->json(compact('addProduct'));
    }

    public function updateStockProduct(Request $request)
    {
        $data = $request->all();
        try {
            $res = MakeProduct::where('product_id', $data['id'] )
                ->update([
                    'product_remaining' => $data['productRemaining'],
                ]);
        } catch (\Exception $e) {
            return 'failed';
            return response()->json($e);
        }
        return 'success';
    }

    public function updateProduct(Request $request){
        $imageName = time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'), $imageName);
        $product = Product::firstOrCreate(['id' => $request->id]);
        $product->image = 'images/'.$imageName;
        $product->product_name = $request['productName'];
        $product->save();
        return response()->json(compact('product'));
    }

    public function getAllProduct($name){
        try {
            $post = DB::table('products')
            ->select('id','product_name','image')
            ->where('product_name', $name)
            ->get();
            foreach($post as $item){
                $data = new MakeProduct;
                $data->product_id = $item->id;
                $data->product_remaining = 0;
                $data->product_status = 'Calculating...';
                $data->save();
            }
        } catch ( \Exception $e) {
            return response()->json($e);
        }
    }



    public function softDeleteProduct($id)
    {
      $post = Product::destroy($id);
      if ($post) {
          $response = $this->successfulMessage(200, 'Successfully deleted', true);

      } else {
          $response = $this->notFoundMessage();
      }
      return response($response);
    }

    public function softDeleteStockProducts(Request $request, $id)
    {
      $product = MakeProduct::find(['id' => $request->product_id]);
      $product = MakeProduct::destroy($id);
      if ($product) {
          $response = $this->successfulMessage(200, 'Successfully deleted', true);
      } else {
          $response = $this->notFoundMessage();
      }
      return response($response);
    }




    public function postStockProduct(){
        try {
            $post = DB::table('products')
                ->join('make_product', 'make_product.product_id', '=', 'products.id')
                // ->leftjoin('ordered_product', 'products.id', '=', 'ordered_product.make_product_id')
                ->select(
                    'products.id',
                    'make_product.product_remaining',
                    'make_product.product_status',
                    'products.product_name',
                    'products.image',
                    'make_product.deleted_at',
                 
                    // 'ordered_product.ordered_product_quantity',
                    // DB::raw('sum(ordered_product.ordered_product_quantity)as total'))
                )
                ->where('make_product.deleted_at','=',null)
                ->groupBy(
                    'products.id',
                    'products.image',
                    'make_product.product_remaining',
                    'make_product.product_status',
                    'products.product_name',
                    'make_product.deleted_at',
                    // 'ordered_product.ordered_product_quantity',
                    )
                ->get();

                $i = 0; 
        
            // foreach($post as $item){
            //     if(array_key_exists('id', $post->toArray())){
            //         return response()->json([
            //             'message' => 'New post created'
            //         ]);
            //     } else{
            //         $item->total = $this->total($item->id);
            //     }
                
            //     continue;
            //     $i++;
            // }
            return response()->json($post);
            // return $post;
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    // public function fetchExpectedProd(request $request){
    //     $getUbeKilo = DB::table('ingredients')
    //     ->select('ingredients_unit')
    //     ->where('ingredients_name', '=', 'Ube')
    //     ->get();
    //     $key = $getUbeKilo[0]->ingredients_unit;
    //     $expect = $key*10;
    //     $expected_output=$expect;
    //     return $expected_output;

    // }

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

    // public function orderSum(Request $request){
    //     $totalSum = DB::table('orders')
    //     ->select(DB::raw('sum(order_quantity) as total'))
    //     ->where('order_status', 'On order')
    //     ->get();
    //     $sum =intval($totalSum[0]->total);
    //     return $sum;
      
    // }

    // public function stockStatus(Request $request){
    //     $sumTotal = $this->orderSum($request);
    //     $expectedUbe = $this->expectedUbeOutput($request);
    //     if($expectedUbe >= $sumTotal){
    //          $status = 'Enough';
    //          return $status;
    //     } else{
    //         $status = 'Not enough';
    //         return $status;
    //     }
        
    // }
}



