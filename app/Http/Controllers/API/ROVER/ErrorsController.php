<?php

namespace App\Http\Controllers\API\ROVER;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
}
