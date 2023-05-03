<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\quickTasks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class quickTaskController extends Controller
{
    public function index(Request $request, $filter){
        $tasks = quickTasks::where('user', $request->user()->id)->orderBy('important', 'desc')->orderBy('created_at', 'desc')->get();
        $today = Carbon::today()->toDateString();

        if($filter == 'today'){
            $todaysTasks = [];

            foreach ($tasks as $task){
                if($today == $task->date){
                    array_push($todaysTasks, $task);
                }
            }
            
            $tasks = $todaysTasks;
        }
        elseif($filter == 'late'){
            $lateTasks = [];

            foreach ($tasks as $task){
                if($today != $task->date){
                    array_push($lateTasks, $task);
                }
            }
            
            $tasks = $lateTasks;
        }

        $count = count($tasks);

        if($filter != 'today'){
            $data = [];
            $days = [];

            foreach ($tasks as $task){
                if(!in_array($task->date, $days)){
                    array_push($days, $task->date);
                }
            }

            rsort($days);

            foreach ($days as $day){
                $daysTasks = [];

                foreach ($tasks as $task){
                    if($day == $task->date){
                        array_push($daysTasks, $task);
                    }
                }

                $daysTaskObject = [
                    'day'=> $today == $day ? 'Today' : $day,
                    'tasks'=> $daysTasks
                ];

                array_push($data, $daysTaskObject);
            }

            $tasks = $data;
        }

        $note = $request->user()->personal_notes;
        
        $response = [
            'success'=> true,
            'count'=> $count,
            'tasks'=> $tasks,
            'note'=> $note
        ];

        return response()->json($response, 200);
    }

    public function create(Request $request){
        $task = new quickTasks();
        $task->user = $request->user()->id;
        $task->task = $request->task;
        $task->date = Carbon::today()->toDateString();
        $task->save();

        $response = [
            'success'=> true,
            'task'=> $task
        ];

        return response()->json($response, 200);
    }

    public function drop($id){
        $task = quickTasks::find($id);
        $task->delete();

        $response = [
            'success'=> true,
            'message'=> 'Task deleted successfully.'
        ];

        return response()->json($response, 200);
    }

    public function update($id, $type){
        $task = quickTasks::find($id);
        
        if($type == 'date'){
            $task->date = Carbon::today()->toDateString();
        }
        elseif($type == 'important'){
            $task->important = !$task->important;
        }

        $task->save();

        $response = [
            'success'=> true,
            'message'=> 'Task updated successfully.'
        ];

        return response()->json($response, 200);
    }

    public function update_personal_note(Request $request){
        $user = User::find($request->user()->id);
        $user->personal_notes = $request->note;
        $user->save();
        
        return response()->json(['success'=>true], 200);
    }
}
