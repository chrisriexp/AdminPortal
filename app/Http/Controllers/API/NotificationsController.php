<?php

namespace App\Http\Controllers\API;

use App\Helper\NotificationsHelper;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $request){
        $systems = ['notebooks', 'pipeline', 'system', 'react'];

        $notifications = (object) [];

        foreach($systems as $system){
            $data = Notifications::where('user_id', $request->user()->id)->where('system', $system)->where('read', false)->orderBy('created_at', 'desc')->get();
            $notifications->$system = $data;
        }
        
        $response = [
            'success'=> true,
            'notifications'=> $notifications
        ];

        return response()->json($response, 200);
    }

    public function create_fromToken(Request $request){
        if($request->header('token') !== "27b00fca-4d9e-4e28-85ce-54f16af26c0b"){
            $response = [
                'success'=> false,
                'message'=> 'Unathorized'
            ];
    
            return response()->json($response, 401);
        }

        if($request->type == 'onboarding'){
            (new NotificationsHelper)->createOnboardingNotification($request->notification);
        }

        return response()->json(["success"=> true], 200);
    }
}
