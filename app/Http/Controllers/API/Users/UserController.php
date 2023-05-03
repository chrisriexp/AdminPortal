<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function member_list(Request $request){
        $id = $request->user()->id;
        $list = User::orderBy('created_at', 'asc')->get();

        $users = [];

        foreach($list as $user){
            $data = [
                "name" => $user->name,
                "id"=> $user->id
            ];

            array_push($users, $data);
        }

        $response = [
            'success'=> true,
            'id'=> $id,
            'users'=> $users
        ];

        return response()->json($response, 200);
    }
}
