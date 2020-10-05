<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\IngredientsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('/post',[OrderController::class,'createOrder']);
Route::get('/posts/order',[OrderController::class,'fetchOrder']);
Route::get('/posts/delivered',[OrderController::class,'fetchDelivered']);
Route::get('/post/edit/{id}',[OrderController::class,'editOrder']);
Route::post('/post/update',[OrderController::class,'updateOrder']);
Route::delete('/post/delete/{id}',[OrderController::class,'deleteOrder']);
Route::put('/post/updateStat/{id}',[OrderController::class,'updateStatus']);
Route::post('/create/stock/',[IngredientsController::class,'createStock']);
Route::get('/fetch/stock',[IngredientsController::class,'fetchStock']);
Route::get('/fetch/sumOrder',[ProductController::class,'orderSum']);
Route::get('/fetch/expectedProduct',[ProductController::class,'fetchExpectedProd']);
Route::get('/fetch/stockStatus',[ProductController::class,'stockStatus']);
Route::get('/post/editStock/{id}',[IngredientsController::class,'editStockIngredients']);
Route::get('/sales',[SalesController::class,'index']);
Route::post('/post/updateStock',[IngredientsController::class,'updateStockIngredients']);
Route::post('/post/fetchName',[IngredientsController::class,'fetchIngredientsName']);
Route::post('/post/addStockAmount',[IngredientsController::class,'updateStockAmount']);

