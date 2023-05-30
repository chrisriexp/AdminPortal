<?php

namespace App\Http\Controllers\API\Pipeline;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\PipelineComments;
use Illuminate\Http\Request;
use App\Helper\NotificationsHelper;

class CommentsController extends Controller
{
    public function create(Request $request){
        $inputs = $request->all();
        $inputs['id'] = uniqid('pipeline_comment_');
        while(PipelineComments::where('id', $inputs['id'])->exists()){
            $inputs['id'] = uniqid('pipeline_comment_');
        }
        $inputs['desc'] = json_encode($inputs['desc']);

        $comment = PipelineComments::create($inputs);
        $comment->save();

        $comments = PipelineComments::where('task_id', $request->task_id)->orderBy('created_at', 'desc')->get();
        foreach($comments as $comment){
            $comment->desc = json_decode($comment->desc);
        }

        // Crate Notifications for all Tagged Users
        foreach($request->tags as $user){
            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> $user['id'],
                'header'=> 'You were Tagged in a Task Comment',
                'body'=> $request->user()->name.' tagged you in a new comment for Task - "'.$request->task_name.'" in Project - "'.$request->project_name.'".',
                'type'=> 'info',
                'system'=> 'pipeline'
            ]);
        }

        $response = [
            'success'=> true,
            'comments'=> $comments
        ];
        
        return response()->json($response, 200);
    }

    public function drop(Request $request, $id){
        $comment = PipelineComments::find($id);
        $comment->delete();

        $response = [
            'success'=> true,
            'message'=> 'Comment Deleted Successfully.'
        ];
        
        return response()->json($response, 200);
    }
}
