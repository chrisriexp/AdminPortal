<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Models\Notebook;
use App\Models\NotebookTags;
use App\Models\Notifications;
use App\Models\PipelineProjects;
use App\Models\PipelineTasks;
use App\Models\quickTasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helper\NotificationsHelper;

class UserController extends Controller
{
    public function member_list(Request $request){
        $id = $request->user()->id;
        $users = User::orderBy('created_at', 'asc')->get(['name', 'id']);

        $response = [
            'success'=> true,
            'id'=> $id,
            'users'=> $users
        ];

        return response()->json($response, 200);
    }

    public function get_user(Request $request, $id){
        $user = User::get(['id', 'name', 'email', 'role', 'onboarding', 'rover', 'react', 'email_notifications'])->find($id);

        $response = [
            'success'=> true,
            'user'=> $user
        ];

        return response()->json($response, 200);
    }

    public function email_notification(Request $request){
        $user = User::find($request->user()->id);

        $user->email_notifications = $request->email_notifications;
        $user->save();

        $response = [
            'success'=> true,
            'message'=> 'Email notifications updated successfuly.'
        ];

        return response()->json($response, 200);
    }

    public function drop(Request $request, $id){
        $user = User::find($id);
        
        // Delete User Quick Tasks
        $quickTasks = quickTasks::where('user', $id)->get();
        foreach($quickTasks as $task){
            $task->delete();
        }

        // Delete User Notifications
        $notifications = Notifications::where('user_id', $id)->get();
        foreach($notifications as $notification){
            $notification->delete();
        }

        // Delete User Notebooks and Tags
        $notebooks = Notebook::where('user', $id);
        foreach($notebooks as $notebook){
            $notebook->delete();
        }
        $notebookTags = NotebookTags::where('user_id', $id)->get();
        foreach($notebookTags as $tag){
            $tag->delete();
        }

        // Clear Assigned Tasks
        $pipelineTasks = PipelineTasks::get();
        foreach($pipelineTasks as $task){
            $assigned = json_decode($task->assigned);

            // If task is assgined to the user change assigned to blank
            if(!empty($assigned) && $assigned->id == $id){
                $task->assigned = json_encode([]);
                $task->save();
            }
        }

        // Delete / Re-Assign User Projects
        $pipelineProjects = PipelineProjects::get();
        foreach($pipelineProjects as $project){
            // Projects where User is the Owner
            if($project->owner == $id){
                // Check to see if project has more than 1 member to reassign
                $members = json_decode($project->members);

                if(count($members) > 1){
                    $memberKey = array_search($id, $members);

                    // If the User ID is found in the members array
                    unset($members[$memberKey]);

                    // Set new members list excluding the User
                    $project->members = json_encode($members);
                    // Change Project Owner
                    $project->owner = $members[0];

                    // Save changes to the project
                    $project->save();

                    // Create Notification for New Project Owner
                    (new NotificationsHelper)->createNotification((object) [
                        "user_id"=> $members[0],
                        "header"=> $project->name." - Update",
                        "body"=> "The previous owner account for this project was deleted. You are now the new project owner.",
                        "type"=> "info",
                        "system"=> "pipeline"
                    ]);
                }else {
                    // Delete the Project if the only member is the Owner
                    $project->delete();
                }
            }else {
                $members = json_decode($project->members);
                // Check to see if the User is a member of any of the projects they dont own
                $memberKey = array_search($id, $members);
                if($memberKey !== false){
                    // If the user is a member then remove them from the array
                    unset($members[$memberKey]);

                    $project->members = json_encode($members);
                    $project->save();
                }
            }
        }

        // Send an email to the Deleted User
        (new NotificationsHelper)->sendEmail((object) [
            "name"=> $user->name,
            "email"=> $user->email,
            "type"=> "error",
            "system"=> "system",
            "header"=> "Account Deleted",
            "body"=> "Your user account has been deleted from the Rocket Admin Portal. If you did not request this or have further questions, please contact a Rocket support agent or admin."
        ]);

        $user->delete();

        $response = [
            'success'=> true,
            'message'=> "User deleted successfully."
        ];

        return response()->json($response, 200);
    }
}
