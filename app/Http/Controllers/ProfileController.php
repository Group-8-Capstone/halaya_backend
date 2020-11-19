<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProfileCollection;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function addProfile(Request $request){
        if (Profile::where('id', '=', '1')->exists()) {
        $imageName = time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'), $imageName);
        $data = $request->all();
        $account = Profile::firstOrCreate(['id' => $request->id]);
        $account->owners_name = $request['ownersName'];
        $account->avatar = 'images/'.$imageName;
        $account->save();
        $this->getAllProduct($data['account']);
         }
         else{
       
            $validator = Validator::make($request->all(), [
                'ownersName' => 'required|string|max:255',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            try { 
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $data = $request->all();
                $account = new Profile();
                $account->avatar = 'images/'.$imageName;
                $account->owners_name = $data['ownersName'];
                $account->save();
                $this->getAllProduct($data['ownersName']);
                
            } catch (\Exception $e){
                return response()->json(['error'=>$e->getMessage()]);
              }
         }
    }

    public function ProfilePicUpdate(Request $request,$id){
        $validator = Validator::make($request->all(), [
       
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        try { 
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
        
            $post = User::firstOrCreate(['id' => $id]);
            $post->profile_url = 'images/'.$imageName;
            $post->save();
        } catch (\Exception $e){
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function passwordUpdate(Request $request, $id){
        try { 
            $data = $request->all();
            $post = User::firstOrCreate(['id' => $id]);
            $post->password = Hash::make($data['confirmPassword']);
            $post->save();
        } catch (\Exception $e){
            return response()->json(['error'=>$e->getMessage()]);
          }

    }


    public function fetchProfile($id)
    {
        try {
            $account=User::select('id','username','profile_url')->where('id', $id)->get();
            return response()->json(compact('account'));
        } catch (\Exception $e){
            return response()->json(['error'=>$e->getMessage()]);
          }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
