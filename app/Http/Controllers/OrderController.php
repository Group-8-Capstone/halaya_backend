<?php

namespace App\Http\Controllers;
// use App\Http\Resources\DeliveryCollection;
use App\Http\Resources\OrderCollection;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeleveredOrder;
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
        $post->ubeHalayaJar_qty = $data['jar_qty']; 
        $post->ubeHalayaTub_qty = $data['tub_qty']; 
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
          $total += $item->ubeHalayaTub_qty;
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
        $total += $item->ubeHalayaJar_qty;
        $i++;
    }
    return $total;
  
  }

    public function fetchDelivery(Request $request){
      return new OrderCollection(Order::where('order_status', 'On order')
      ->where('preferred_delivery_date', Carbon::today()->toDateString())
      ->orderBy('distance', 'asc')
      ->get());
      
      // ->where('preferred_delivery_date', '2020-10-27')->get());
      // ->orderBy('delivery_date', 'asc')->get());      
      // ->orderBy('delivery_date', 'asc')->get());
      // dd(Carbon::today()->toDateString());
      // $order = Order::where('order_status', 'On order' AND 'delivery_date', Carbon::today()->toDateString())
      // ->orderBy('distance', 'asc')->get();
      // $order = DB::table('orders')->select('*')->where('order_status', 'On order' AND 'delivery_date', Carbon::today()->toDateString())->get();
      // $test = $order->delivery_date;
      //$order = DB:: table('orders'h)
      //->whereColumn([
       // ['order_status', 'On order']
     // ]) 
     
    //  dd($order);
    //   $start = 0;
    //   $stop = 5;
    //   $data = [];
    //   $break = false;
    //   for($i = 0; $i < 5; $i++){
    //     $z = 0;
    //     $tempData = [];
    //     if($break){
    //       break;
    //     }
    //     for($x = $start; $x < $stop; $x++){
    //       if($x < sizeof($order)){
    //         $z = $x;
    //         array_push($tempData, $order[$x]);
    //       }else{
    //         $break = true;
    //         // \Log::info($x);
    //         break;
    //       }
    //     }
    //     array_push($data, $tempData);
    //     $start = $z + 1;
    //     $stop = $stop + 5;
    //     \Log::info($start);
    //   }
      return response()->json($data);
  }

  public function toDeliver(){
    $post = Order::where('order_status', 'On order')
      ->where('preferred_delivery_date', Carbon::today()->toDateString())
      ->orderBy('distance', 'asc')
      ->get();
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
      // $post->customer_address = $request['customer_address'];
      // $post->contact_number = $request['contact_number'];
      $post->building_or_street = $request['building_or_street'];
      $post->barangay = $request['barangay'];
      $post->city_or_municipality = $request['city_or_municipality'];
      $post->province = $request['province'];
      $post->preferred_delivery_date = $request['preferred_delivery_date'];
      $post->ubeHalayaJar_qty = $request['ubeHalayaJar_qty'];
      $post->ubeHalayaTub_qty = $request['ubeHalayaTub_qty'];
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

    public function saveDeliveredOrder(Request $request, $id){
      try{
        $test = DB::table('delivered_order')
            ->select('*')
            ->where('order_id', '=', $id)
            ->get();
        
        if(sizeof($test) == 0){
          $post = new Order;
          $data=$request->all();
          $post->order_id = $data['order_id'];
          $post->customer_name = $data['name'];
          $post->delivery_address = $data['address'];
          $post->halayaJar_qty = $data['halaya_qty']; 
          $post->ubechi_qty = $data['ubechi_qty']; 
          $post->delivery_date = $data['deliveryDate'];
          $post->order_status = $data['orderStatus'];
          $post->distance = $data['distance'];
          $post->save();
          DB::disconnect('wawenshalaya');
          return 'success';
        }else {
          return 'already exist';
        }
      } catch (\Exception $e){
        return "failed";
        return response()->json(['error'=>$e]);
      }
    }

    public function updateConfirmStatus(Request $request, $id){
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $id]);
      $post->order_status = 'On order';
      $post->save();
      return response()->json(compact('post'));
    }
}
