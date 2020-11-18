<?php

namespace App\Http\Controllers;
use App\Http\Resources\OrderCollection;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeleveredOrder;
use App\Models\DeliveryRangeQty;
use DB;

class OrderController extends Controller
{
  
    public function createOrder(Request $request)
    {
      try{
        $post = new Order;
        $data=$request->all();
        $post->customer_id = $data['customer_id'];
        $post->receiver_name = $data['receiver_name'];
        $post->building_or_street = $data['building_street'];
        $post->barangay = $data['barangay'];
        $post->city_or_municipality = $data['city_municipality'];
        $post->province = $data['province'];
        $post->contact_number = $data['contactNumber'];
        $post->ubehalayajar_qty = $data['jar_qty']; 
        $post->ubehalayatub_qty = $data['tub_qty']; 
        $post->preferred_delivery_date = $data['deliveryDate'];
        $post->order_status = $data['orderStatus'];
        $post->distance = $data['distance'];
        $post->save();
        return 'success';
      } catch (\Exception $e){
        return response()->json(['error'=>$e]);
      }
    }
  
   public function fetchOrder()
    {
      return new OrderCollection(Order::where('order_status', 'On order')
      ->orWhere('order_status', 'Canceled')
      ->orderBy('preferred_delivery_date', 'asc')
      ->get());
    }

    public function fetchPendingOrder()
    {
      return new OrderCollection(Order::where('order_status', 'Pending')
      ->orderBy('preferred_delivery_date', 'asc')
      ->get());
    }

    public function fetchDelivered()
    {
      return new OrderCollection(Order::where('order_status', 'Delivered')
      ->orderBy('preferred_delivery_date', 'desc')
      ->get());
    }

    public function totalTab(){
      $date = Carbon::today();
      $data = DB::table('orders')
      ->where('preferred_delivery_date',$date)
      ->where('order_status', 'On order' )
      ->get();
      
      $i = 0;
      $total = 0;
      foreach($data as $item){
          $total += $item->ubehalayatub_qty;
          $i++;
      }
      return $total;
  }

  public function totalJar(){
    $date = Carbon::today();
    $data = DB::table('orders')
    ->where('preferred_delivery_date',$date)
    ->where('order_status', 'On order' )
    ->get();
    $i = 0;
    $total = 0;
    foreach($data as $item){
        $total += $item->ubehalayajar_qty;
        $i++;
    }
    return $total;
  
}

public function fetchDelivery(Request $request){
  $data = Order::where('preferred_delivery_date', Carbon::today()->toDateString())
  ->where( function($query) {
    $query->where('order_status', 'On order')
    ->orWhere('order_status', 'Canceled')
    ->orWhere('order_status', 'Delivered');
  })
  ->orderBy('distance', 'asc')
  ->get();
  return response()->json(compact('data'));
}

    public function updateCancelledStatus(Request $request, $id)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $request->id]);
      $post->order_status = 'Canceled';
      $post->save();
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
      $post->receiver_name = $request['receiver_name'];
      $post->building_or_street = $request['building_or_street'];
      $post->barangay = $request['barangay'];
      $post->city_or_municipality = $request['city_or_municipality'];
      $post->province = $request['province'];
      $post->preferred_delivery_date = $request['preferred_delivery_date'];
      $post->ubehalayajar_qty = $request['ubehalayajar_qty'];
      $post->ubehalayatub_qty = $request['ubehalayatub_qty'];
      $post->save();
      return response()->json(compact('post'));
    }

    public function updateStatus(Request $request, $id)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $id]);
      $post->order_status = 'Delivered';
      $post->save();
      return response()->json(compact('post'));
    }

    public function fetchOnOrderStat($id){
      $post = new OrderCollection(Order::where('order_status', 'On order')
      ->orWhere('order_status', 'Pending')
      ->where('customer_id','=', $id)
      ->orderBy('preferred_delivery_date')
      ->get());
      return response()->json(compact('post'));
    }

    public function fetchDeliveredOrder($id){
      $post = new OrderCollection(Order::where('order_status', 'Delivered')
      ->where('customer_id','=', $id)
      ->orderBy('preferred_delivery_date')
      ->get());
      return response()->json(compact('post'));
    }
    
  
    public function deleteOrder($id)
    {
      $post = Order::find($id);
      $post->delete();
      return response()->json('successfully deleted');
    }

    public function updateConfirmStatus(Request $request, $id){
      try {
        $newItem =  $request->all();
        $post = Order::firstOrCreate(['id' => $id]);
        $post->order_status = 'On order';
        $post->save();
        return response()->json(compact('post'));
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }
}
