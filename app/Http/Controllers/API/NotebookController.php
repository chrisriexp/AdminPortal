<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notebook;
use App\Models\NotebookTags;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotebookController extends Controller
{
    // Get Users Notebooks
    public function index(Request $request, $sort){
        $notebooks = Notebook::where('user', $request->user()->id)->orderBy($sort, 'desc')->get();
        $folders = json_decode($request->user()->notebook_folders);
        $tags = NotebookTags::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get(['id', 'name', 'color']);

        foreach($notebooks as $notebook){
            $notebook->shared = json_decode($notebook->shared);
            $notebook->tags = json_decode($notebook->tags);
        }

        $response = [
            'success'=> true,
            'notebook'=> $notebooks,
            'folders'=> $folders,
            'tags'=> $tags
        ];

        return response()->json($response, 200);
    }

    // Create new Notebook
    public function create(Request $request){
        $notebook = new Notebook();
        $notebook->user = $request->user()->id;
        $notebook->save();

        $response = [
            'success'=> true,
            'notebook'=> $notebook
        ];

        return response()->json($response, 200);
    }

    // Update Notebook
    public function update(Request $request, $id){
        $notebook = Notebook::find($id);
        if($request->has('tags')){
            $request['tags'] = json_encode($request->tags);
        }
        $notebook->fill($request->all());
        $notebook->updated_at = Carbon::now();
        $notebook->save();

        $notebook->tags = json_decode($notebook->tags);

        $response = [
            'success'=> true,
            'message'=> 'Notebook update successful.',
            'notebook'=> $notebook
        ];

        return response()->json($response, 200);
    }

    // Delete Notebook
    public function drop(Request $request, $id){
        $notebook = Notebook::find($id);
        $notebook->delete();

        $response = [
            'success'=> true,
            'message'=> 'Notebook deleted successfully.'
        ];

        return response()->json($response, 200);
    }

    // Get Folder Specific Notebooks
    public function folder(Request $request, $type, $sort){
        if($type == 'favorites'){
            $notebooks = Notebook::where('user', $request->user()->id)->where('favorite', true)->orderBy($sort, 'desc')->get();
        }
        elseif($type == 'shared'){
            $notebooks = Notebook::orderBy($sort, 'desc')->get();

            $sharedNotebooks = [];
            foreach($notebooks as $notebook){
                if($notebook->shared && in_array($request->user()->id, json_decode($notebook->shared))){
                    array_push($sharedNotebooks, $notebook);
                }
            }

            $notebooks = $sharedNotebooks;
        }
        elseif($type == 'notebooks'){
            $notebooks = Notebook::where('user', $request->user()->id)->orderBy($sort, 'desc')->get();
        }else{
            $notebooks = Notebook::where('user', $request->user()->id)->where('folder', $type)->orderBy($sort, 'desc')->get();
        }

        foreach($notebooks as $notebook){
            $notebook->shared = json_decode($notebook->shared);
            $notebook->tags = json_decode($notebook->tags);
        }

        $response = [
            'success'=> true,
            'notebooks'=> $notebooks
        ];

        return response()->json($response, 200);
    }

    // Create New Notebooks Folder
    public function new_folder(Request $request){
        $user = User::find($request->user()->id);

        $folders = json_decode($user->notebook_folders);

        if(empty($folders)){
            $folders = [$request->name];
        }else {
            array_push($folders, $request->name);
        }

        $user->notebook_folders = $folders;
        $user->save();

        $response = [
            'success'=> true,
            'message'=> 'New folder "'.$request->name.'" added to notebooks.',
            'folders'=> $folders
        ];

        return response()->json($response, 200);
    }

    // Personal Note
    public function personal_note(Request $request){
        $input = $request->all();
        $input['user'] = $request->user()->id;

        $notebook = Notebook::create($input);
        $notebook->save();

        $user = User::find($request->user()->id);
        $user->personal_notes = null;
        $user->save();

        $response = [
            'success'=>true,
            'message'=> 'Notebook saved successfully.'
        ];

        return response()->json($response, 200);
    }

    // Share Note
    public function share(Request $request, $id){
        $notebook = Notebook::find($id);

        $share_ids = [];

        foreach($request->users as $user){
            array_push($share_ids, $user['id']);
        }

        if($notebook->shared){
            $currentShared = json_decode($notebook->shared);
        }else {
            $currentShared = [];
        }

        $notebook->shared = json_encode($share_ids);
        $notebook->save();

        // Create Notification for Each User the Notebook was Shared with who isnt already shared with
        foreach($share_ids as $user){
            if(!in_array($user, $currentShared)){
                $notificationInputs = [];

                $notificationInputs['user_id'] = $user;
                $notificationInputs['header'] = "Notebook Shared";
                $notificationInputs['body'] = $request->user()->name." shared a notebook with you '".$notebook->title."'";
                $notificationInputs['type'] = "info";
                $notificationInputs['system'] = "notebooks";

                $notificationInputs['id'] = uniqid('notification_');
                while(Notifications::where('id', $notificationInputs['id'])->exists()){
                    $notificationInputs['id'] = uniqid('notification_');
                }

                $notification = Notifications::create($notificationInputs);
                $notification->save();
            }
        }

        $response = [
            'success'=>true,
            'message'=> 'Notebook share list updated successfully.'
        ];

        return response()->json($response, 200);
    }
}
