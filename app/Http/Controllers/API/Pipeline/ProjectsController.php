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
                'color'=> '#FF6361'
            ],
            [
                'name'=> 'Medium',
                'color'=> '#BC5090'
            ],
            [
                'name'=> 'Normal',
                'color'=> '#494CA2'
            ],
            [
                'name'=> 'Low',
                'color'=> '#D2D462'
            ]
        ];

        $deafult_progress = [
            [
                'name'=> 'Not Started',
                'color'=> '#FF6361'
            ],
            [
                'name'=> 'In Progress',
                'color'=> '#D2D462'
            ],
            [
                'name'=> 'Done',
                'color'=> '#6F975C'
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
}
