<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Helper\NotificationsHelper;

class ResetPasswordController extends Controller
{
    public function token(Request $request, $email){
        $user = User::where('email', $email)->first();

        $token = uniqid();
        while(User::where('remember_token', $token)->exists()){
            $token = uniqid();
        }
        
        if($user){
            $user->remember_token = $token;
            $user->save();


            $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                "service_id"=> "service_0vyim6r",
                "user_id"=> "h29zXRTKkaswfKPkp",
                "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                "template_id"=> "template_m212lub",
                "template_params"=> (object) [
                    "name"=> $user->name,
                    "email"=> $user->email,
                    "token"=> $token
                ]
            ]);

            $response = [
                'success'=> true,
                'message'=> "An email has been sent containing your reset token, please use the provided token to continue."
            ];

            return response()->json($response, 200);
        }else {
            $response = [
                'success'=> false,
                'message'=> "An account with with provided email does not exist. Please contact support to have an account created."
            ];

            return response()->json($response, 401);
        }
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "token"=> 'required',
            "password"=> 'required|min:8',
            "confirm_password"=> 'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        $user = User::where('remember_token', $request->token)->first();

        if($user){
            $user->remember_token = null;
            $user->password = bcrypt($request->password);
            $user->save();

            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> $user->id,
                'header'=> "Password Reset",
                'body'=> "The password for your account was reset successfully. If you did not request this please reach out to the support team.",
                'type'=> "success",
                'system'=> 'system'
            ]);
            
            $response = [
                'success'=> true,
                'message'=> "Password reset successfully."
            ];
            
            return response()->json($response, 200);

        } else {
            $response = [
                'success'=> false,
                'message'=> "Invalid token passed."
            ];
            
            return response()->json($response, 401);
        }
    }
}
