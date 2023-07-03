<?php

namespace App\Http\Controllers\API\ROVER;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helper\NotificationsHelper;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ErrorsController extends Controller
{
    public function index(Request $request){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->post('https://rover.rocketflood.com/api/admin-portal/errors', [
            "filters"=> $request->filters
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function application(Request $request, $id){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://rover.rocketflood.com/api/admin-portal/app/'.$id);

        $response = json_decode($data);
        $response->user = $request->user()->name;
        $response->users = User::orderBy('created_at', 'desc')->get(['name', 'id']);

        return response()->json($response, $data->status());
    }

    public function error(Request $request, $app_id, $carrier){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://rover.rocketflood.com/api/admin-portal/error/'.$app_id.'/'.$carrier);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function add_comment(Request $request){
        $comment = (object) $request->comment;
        $comment->user = $request->user()->name;

        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->post('https://rover.rocketflood.com/api/admin-portal/comment', [
            "comment"=> $comment
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function drop_comment(Request $request, $id){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->delete('https://rover.rocketflood.com/api/admin-portal/comment/'.$id);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function update_assigned(Request $request, $id){
        $user = (object) [
            "name"=> $request->user['name']
        ];

        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->put('https://rover.rocketflood.com/api/admin-portal/assigned/'.$id, [
            "user"=> $user,
            "changeBy"=> $request->user()->name
        ]);

        $response = json_decode($data);
        if($response->success){
            (new NotificationsHelper)->createNotification((object) [
                "user_id"=> $request->user['id'],
                "header"=> "ROVER Error Update",
                "body"=> "The ROVER error for ".$request->error['app'].' '.$request->error['carrier_name'].' has been assigned to you by '.$request->user()->name,
                "system"=> "rover",
                "type"=> "info"
            ]);
        }

        return response()->json($response, $data->status());
    }

    public function update_stage(Request $request, $id){
        $error = $request->error;

        $error['assigned'] = (object) [
            "name"=> $request->error['assigned']['name']
        ];
        
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->put('https://rover.rocketflood.com/api/admin-portal/stage/'.$id, [
            "error"=> $error,
            "changeBy"=> $request->user()->name
        ]);

        $response = json_decode($data);
        if($response->success){
            (new NotificationsHelper)->createNotification((object) [
                "user_id"=> $request->error['assigned']['id'],
                "header"=> "ROVER Error Update",
                "body"=> "A ROVER error's stage has been changed and assigned you by ".$request->user()->name." you can access the error at - https://admin.rocketflood.com/rover/error/".$response->app_id.'/'.$response->carrier,
                "system"=> "rover",
                "type"=> "info"
            ]);
        }

        return response()->json($response, $data->status());
    }

    public function log_test(Request $request, $id){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->post('https://rover.rocketflood.com/api/admin-portal/test/'.$id, [
            "test"=> $request->test,
            "logBy"=> $request->user()->name
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function fixed(Request $request, $id){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->post('https://rover.rocketflood.com/api/admin-portal/fixed/'.$id, [
            "fixedBy"=> $request->user()->name
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }
}
