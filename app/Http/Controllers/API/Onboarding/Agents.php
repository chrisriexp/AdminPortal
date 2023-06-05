<?php

namespace App\Http\Controllers\API\Onboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Agents extends Controller
{
    public function index(Request $request){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->post('https://onboarding.rocketmga.com/api/admin-portal/agents', [
            "filters"=> $request->filters
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function agency(Request $request, $rocket_id, $category){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://onboarding.rocketmga.com/api/admin-portal/agency/'.$rocket_id.'/'.$category,);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function admin_update(Request $request, $rocket_id, $category){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->put('https://onboarding.rocketmga.com/api/admin-portal/agency/'.$rocket_id.'/'.$category, [
            "agency"=> $request->agency
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }
}
