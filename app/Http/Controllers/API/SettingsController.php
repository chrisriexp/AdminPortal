<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notebook;
use App\Models\NotebookTags;
use App\Models\PipelineProjectPriority;
use App\Models\PipelineProjectProgress;
use App\Models\PipelineProjects;
use App\Models\PipelineProjectSections;
use App\Models\PipelineProjectTags;
use App\Models\PipelineTasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class SettingsController extends Controller
{
    public function pipeline(Request $request){
        if($request->has('update')){
            $project = PipelineProjects::find($request->update['id']);

            $project->fill($request->update);
            $project->save();

            // Save / Update Sections
            foreach($request->update['sectionOptions'] as $section){
                if($section['id']){
                    $existingSection = PipelineProjectSections::find($section['id']);
                    $existingSection->fill($section);
                    $existingSection->save();
                }else {
                    $section['id'] = uniqid('pipeline_project_section_');
                    while(PipelineProjectSections::where('id', $section['id'])->exists()){
                        $section['id'] = uniqid('pipeline_project_section_');
                    }

                    $newSection = PipelineProjectSections::create($section);
                    $newSection->save();
                }
            }

            // Save / Update Priority
            foreach($request->update['priorityOptions'] as $priority){
                if($priority['id']){
                    $existingPriority = PipelineProjectPriority::find($priority['id']);
                    $existingPriority->fill($priority);
                    $existingPriority->save();
                }else {
                    $priority['id'] = uniqid('pipeline_priority');
                    while(PipelineProjectPriority::where('id', $priority['id'])->exists()){
                        $priority['id'] = uniqid('pipeline_priority');
                    }

                    $newPriority = PipelineProjectPriority::create($priority);
                    $newPriority->save();
                }
            }

            // Save / Update Progress
            foreach($request->update['progressOptions'] as $progress){
                if($progress['id']){
                    $existingProgress = PipelineProjectProgress::find($progress['id']);
                    $existingProgress->fill($progress);
                    $existingProgress->save();
                }else {
                    $progress['id'] = uniqid('pipeline_progress');
                    while(PipelineProjectProgress::where('id', $progress['id'])->exists()){
                        $progress['id'] = uniqid('pipeline_progress');
                    }

                    $newProgress = PipelineProjectProgress::create($progress);
                    $newProgress->save();
                }
            }

            // Save / Update Tags
            foreach($request->update['tagOptions'] as $tag){
                if($tag['id']){
                    $existingTag = PipelineProjectTags::find($tag['id']);
                    $existingTag->fill($tag);
                    $existingTag->save();
                }else {
                    $tag['id'] = uniqid('pipeline_tag');
                    while(PipelineProjectTags::where('id', $tag['id'])->exists()){
                        $tag['id'] = uniqid('pipeline_tag');
                    }

                    $newTag = PipelineProjectTags::create($tag);
                    $newTag->save();
                }
            }

            $response = [
                'success'=> true,
                'message'=> 'Project updated successfully.'
            ];

            
            return response()->json($response, 200);
        }else{
            $data = PipelineProjects::where('owner', $request->user()->id)->orderBy('created_at', 'desc')->get();
            $users = User::orderBy('created_at', 'desc')->get(['name', 'id']);
            $projects = [];

            foreach($data as $project){
                // if(in_array($request->user()->id, json_decode($project->members))){
                //     $project->members = json_decode($project->members);                    
                //     array_push($projects, $project);
                // }

                $project->members = json_decode($project->members);                    
                array_push($projects, $project);
            }

            foreach($projects as $project){
                $sections = PipelineProjectSections::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();
                $priorites = PipelineProjectPriority::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();
                $progress = PipelineProjectProgress::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();
                $tags = PipelineProjectTags::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();

                $project->sectionOptions = $sections;
                $project->priorityOptions = $priorites;
                $project->progressOptions = $progress;
                $project->tagOptions = $tags;
            }

            $response = [
                'success'=> true,
                'user_id'=> $request->user()->id,
                'projects'=> $projects,
                'users'=> $users,
                'theme'=> $request->user()->theme
            ];

            return response()->json($response, 200);
        }
    }

    public function delete_pipeline_item(Request $request, $type, $id){
        if($type == 'sectionOptions'){
            $section = PipelineProjectSections::find($id);
            $section->delete();

            $tasks = PipelineTasks::where('section_id', $id)->get();

            foreach($tasks as $task){
                $task->section_id = null;
                $task->save();
            }

            $response = [
                'success'=> true,
                'message'=> 'Project section deleted successfully.'
            ];

            return response()->json($response, 200);
        }
        elseif($type == 'priorityOptions'){
            $tasks = PipelineTasks::get();

            foreach($tasks as $task){
                if(!empty(json_decode($task->priority)) && json_decode($task->priority)->id == $id){
                    $task->priority = [];
                    $task->save();
                }
            }

            $priority = PipelineProjectPriority::find($id);
            $priority->delete();

            $response = [
                'success'=> true,
                'message'=> 'Project priority deleted successfully.'
            ];

            return response()->json($response, 200);
        }
        elseif($type == 'progressOptions'){
            $tasks = PipelineTasks::get();

            foreach($tasks as $task){
                if(!empty(json_decode($task->progress)) && json_decode($task->progress)->id == $id){
                    $task->progress = [];
                    $task->save();
                }
            }

            $progress = PipelineProjectProgress::find($id);
            $progress->delete();

            $response = [
                'success'=> true,
                'message'=> 'Project priority deleted successfully.'
            ];

            return response()->json($response, 200);
        }
        elseif($type == 'tagOptions'){
            $tasks = PipelineTasks::get();

            foreach($tasks as $task){
                if(!empty(json_decode($task->tags))){
                    $tags = [];

                    foreach(json_decode($task->tags) as $tag){
                        if($tag->id != $id){
                            array_push($tags, $tag);
                        }
                    }

                    $task->tags = json_encode($tags);
                    $task->save();
                }
            }

            $tag = PipelineProjectTags::find($id);
            $tag->delete();

            $response = [
                'success'=> true,
                'message'=> 'Project tag deleted successfully.'
            ];

            return response()->json($response, 200);
        }
    }

    public function notebooks(Request $request){
        if($request->has('update')){
            $user = User::find($request->user()->id);
            $user->notebook_folders = json_encode($request->update['folders']);
            $user->save();

            foreach($request->update['tags'] as $tag){
                if($tag['id']){
                    $existingNotebookTag = NotebookTags::find($tag['id']);
                    $existingNotebookTag->fill($tag);
                    $existingNotebookTag->save();

                }else {
                    $tag['user_id'] = $request->user()->id;
                    $tag['id'] = uniqid('notebook_tags');
                    while(NotebookTags::where('id', $tag['id'])->exists()){
                        $tag['id'] = uniqid('notebook_tags');
                    }

                    $tag = NotebookTags::create($tag);
                    $tag->save();
                }
            }

        }else{
            $folders = json_decode($request->user()->notebook_folders);
            $tags = NotebookTags::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get();

            $response = [
                'success'=> true,
                'theme'=> $request->user()->theme,
                'folders'=> $folders,
                'tags'=> $tags
            ];

            return response()->json($response, 200);
        }
    }

    public function delete_notebook_folder(Request $request, $item){
        $newFolders = [];

        foreach(json_decode($request->user()->notebook_folders) as $folder){
            if($folder != $item){
                array_push($newFolders, $folder);
            }
        }

        $user = User::find($request->user()->id);
        $user->notebook_folders = json_encode($newFolders);
        $user->save();

        $notebooks = Notebook::where('user', $request->user()->id)->where('folder', $item)->get();

        foreach($notebooks as $notebook){
            $notebook->folder = 'notebooks';
            $notebook->save();
        }

        $response = [
            'success'=> true,
            'message'=> 'Notebook folder deleted successfully.'
        ];

        return response()->json($response, 200);
    }

    public function delete_notebook_tag(Request $request, $id){
        $notebooks = Notebook::where('user', $request->user()->id)->get();

        foreach($notebooks as $notebook){
            if(!empty(json_decode($notebook->tags))){
                $newTags = [];

                foreach(json_decode($notebook->tags) as $tag){
                    if($tag->id != $id){
                        array_push($newTags, $tag);
                    }
                }

                $notebook->tags = json_encode($newTags);
                $notebook->save();
            }
        }

        $tag = NotebookTags::find($id);
        $tag->delete();

        $response = [
            'success'=> true,
            'message'=> 'Notebook folder deleted successfully.'
        ];

        return response()->json($response, 200);
    }
}
