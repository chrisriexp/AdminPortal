<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ROVERReports extends Controller
{
    public function index(Request $request){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://rover.rocketflood.com/api/admin-portal/reports');

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }
}
