<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\ForgotPassword;
use App\SmsService\NexmoSmsGateway;

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
  public function forgotPassword(Request $request){
    $response = [];
    try{
      $forgot = User::select("id","phone")
      ->where("phone","=", $request->get('contacts'))
      ->get();

      $id = $forgot[0]["id"];
      $phone =$forgot[0]["phone"];
      $code = $this->generateCode();

      // Store phone,id,code into ForgotPassword table
      $store = new ForgotPassword;
      $store->account_id = $id;
      $store->phone = $phone;
      $store->code = $code;
      $store->is_Valid = true;
      $store->save();
      
      // send $code to sms
      // $objSMS = new \SmsGateway(new \NexmoSmsGateway());
      // $response = $objSMS->sendSms('+639079077210', $code);
      $to = substr_replace($phone, "+63", 0, 1);
      // return response($to);
      $send = new NexmoSmsGateway();
      $send->send($to, $code);

      $response = $store;
    }catch(\Exception $e){
      return response()->json(['message' => 'server_error', 'status'=>500, 'data'=> $response]);
      
    }
   return response()->json(['message' => 'inserted successfully ', 'status'=>200, 'data'=> $response]);
  }
  public function generateCode(){
    $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 7);
    $codeExist = ForgotPassword::where('code', '=', $code)->get();
    if(sizeof($codeExist) > 0){
      $this->generateCode();
    }else{
      return $code;
    }
  }
  public function phoneCode(Request $request){
    try{
      $code= $request->get('code');
      $getCode = ForgotPassword::where([
        ['code','=',$code],
        ['is_Valid', '=', 1]
      ])->get();
      if(sizeof($getCode) > 0){
        return response()->json(['message'=> 'valid_code']);
      }else{
        return response()->json(['message'=> 'invalid_code']);
      }
    }catch(\Exception $e){
      return response()->json(['message' => 'server_error', 'status'=>500, 'data'=> $e]);
    }
  }
  public function newPassword(Request $request){
   try{
    $passCode = $request->get('code');
    $newPassword = $request->get('pass');
    $ForgotPasswordData = ForgotPassword::where("code","=",$passCode)->get(); // [{account_id, code, phone, is_Valid}]
    $accountID = $ForgotPasswordData[0]["account_id"];

    $UserData = User::where('id','=',$accountID)
      ->update(array('password'=>Hash::make($newPassword)));

    ForgotPassword::where("account_id", "=", $accountID)
      ->update(array('is_Valid'=> 0));
    
    return response()->json(['message' => 'successfully_password_changed', 'status'=>200, 'data'=> $UserData]);
   }catch(\Exeption $e){
    return response()->json(['message' => 'invalid_password', 'status'=>500, 'data'=> $e]);
   }

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
    try{
      $user = User::create([
        'username' => $request->get('uName'),
        'phone' => $request->get('phone'),
        'role' => $request->get('role'),
        'password' => Hash::make($request->get('pass')),
      ]);
    }catch(\PDOException $e){
      if($e->errorInfo[1] == 1062){
        return response()->json(["message"=>"invalid_username", "status"=>"409"]);
      }
    }

    $token = JWTAuth::fromUser($user);
    $message = [];
    $message['message'] = 'success';

    return response()->json(compact('user', 'token', 'message'), 200);
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