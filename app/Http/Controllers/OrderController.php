<?php

namespace App\Http\Controllers;
use App\Http\Resources\OrderCollection;

use Illuminate\Http\Request;
use App\Models\Order;
use DB;

class OrderController extends Controller
{

  
    public function createOrder(Request $request)
    {
        $post = new Order;
        $data=$request->all();
        $post->customer_name = $data['name'];
        $post->customer_address = $data['address'];
        $post->contact_number = $data['contactNumber'];
        $post->order_quantity = $data['orderQuantity']; 
        $post->delivery_date = $data['deliveryDate'];
        $post->order_status = $data['orderStatus'];
        $post->save();
        return response()->json([
            'message' => 'New post created'
        ]);
    }
  

   public function fetchOrder()
    {
      
      return new OrderCollection(Order::where('order_status', 'On order')
      ->orderBy('delivery_date', 'asc')->get());
    }

    public function fetchDelivered()
    {
      return new OrderCollection(Order::where('order_status', 'Delivered')->get());
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
