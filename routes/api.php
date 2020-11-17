<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\IngredientsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ProfileController;


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




Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);
Route::get('open', [DataController::class, 'open']);

Route::post('/sales/daily', [SalesController::class, 'index']);

Route::group(['middleware' => ['jwt.verify']], function () {
    //Order Controller
    Route::post('/post/update', [OrderController::class, 'updateOrder']);
    Route::post('/post/createOrder', [OrderController::class, 'createOrder']);
    Route::get('/fetch/pending-orders', [OrderController::class, 'fetchPendingOrder']);

    Route::get('/posts/order', [OrderController::class, 'fetchOrder']);
    Route::get('/posts/delivered', [OrderController::class, 'fetchDelivered']);
    Route::get('/posts/delivery', [OrderController::class, 'fetchDelivery']);
    Route::get('/post/edit/{id}', [OrderController::class, 'editOrder']);
    // Route::post('/post/deliveredOrder/{id}', [OrderController::class, 'saveDeliveredOrder']);
    
    Route::post('/post/updateStat/{id}', [OrderController::class, 'updateStatus']);
    Route::delete('/post/delete/{id}', [OrderController::class, 'deleteOrder']);

    Route::post('/post/updateCanceledStat/{id}', [OrderController::class, 'updateCancelledStatus']);
    Route::get('/fetchOnOrder/{id}', [OrderController::class, 'fetchOnOrder']);
    Route::get('/fetchDeliveredOrder/{id}', [OrderController::class, 'fetchDeliveredOrder']);
  
    Route::post('/post/confirm/{id}', [OrderController::class, 'updateConfirmStatus']);
    Route::get('/totalTab', [OrderController::class, 'totalTab']);
    Route::get('/totalJar', [OrderController::class, 'totalJar']);

//Sales Controller
    // Route::post('/sales/daily', [SalesController::class, 'index']);
    Route::post('/sales/weekly', [SalesController::class, 'indexWeekly']);
    Route::post('/sales/monthly', [SalesController::class, 'indexMonthly']);

    Route::get('/sales/filterYear', [SalesController::class, 'selectYear']);
    Route::get('/sales/yearly', [SalesController::class, 'indexYearly']);

    //FOR TUB

    Route::post('/sales/dailyTubs', [SalesController::class, 'indexTub']);
    Route::post('/sales/weeklyTubs', [SalesController::class, 'indexWeeklyTub']);
    Route::post('/sales/monthlyTubs', [SalesController::class, 'indexMonthlyTub']);

    Route::get('/sales/yearlyTubs', [SalesController::class, 'indexYearlyTub']);

//Ingredients Controller
    Route::post('/post/updateStock', [IngredientsController::class, 'updateStockIngredients']);
    Route::post('/post/addStockAmount', [IngredientsController::class, 'updateStockAmount']);
    Route::post('/post/usedIngredients', [IngredientsController::class, 'saveUsedIngredients']);
    Route::post('/fetch/updateStatus', [IngredientsController::class, 'updateStatus']);
    Route::post('/posts/ingredients', [IngredientsController::class, 'newIngredients']);
    Route::post('/post/saveRealNumbers', [IngredientsController::class, 'saveRealAmount']);
    Route::post('/post/neededValue', [IngredientsController::class, 'addEstimatedAmount']);
    Route::post('/post/updateNewEstimatedValue', [IngredientsController::class, 'updateEstimatedValue']);
    Route::get('/fetchUsedIng', [IngredientsController::class, 'fetchUsedIng']);

    Route::get('/fetch/estimatedValue', [IngredientsController::class, 'fetchEstimatedValue']);
    Route::get('/post/updateEstimatedValue/{id}', [IngredientsController::class, 'editEstimatedValue']);
    Route::get('/post/editStock/{id}', [IngredientsController::class, 'editStockIngredients']);
    Route::get('/fetch/stock', [IngredientsController::class, 'fetchStock']);
    Route::get('/getHalayaIngredients', [IngredientsController::class, 'getHalayaIngredients']);
    // Route::get('/getButchiIngredients', [IngredientsController::class, 'getButchiIngredients']);
    // Route::get('/getIceCreamIngredients', [IngredientsController::class, 'getIceCreamIngredients']);
    Route::get('/fetch/ingredientsName', [IngredientsController::class, 'fetchIngredientsName']);
    Route::delete('/softDeleteIngredients/{id}', [IngredientsController::class, 'softDeleteIngredients']);
    Route::delete('/softDeleteStockIngredients/{id}', [IngredientsController::class, 'softDeleteStockIngredients']);



//for testing only
    Route::get('/fetch/checkStatus', [IngredientsController::class, 'checkStatus']);
    Route::get('/getMonthYear', [IngredientsController::class, 'compareDate']);
    Route::get('/getAllIngredients', [IngredientsController::class, 'getAllIngredients']);
    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::get('closed', [DataConroller::class, 'closed']);
    // Route::get('/posts/to-deliver', [OrderController::class, 'toDeliver']);
    // Route::get('/fetch/delivery-range', [OrderController::class, 'getRange']);
    

    //Product
    Route::post('/editTub/{id}',[ProductController::class,'editTub']);
    Route::post('/editJar/{id}',[ProductController::class,'editJar']);
    Route::get('/fetchRecordedProduct',[ProductController::class,'fetchRecordedProduct']);
    Route::get('/fetchHalayaTub',[ProductController::class,'fetchHalayaTub']);
    Route::get('/fetchHalayaJar',[ProductController::class,'fetchHalayaJar']);
    Route::post('/dailyRecords',[ProductController::class,'dailyRecords']);

    Route::post('/post/product',[ProductController::class,'addProduct']);
    Route::get('/fetch/product',[ProductController::class,'retrieveProduct']);
    Route::post('/post/updateProduct',[ProductController::class,'updateProduct']);
    Route::get('/fetch/postProduct',[ProductController::class,'postStockProduct']);
    Route::get('/fetch/allProduct',[ProductController::class,'getAllProduct']);
    Route::post('/post/updateStockProduct',[ProductController::class,'updateStockProduct']);
    Route::delete('/softDeleteProduct/{id}',[ProductController::class,'softDeleteProduct']);
    Route::delete('/softDeleteStockProduct/{id}',[ProductController::class,'softDeleteStockProducts']);

    //Profile

    Route::post('/post/account',[ProfileController::class,'addProfile']);
    Route::get('/retrieveAccount',[ProfileController::class,'fetchAccount']);
    Route::get('/fetchProfile/{id}',[ProfileController::class,'fetchProfile']);
    Route::post('/ProfilePicUpdate/{id}',[ProfileController::class,'ProfilePicUpdate']);
    Route::post('/passwordUpdate/{id}',[ProfileController::class,'passwordUpdate']);


    Route::post('/verify_auth',[UserController::class,'AuthenticationGuard']);

    
 
});

//test
Route::post('/sales/testing', [SalesController::class, 'test']);