<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

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
    public function storeProfile(Request $request)
    {
        if($request->get('image'))
        {
           $image = $request->get('image');
           $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           \Image::make($request->get('image'))->save(public_path('images/').$name);
         }
 
        $image= new FileUpload();
        $image->image_name = $name;
        $image->save();
 
        return response()->json(['success' => 'You have successfully uploaded an image'], 200);
        // $this->validate($request, [
        //     'username' => 'required',
        //     'avatar' => 'required'
        // ]);

        // if($request->avatar){

        //     $name = time().'.' . explode('/', explode(':', substr($request->avatar, 0, strpos($request->avatar, ';')))[1])[1];
        //     \Image::make($request->avatar)->save(public_path('img/profile/').$name);
        //     $request->merge(['avatar' => $name]);
           
        // }
        
        // $user = new User;
        // $user->username = $request->username;
        // $user->avatar = $name;
        // $user->save();
        
        // return response()->json(
        //     [
        //         'success' => true,
        //         'message' => 'User registered successfully'
        //     ]
        // );

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
