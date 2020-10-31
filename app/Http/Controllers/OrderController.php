<?php

namespace App\Http\Controllers;
use App\Http\Resources\DeliveryCollection;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Order;
use DB;

class OrderController extends Controller
{
  
    public function createOrder(Request $request)
    {
      try{
        $post = new Order;
        $data=$request->all();
        $post->customer_name = $data['name'];
        $post->customer_address = $data['address'];
        $post->contact_number = $data['contactNumber'];
        $post->order_quantity = $data['orderQuantity']; 
        $post->delivery_date = $data['deliveryDate'];
        $post->order_status = $data['orderStatus'];
        $post->longitude = $data['longitude'];
        $post->latitude = $data['latitude'];
        $post->distance = $data['distance'];
        $post->save();
      } catch (\Exception $e){
        return response()->json(['error'=>$e]);
      }
    }
  
   public function fetchOrder()
    {
      
      return new OrderCollection(Order::where('order_status', 'On order')
      ->orWhere('order_status', 'Canceled')
      ->orderBy('delivery_date', 'asc')->get());
    }

    public function fetchDelivered()
    {
      return new OrderCollection(Order::where('order_status', 'Delivered')->get());
    }

    public function fetchDelivery(Request $request){
      return new OrderCollection(Order::where('order_status', 'On order')
      ->orderBy('delivery_date', 'asc')->get());
      // dd(Carbon::today()->toDateString());
      // $order = Order::where('order_status', 'On order' AND 'delivery_date', Carbon::today()->toDateString())
      // ->orderBy('distance', 'asc')->get();
      // $order = DB::table('orders')->select('*')->where('order_status', 'On order' AND 'delivery_date', Carbon::today()->toDateString())->get();
      // $test = $order->delivery_date;
      //$order = DB:: table('orders')
      //->whereColumn([
       // ['order_status', 'On order']
     // ]) 
     
     dd($order);
      $start = 0;
      $stop = 5;
      $data = [];
      $break = false;
      for($i = 0; $i < 5; $i++){
        $z = 0;
        $tempData = [];
        if($break){
          break;
        }
        for($x = $start; $x < $stop; $x++){
          if($x < sizeof($order)){
            $z = $x;
            array_push($tempData, $order[$x]);
          }else{
            $break = true;
            // \Log::info($x);
            break;
          }
        }
        array_push($data, $tempData);
        $start = $z + 1;
        $stop = $stop + 5;
        \Log::info($start);
      }
      return response()->json($data);
  }

    public function updateCancelledStatus(Request $request, $id)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $request->id]);
      $post->order_status = 'Canceled';
      $post->update();
      return response()->json(compact('post'));
    }


    public function editOrder($id)
    {
      $post = Order::find($id);
      return response()->json($post);
    }


    public function updateOrder(Request $request)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $request->id]);
      $post->customer_name = $request['customer_name'];
      $post->customer_address = $request['customer_address'];
      $post->contact_number = $request['contact_number'];
      $post->delivery_date = $request['delivery_date'];
      $post->order_quantity = $request['order_quantity'];
      $post->save();
      return response()->json(compact('post'));
    }

    public function updateStatus(Request $request, $id)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $request->id]);
      $post->order_status = 'Delivered';
      $post->update();
      return response()->json(compact('post'));
    }
    
  
    public function deleteOrder($id)
    {
      $post = Order::find($id);
      $post->delete();
      return response()->json('successfully deleted');
    }
}
