<?php

namespace App\Http\Controllers;
use App\Http\Resources\OrderCollection;
use Carbon\Carbon;
use App\Events\OrderEvent;
use App\Events\StatusOnOrder;

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
        $data = $request->all();
        $post->customer_id = $data['customer_id'];
        $post->receiver_name = $data['receiver_name'];
        $post->building_or_street = $data['building_street'];
        $post->barangay = $data['barangay'];
        $post->city_or_municipality = $data['city_municipality'];
        $post->province = $data['province'];
        $post->contact_number = $data['contactNumber'];
        $post->ubehalayajar_qty = $data['jar_qty']; 
        $post->ubehalayatub_qty = $data['tub_qty']; 
        $post->total_payment = $data['total_payment'];
        $post->preferred_delivery_date = $data['deliveryDate'];
        $post->order_status = $data['orderStatus'];
        $post->mark_status = 'Unread';
        $post->mark_adminstatus = 'Unread';
        $post->distance = $data['distance'];
        $post->save();
        event(new OrderEvent($post));
        return 'success';
      } catch (\Exception $e){
        // event(new OrderEvent({'bolbol': 'ate jess'}));
        event(new OrderEvent('good'));
        return response()->json(['error'=>$e->getMessage()]);
      }
    }
  
   public function fetchOrder()
    {
      try {
        return new OrderCollection(Order::where('order_status', 'On order')
          ->orWhere('order_status', 'Canceled')
          ->orderBy('preferred_delivery_date', 'asc')
          ->get());
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }

    public function fetchPendingOrder()
    {
      try {
        return new OrderCollection(Order::where('order_status', 'Pending')
        ->orderBy('preferred_delivery_date', 'asc')
        ->get());
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
      
    }

    public function fetchDelivered()
    {
      try {
        return new OrderCollection(Order::where('order_status', 'Delivered')
        ->orderBy('preferred_delivery_date', 'desc')
        ->get());
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }

    public function totalTab(){
      try {
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
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
  }

  public function totalJar(){
    try {
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
    } catch (\Exception $e){
      return response()->json(['error'=>$e->getMessage()]);
    }
}

public function fetchDelivery(Request $request){
  try {
    $data = Order::where('preferred_delivery_date', Carbon::today()->toDateString())
    ->where( function($query) {
      $query->where('order_status', 'On order')
      ->orWhere('order_status', 'Canceled')
      ->orWhere('order_status', 'Delivered');
    })
    ->orderBy('distance', 'asc')
    ->get();
    return response()->json(compact('data'));
  } catch (\Exception $e){
    return response()->json(['error'=>$e->getMessage()]);
  }
}

    public function updateCancelledStatus(Request $request, $id)
    {
      try {
        $newItem =  $request->all();
        $post = Order::firstOrCreate(['id' => $request->id]);
        $post->order_status = 'Canceled';
        $post->save();
        return response()->json(compact('post'));
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
      
    }


    public function editOrder($id)
    {
      try {
        $post = Order::find($id);
        return response()->json($post);
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
    }


    public function updateOrder(Request $request)
    {
       try {
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
        $post->distance = $request['distance'];
        $post->save();
        return response()->json(compact('post'));
       } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
     
    }

    public function updateStatus(Request $request, $id)
    {
      try {
        $newItem =  $request->all();
        $post = Order::firstOrCreate(['id' => $id]);
        $post->order_status = 'Delivered';
        $post->save();
        event(new OrderEvent($post));
        return response()->json(compact('post'));
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
    }


    public function fetchOnOrderStat($id){
      try {
        $post = new OrderCollection(Order::where('order_status', 'On order')
        ->orWhere('order_status', 'Pending')
        ->where('customer_id','=', $id)
        ->orderBy('preferred_delivery_date')
        ->get());
        return response()->json(compact('post'));
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
    }
    public function fetchOngoingOrder($id){
      $post = new OrderCollection(Order::where('customer_id', $id)
      ->where(function($q) {
          $q->where('order_status', 'On order')
            ->orWhere('order_status', 'Pending');
      })
      ->orderBy('id', 'DESC')
      ->get());
      return response()->json(compact('post'));
    }

    public function fetchDeliveredOrder($id){
      try {
        $post = new OrderCollection(Order::where('order_status', 'Delivered')
        ->where('customer_id','=', $id)
        ->orderBy('preferred_delivery_date')
        ->get());
        return response()->json(compact('post'));
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
    }
    
  
    public function deleteOrder($id)
    {
      try {
        $post = Order::find($id);
        $post->delete();
      return response()->json('successfully deleted');
      } catch (\Exception $e){
        return response()->json(['error'=>$e->getMessage()]);
      }
    }



    public function updateConfirmStatus(Request $request, $id){
      try {
        $newItem =  $request->all();
        $post = Order::firstOrCreate(['id' => $id]);
        $post->order_status = 'On order';
       
        $post->save();
         event(new StatusOnOrder($post));
        return response()->json($post);
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }

    public function fetchProcessOrder()
    {
      try {
        return new OrderCollection(Order::where('order_status', 'On order')
          ->orWhere('order_status', 'Pending')
          ->orderBy('id', 'DESC')
          ->get());
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }

    public function unReadOrder($id){
      $post = new OrderCollection(Order::where('customer_id', $id)
      ->where('mark_status','Unread')
      ->where(function($q) {
          $q->where('order_status', 'On order')
            ->orWhere('order_status', 'Pending');
      })
      ->orderBy('preferred_delivery_date')
      ->get());
      return response()->json(compact('post'));
    }

    public function unreadAdminOrder()
    {
      $post = new OrderCollection(Order::where('mark_adminstatus', 'Unread')
      ->where(function($q) {
          $q->where('order_status', 'On order')
            ->orWhere('order_status', 'Pending');
      })
      ->orderBy('id', 'asc')
      ->get());
      return response()->json(compact('post'));
      }
    

    public function updateMarkStatus(Request $request, $id){
      try {
        $newItem =  $request->all();
        $post = Order::firstOrCreate(['id' => $id]);
        $post->mark_status = 'Read';
        $post->save();
        event(new OrderEvent($post));
        return response()->json($post);
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }

    public function updateadminStatus(Request $request, $id){
      try {
        $newItem =  $request->all();
        $post = Order::firstOrCreate(['id' => $id]);
        $post->mark_adminstatus = 'Read';
        $post->save();
        event(new OrderEvent($post));
        return response()->json($post);
      } catch (\Exception $e) {
        return response()->json(['error'=>$e->getMessage()]);
      }
    }

   
}
