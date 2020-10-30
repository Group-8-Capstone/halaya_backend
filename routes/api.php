<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\IngredientsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;

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

Route::group(['middleware' => ['jwt.verify']], function () {
    //Order Controller
    Route::post('/post/update', [OrderController::class, 'updateOrder']);
    Route::post('/post', [OrderController::class, 'createOrder']);

    Route::get('/posts/order', [OrderController::class, 'fetchOrder']);
    Route::get('/posts/delivered', [OrderController::class, 'fetchDelivered']);
    Route::get('/posts/delivery', [OrderController::class, 'fetchDelivery']);
    Route::get('/post/edit/{id}', [OrderController::class, 'editOrder']);

    Route::delete('/post/delete/{id}', [OrderController::class, 'deleteOrder']);

    Route::put('/post/updateStat/{id}', [OrderController::class, 'updateStatus']);
    Route::put('/post/updateCanceledStat/{id}', [OrderController::class, 'updateCancelledStatus']);

//Sales Controller
    Route::post('/sales/daily', [SalesController::class, 'index']);
    Route::post('/sales/weekly', [SalesController::class, 'indexWeekly']);
    Route::post('/sales/monthly', [SalesController::class, 'indexMonthly']);

    Route::get('/sales/filterYear', [SalesController::class, 'selectYear']);
    Route::get('/sales/yearly', [SalesController::class, 'indexYearly']);

//Ingredients Controller
    Route::post('/post/updateStock', [IngredientsController::class, 'updateStockIngredients']);
    Route::post('/post/addStockAmount', [IngredientsController::class, 'updateStockAmount']);
    Route::post('/post/usedIngredients', [IngredientsController::class, 'saveUsedIngredients']);
    Route::post('/fetch/updateStatus', [IngredientsController::class, 'updateStatus']);
    Route::post('/posts/ingredients', [IngredientsController::class, 'newIngredients']);
    Route::post('/post/saveRealNumbers', [IngredientsController::class, 'saveRealAmount']);
    Route::post('/post/neededValue', [IngredientsController::class, 'addEstimatedAmount']);
    Route::post('/post/updateNewEstimatedValue', [IngredientsController::class, 'updateEstimatedValue']);

    Route::get('/fetch/estimatedValue', [IngredientsController::class, 'fetchEstimatedValue']);
    Route::get('/post/updateEstimatedValue/{id}', [IngredientsController::class, 'editEstimatedValue']);
    Route::get('/post/editStock/{id}', [IngredientsController::class, 'editStockIngredients']);
    Route::get('/fetch/stock', [IngredientsController::class, 'fetchStock']);
    Route::get('/getHalayaIngredients', [IngredientsController::class, 'getHalayaIngredients']);
    Route::get('/getButchiIngredients', [IngredientsController::class, 'getButchiIngredients']);
    Route::get('/getIceCreamIngredients', [IngredientsController::class, 'getIceCreamIngredients']);
    Route::get('/fetch/ingredientsName', [IngredientsController::class, 'fetchIngredientsName']);


//for testing only
    Route::get('/fetch/checkStatus', [IngredientsController::class, 'checkStatus']);
    Route::get('/getMonthYear', [IngredientsController::class, 'compareDate']);
    Route::get('/getAllIngredients', [IngredientsController::class, 'getAllIngredients']);
    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::get('closed', [DataConroller::class, 'closed']);
});
