<?php

namespace App\Http\Controllers;

use App\Notifications\LoginNeedVerification;
use Illuminate\Http\Request;
use App\Models\User;
class LoginController extends Controller
{
    //
    public function submit(Request $request)
    {
        //validate phone num
        $request->validate([
            'phone'=>'required|numeric|min:10'
        ]);

        //find or create a user model
        $user= User::firstOrCreate([
            'phone'=>$request->phone
        ]);

        if(!$user){
            return response()->json(['message'=>'Could not process a user'], 401);
        }

        //send the user a one-time use code
        $user->notify(new LoginNeedVerification());

        // return response
        return response()->json(['message'=> 'message sent']);

    }

        public function verify(Request $request){

            // validate the incomming request
            $request->validate([
                'phone'=>'required|numeric|min:10',
                'login_code'=>'required|numeric|between:111111,999999'

            ]);
            //find the user
            $user= User::where('phone',$request->phone)
            ->where('login_code', $request->login_code)
            -first();


            //is the code provided the same




            //if yes return back an auth token
            if($user){
                $user->update([
                    'login_code'=>null
                ]);
                return $user->createToken($request->login_code)->plainTextToken;


            }
            //if not retun message

            return response()->json(['message'=>'Invalid code']);
        }

}
