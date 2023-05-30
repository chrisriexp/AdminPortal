<?php

namespace App\Http\Controllers\API;

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
}
