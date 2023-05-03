<?php

namespace App\Http\Controllers\API\Pipeline;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\PipelineComments;
use Illuminate\Http\Request;

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
            $notificationInputs = [];

            $notificationInputs['user_id'] = $user['id'];
            $notificationInputs['header'] = "Pipeline Task Comment";
            $notificationInputs['body'] = $request->user()->name." tagged you in a new comment for task '".$request->task_name."' in project - ".$request->project_name;
            $notificationInputs['type'] = "info";
            $notificationInputs['system'] = "pipeline";

            $notificationInputs['id'] = uniqid('notification_');
            while(Notifications::where('id', $notificationInputs['id'])->exists()){
                $notificationInputs['id'] = uniqid('notification_');
            }

            $notification = Notifications::create($notificationInputs);
            $notification->save();
        }

        $response = [
            'success'=> true,
            'comments'=> $comments
        ];
        
        return response()->json($response, 200);
    }
}
