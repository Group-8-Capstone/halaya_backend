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
        try {
            $product = RecordedProduct::all();
            return response()->json(compact('product'));    
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function fetchHalayaTub(){
        try {
            $product = Product::select('id','product_name','product_price','product_availability')->where('product_name','Ube Halaya Tub')->get();
            return response()->json(compact('product'));
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function fetchHalayaJar(){
        try{
            $product = Product::select('id','product_name','product_price','product_availability')->where('product_name','Ube Halaya Jar')->get();
        return response()->json(compact('product'));
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function editTub(Request $request,$id){
        try {
            $product = Product::findOrFail($id);
            $product->product_price = $request['product_price'];
            $product->product_availability = $request['product_availability'];
            $product->save();
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function editJar(Request $request,$id){
        try {
            $product = Product::findOrFail($id);
            $product->product_price = $request['product_price'];
            $product->product_availability = $request['product_availability'];
    
            $product->save();
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
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
    } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
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
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    } else {
          return 'existed';
    }
   }
}



