<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
  public function authenticate(Request $request)
  {
    $credentials = $request->only('username', 'password');
    
    // dd($credentials);
    $info = "";
    try {
      if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['message' => 'invalid_credentials', 'status'=>400]);
      }
    } catch (JWTException $e) {
      return response()->json(['message' => 'could_not_create_token', 'status'=> 500]);
    }
    // return response()->json(['status'=>200, 'token'=>$token, 'message'=> 'successfully_login', 'username'=>$credentials['username']]);

    try{
      $info = User::select("role", "username", "id")
      ->where("username", "=", $request->get('username'))
      ->get();
    }catch(\Exception $e){
      return response()->json(['message' => 'invalid_credentials', 'status'=> 400]);
    }

    return response()->json(['status'=>200, 'token'=>$token, 'message'=> 'successfully_login', "UserAccount"=>$info]);
  }

  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'uName' => 'required',
      'phone' => 'required',
      'pass' => 'required',
      'role' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors()->toJson(), 400);
    }

    $user = User::create([
      'username' => $request->get('uName'),
      'phone' => $request->get('phone'),
      'role' => $request->get('role'),
      'password' => Hash::make($request->get('pass')),
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json(compact('user', 'token'), 201);
  }

  public function getAuthenticatedUser()
  {
    try {

      if (!$user = JWTAuth::parseToken()->authenticate()) {
        return response()->json(['user_not_found'], 404);
      }

    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

      return response()->json(['token_expired'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

      return response()->json(['token_invalid'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

      return response()->json(['token_absent'], $e->getStatusCode());

    }

    return response()->json(compact('user'));
  }

  public function AuthenticationGuard(){
    return response()->json(['status'=>"verified"]);
  }
}