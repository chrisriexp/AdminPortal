<?php

namespace App\Http\Controllers\API\Pipeline;

use App\Http\Controllers\Controller;
use App\Models\PipelineComments;
use App\Models\PipelineProjects;
use App\Models\PipelineProjectSections;
use App\Models\PipelineTasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helper\NotificationsHelper;

class TasksController extends Controller
{

    // Get All Tasks Assigned to You
    public function index_my_tasks(Request $request){
        $tasks = PipelineTasks::where('completed', $request->completed)->whereIn('project_id', $request->projects)->orderBy($request->sort, 'desc')->get(['id', 'project_id', 'name', 'assigned', 'due_date', 'priority', 'progress']);

        $data = [];

        foreach($request->projects as $project){
            $project_details = PipelineProjects::find($project);

            $project_data = (object)[
                "name"=> $project_details->name,
                "tasks"=> []
            ];

            foreach($tasks as $task){
                if($task->project_id == $project && !empty(json_decode($task->assigned)) && json_decode($task->assigned)->id == $request->user()->id){
                    $task->priority = json_decode($task->priority);
                    $task->progress = json_decode($task->progress);

                    array_push($project_data->tasks, $task);
                }
            }

            if(count($project_data->tasks) > 0){
                array_push($data, $project_data);
            }
        }

        $response = [
            'success'=> true,
            'message'=> 'Here are your tasks.',
            'tasks'=> $data
        ];
        
        return response()->json($response, 200);
    }

    // Get Project Sepecific Tasks
    public function index(Request $request, $id){
        $tasksNoSection = PipelineTasks::where('project_id', $id)->where('section_id', null)->where('completed', $request->completed)->orderBy($request->sort['code'], 'desc')->get();

        $json_fields = ['assigned', 'priority', 'progress', 'tags', 'desc'];
        $filters = [
            (object) [
                'name'=> 'assigned',
                'values'=> []
            ],
            (object) [
                'name'=> 'priority',
                'values'=> []
            ],
            (object) [
                'name'=> 'progress',
                'values'=> []
            ],
            (object) [
                'name'=> 'tags',
                'values'=> []
            ]
        ];

        $filterTasks = [];

        foreach($filters as $filter){
            foreach($request[$filter->name] as $item){
                array_push($filter->values, $item['id']);
            }
        }

        foreach ($tasksNoSection as $task){
            foreach($json_fields as $field){
                $task->$field = json_decode($task->$field);
            }

            $filterValid = true;

            foreach($filters as $filter){
                if($filter->name == 'tags'){}
                elseif(empty($task[$filter->name]) || in_array($task[$filter->name]->id, $filter->values)){}
                else{
                    $filterValid = false;
                }
            }

            if($filterValid){
                array_push($filterTasks, $task);
            }
        }

        $data = (object) [
            'tasks'=> $filterTasks,
            'sections'=> []
        ];

        foreach($request->sections as $section){
            $tasks = PipelineTasks::where('project_id', $id)->where('section_id', $section['id'])->where('completed', $request->completed)->orderBy($request->sort['code'], 'desc')->get();
            $filterSection = [];

            foreach ($tasks as $task){
                foreach($json_fields as $field){
                    $task->$field = json_decode($task->$field);
                }

                $sectionFilterValid = true;

                foreach($filters as $filter){
                    if($filter->name == 'tags'){}
                    elseif(empty($task[$filter->name]) || in_array($task[$filter->name]->id, $filter->values)){}
                    else{
                        $sectionFilterValid = false;
                    }
                }

                if($sectionFilterValid){
                    array_push($filterSection, $task);
                }
            }

            $sectionData = (object)[
                'name'=> $section['name'],
                'tasks' => $filterSection
            ];

            if(count($sectionData->tasks) > 0){
                array_push($data->sections, $sectionData);
            }
        }
        
        $response = [
            'success'=> true,
            'tasks'=> $data
        ];
        
        return response()->json($response, 200);
    }

    // Create New Task
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'project_id'=> 'required',
            'name'=> 'required'
        ]);

        if($validator->fails()){
            $response  = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $json_fields = ['assigned', 'priority', 'progress', 'tags', 'desc'];

        $inputs = $request->all();
        $inputs['id'] = uniqid('pipeline_tasks_');
        $inputs['created_by'] = $request->user()->name;

        foreach($json_fields as $field){
            $inputs[$field] = json_encode($inputs[$field]);
        }

        while(PipelineTasks::where('id', $inputs['id'])->exists()){
            $inputs['id'] = uniqid('pipeline_tasks_');
        }

        $task = new PipelineTasks($inputs);
        $task->save();

        $project = PipelineProjects::where('id', $task->project_id)->first();

        // Create Notification for Assigned User
        if(!empty(json_decode($task->assigned))){
            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> json_decode($task->assigned)->id,
                'header'=> "New Task Created",
                'body'=> 'New task "'.$task->name.'" was just created and assigned to you by '.$request->user()->name.'. This task is in project "'.$project->name.'".',
                'type'=> 'success',
                'system'=> 'pipeline'
            ]);
        }

        // Create Task Created Comment
        $comment  = new PipelineComments();
        $comment->id = uniqid('pipeline_comment_');
        while(PipelineComments::where('id', $comment->id)->exists()){
            $comment->id= uniqid('pipeline_comment_');
        }
        $comment->task_id = $task->id;
        $comment->user = "system";
        $comment->desc = '"<p>Task was created successfully. '. (!empty(json_decode($task->assigned)) ? 'Task assigned to '.json_decode($task->assigned)->name : '') .'</p>"';
        $comment->save();

        $response = [
            'success'=> true,
            'message'=> 'Project created successfully.'
        ];
        
        return response()->json($response, 200);
    }

    // Get Task Information
    public function get(Request $request, $id){
        $task = PipelineTasks::find($id);

        $json_fields = ['assigned', 'priority', 'progress', 'tags', 'desc'];
        foreach($json_fields as $field){
            $task->$field = json_decode($task->$field);
        }

        $comments = PipelineComments::where('task_id', $id)->orderBy('created_at', 'desc')->get();
        
        foreach($comments as $comment){
            $comment->desc = json_decode($comment->desc);
        }

        $response = [
            'success'=> true,
            'task'=> $task,
            'comments'=> $comments
        ];
        
        return response()->json($response, 200);
    }

    // Update Task
    public function update(Request $request, $id){
        $task = PipelineTasks::find($id);
        $json_fields = ['assigned', 'priority', 'progress', 'tags', 'desc'];

        $inputs = $request->all();

        foreach($json_fields as $field){
            $inputs[$field] = json_encode($inputs[$field]);
        }
        
        $task->fill($inputs);
        $task->save();

        foreach($json_fields as $field){
            $task[$field] = json_decode($task[$field]);
        }

        $response = [
            'success'=> true,
            'task'=> $task
        ];
        
        return response()->json($response, 200);
    }

    // Update Task Completion Status
    public function complete(Request $request, $id){
        $task = PipelineTasks::find($id);
        $json_fields = ['assigned', 'priority', 'progress', 'tags', 'desc'];

        $task->completed = !$task->completed;
        $task->save();

        // Notification to Assigned User
        if(!empty(json_decode($task->assigned))){
            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> json_decode($task->assigned)->id,
                'header'=> 'Task Completed',
                'body'=> 'Task "'.$task->name.'" was marked '. ($task->completed ? 'complete' : 'incomplete') .' by '.$request->user()->name,
                'type'=> 'success',
                'system'=> 'pipeline'
            ]);
        }

        // Create Task Completion Comment
        $comment  = new PipelineComments();
        $comment->id = uniqid('pipeline_comment_');
        while(PipelineComments::where('id', $comment->id)->exists()){
            $comment->id= uniqid('pipeline_comment_');
        }
        $comment->task_id = $task->id;
        $comment->user = "system";
        $comment->desc = '"<p>Task was marked '. ($task->completed ? 'complete' : 'incomplete') .' by '. $request->user()->name .'</p>"';
        $comment->save();

        foreach($json_fields as $field){
            $task[$field] = json_decode($task[$field]);
        }

        $response = [
            'success'=> true,
            'task'=> $task,
            'message'=> 'Task completion updated successfully.'
        ];
        
        return response()->json($response, 200);
    }

    // Delete Task
    public function drop(Request $request, $id){
        $task = PipelineTasks::find($id);

        // Notification to assigned User
        if(!empty(json_decode($task->assigned))){
            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> json_decode($task->assigned)->id,
                'header'=> 'Task Deleted',
                'body'=> 'Task "'.$task->name.'" was deleted by '.$request->user()->name,
                'type'=> 'error',
                'system'=> 'pipeline'
            ]);
        }

        $task->delete();

        $task_comments = PipelineComments::where('task_id', $id)->get();

        foreach($task_comments as $comment){
            $comment->delete();
        }

        $response = [
            'success'=> true,
            'message'=> "Task Deleted Succcessfully"
        ];
        
        return response()->json($response, 200);
    }
}
