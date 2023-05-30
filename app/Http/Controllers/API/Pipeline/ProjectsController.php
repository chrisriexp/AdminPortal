<?php

namespace App\Http\Controllers\API\Pipeline;

use App\Http\Controllers\Controller;
use App\Models\PipelineProjectPriority;
use App\Models\PipelineProjectProgress;
use App\Models\PipelineProjects;
use App\Models\PipelineProjectSections;
use App\Models\PipelineProjectTags;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\NotificationsHelper;
use App\Models\PipelineComments;
use App\Models\PipelineTasks;

use function PHPSTORM_META\map;

class ProjectsController extends Controller
{
    // Get User Projects
    public function index(Request $request){
        $allProjects = PipelineProjects::orderBy('created_at', 'asc')->get();
        $userProjects = [];

        foreach ($allProjects as $project){
            if(in_array($request->user()->id, json_decode($project->members))){
                $data = (object) [];

                $data->name = $project->name;
                $data->color = $project->color;
                $data->id = $project->id;

                array_push($userProjects, $data);
            }
        }
        
        $response = [
            'success'=> true,
            'projects'=> $userProjects
        ];

        return response()->json($response, 200);
    }
    
    // Create Project
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'color'=> 'required',
            'members'=> 'required'
        ]);

        if($validator->fails()){
            $response  = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $inputs = $request->all();
        $inputs['members'] = json_encode($inputs['members']);
        $inputs['id'] = uniqid('pipeline_project_');
        $inputs['owner'] = $request->user()->id;

        while(PipelineProjects::where('id', $inputs['id'])->exists()){
            $inputs['id'] = uniqid('pipeline_project_');
        }

        $project = PipelineProjects::create($inputs);
        $project->save();

        $deafult_priority = [
            [
                'name'=> 'High',
                'color'=> '#F42D2D'
            ],
            [
                'name'=> 'Medium',
                'color'=> '#F4812D'
            ],
            [
                'name'=> 'Normal',
                'color'=> '#2D94F4'
            ],
            [
                'name'=> 'Low',
                'color'=> '#2FA52D'
            ]
        ];

        $deafult_progress = [
            [
                'name'=> 'Not Started',
                'color'=> '#F42D2D'
            ],
            [
                'name'=> 'In Progress',
                'color'=> '#2D88A5'
            ],
            [
                'name'=> 'Done',
                'color'=> '#2FA52D'
            ]
        ];

        foreach ($deafult_priority as $item){
            $item['project_id'] = $inputs['id'];
            $item['id'] = uniqid('pipeline_priority_');
            while(PipelineProjectPriority::where('id', $item['id'])->exists()){
                $item['id'] = uniqid('pipeline_priority_');
            }

            $priority = PipelineProjectPriority::create($item);
            $priority->save();
        }

        foreach ($deafult_progress as $item){
            $item['project_id'] = $inputs['id'];
            $item['id'] = uniqid('pipeline_progress_');
            while(PipelineProjectProgress::where('id', $item['id'])->exists()){
                $item['id'] = uniqid('pipeline_progress_');
            }

            $progress = PipelineProjectProgress::create($item);
            $progress->save();
        }

        // Create Notification for all members that the Project has been created
        foreach($request->members as $user){
            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> $user,
                'header'=> 'New Project Created',
                'body'=> 'A new project has been created - "'.$project->name.'"',
                'type'=> 'success',
                'system'=> 'pipeline'
            ]);
        }

        $response = [
            'success'=> true,
            'message'=> 'Project created successfully.'
        ];
        
        return response()->json($response, 200);
    }

    // Create Project Section
    public function create_section(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'project_id'=> 'required'
        ]);

        if($validator->fails()){
            $response  = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $inputs = $request->all();
        $inputs['id'] = uniqid('pipeline_project_section_');
        while(PipelineProjectSections::where('id', $inputs['id'])->exists()){
            $inputs['id'] = uniqid('pipeline_project_section_');
        }

        $section = PipelineProjectSections::create($inputs);
        $section->save();

        $response = [
            'success'=> true,
            'message'=> 'Project Section created successfully.'
        ];
        
        return response()->json($response, 200);
    }

    // Get Project Details
    public function details(Request $request, $id){
        $project = PipelineProjects::find($id);
        $member_ids = json_decode($project->members);

        $sections = PipelineProjectSections::where('project_id', $id)->orderBy('created_at', 'desc')->get(['name', 'id']);
        $members = User::whereIn('id', $member_ids)->orderBy('created_at', 'asc')->get(['name', 'id']);
        $priority = PipelineProjectPriority::where('project_id', $id)->orderBy('created_at', 'desc')->get(['name', 'color', 'id']);
        $progress = PipelineProjectProgress::where('project_id', $id)->orderBy('created_at', 'desc')->get(['name', 'color', 'id']);
        $tags = PipelineProjectTags::where('project_id', $id)->orderBy('created_at', 'desc')->get(['name', 'color', 'id']);

        $response = [
            'success'=> true,
            'name'=> $project->name,
            'sections'=> $sections,
            'members'=> $members,
            'priority'=> $priority,
            'progress'=> $progress,
            'tags'=> $tags
        ];

        return response()->json($response, 200);
    }

    // Delete Project
    public function drop(Request $request, $id){
        $project = PipelineProjects::find($id);

        if($request->user()->role !== 'super-admin' && $request->user()->id !== $project->owner){
            $response = [
                'success'=> false,
                'message'=> 'Unathorized'
            ];
    
            return response()->json($response, 401);
        }

        // Delete all Project Tags
        $tags = PipelineProjectTags::where('project_id', $id)->get();
        foreach($tags as $tag){
            $tag->delete();
        }

        // Delete all Project Sections
        $sections = PipelineProjectSections::where('project_id', $id)->get();
        foreach($sections as $section){
            $section->delete();
        }

        // Delete all Project Progress
        $progress = PipelineProjectProgress::where('project_id', $id)->get();
        foreach($progress as $item){
            $item->delete();
        }

        // Delete all Project Priority
        $priority = PipelineProjectPriority::where('project_id', $id)->get();
        foreach($priority as $item){
            $item->delete();
        }

        // Delete all Project Tasks
        $tasks = PipelineTasks::where('project_id', $id)->get();
        foreach($tasks as $task){
            // Delete all task comments
            $comments = PipelineComments::where('task_id', $task->id)->get();
            foreach($comments as $comment){
                $comment->delete();
            }
            
            $task->delete();
        }

        // Notify all Members
        $members = json_decode($project->members);
        foreach($members as $member){
            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> $member,
                'header'=> 'Project Deleted',
                'body'=> 'The project "'.$project->name.'" has been deleted, all related information has also been deleted such as tasks, comments, and uploads by '.$request->user()->name,
                'type'=> 'error',
                'system'=> 'pipeline'
            ]);
        }

        $project->delete();

        $response = [
            'success'=> true,
            'message'=> 'Project deleted successfully.'
        ];

        return response()->json($response, 200);
    }
}
