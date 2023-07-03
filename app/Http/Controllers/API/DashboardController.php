<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PipelineTasks;
use App\Models\quickTasks;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request){
        $onboardingData = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://onboarding.rocketmga.com/api/admin-portal/dashboard');
        $roverData = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://rover.rocketflood.com/api/admin-portal/dashboard');

        $pipelineTasks = PipelineTasks::where('completed', false)->orderBy('created_at', 'desc')->get(['id', 'project_id', 'name', 'due_date', 'assigned']);
        $assignedTasks = [];
        foreach($pipelineTasks as $assignedTask){
            $assignedTask->assigned = json_decode($assignedTask->assigned);
            if(!empty($assignedTask->assigned) && $assignedTask->assigned->id == $request->user()->id){
                array_push($assignedTasks, $assignedTask);
            }
        }

        $todayTasks = PipelineTasks::where('completed', false)->whereDate('due_date', Carbon::today())->get();
        $todayTaskCount = 0;
        foreach($todayTasks as $todayTask){
            if(!empty($todayTask->assigned) && json_decode($todayTask->assigned)->id == $request->user()->id){
                $todayTaskCount = $todayTaskCount + 1;
            }
        }

        $quickTasks = quickTasks::where('user', $request->user()->id)->whereDate('date', Carbon::today())->get();

        $response = [
            'success'=> true,
            'name'=> $request->user()->name,
            'data'=> [
                'note'=> $request->user()->personal_notes,
                'quickTasks'=> $quickTasks,
                'onboarding'=> json_decode($onboardingData),
                'rover'=> json_decode($roverData),
                'pipeline'=> [
                    'total'=> count($assignedTasks),
                    'dueToday'=> $todayTaskCount,
                    'recent'=> array_slice($assignedTasks, 0, 4)
                ]
            ]
        ];

        return response()->json($response, 200);
    }
}
