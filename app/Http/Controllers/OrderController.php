<?php

namespace App\Http\Controllers;
use App\Http\Resources\OrderCollection;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $post = new Order;
        $data=$request->all();
        $post->customersName = $data['name'];
        $post->address = $data['address'];
        $post->contactNumber = $data['contactNumber'];
        $post->orderQuantity = $data['orderQuantity']; 
        $post->deliveryDate = $data['deliveryDate'];
        $post->orderStatus = $data['orderStatus'];
        $post->save();

        return response()->json([
            'message' => 'New post created'
        ]);
    }

   public function fetchOrder()
    {
      return new OrderCollection(Order::where('orderStatus', 'On order')->get());
    }

    public function fetchDelivered()
    {
      return new OrderCollection(Order::where('orderStatus', 'Delivered')->get());
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
      $post->customersName = $request['customersName'];
      $post->address = $request['address'];
      $post->contactNumber = $request['contactNumber'];
      $post->deliveryDate = $request['deliveryDate'];
      $post->orderQuantity = $request['orderQuantity'];
      $post->save();
      return response()->json(compact('post'));
    }

    public function updateStatus(Request $request, $id)
    {
      $newItem =  $request->all();
      $post = Order::firstOrCreate(['id' => $request->id]);
      $post->orderStatus = 'Delivered';

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
