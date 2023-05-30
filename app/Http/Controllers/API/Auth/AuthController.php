<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\PipelineProjectPriority;
use App\Models\PipelineProjectProgress;
use App\Models\PipelineProjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Helper\NotificationsHelper;

class AuthController extends Controller
{
    // Get All Users
    public function users(){
        $users = User::orderBy('created_at', 'desc')->get();

        $response = [
            'success' => true,
            'users' => $users
        ];

        return response()->json($response, 200);
    }

    // Validate Token
    public function validateToken(Request $request){
        $id = $request->user()->id;

        $tokenExists = DB::table('personal_access_tokens')->where('tokenable_id', $id)->exists();

        if($tokenExists){
            $response = [
                'valid'=> true,
                'role'=> $request->user()->role,
                'react'=> $request->user()->react,
                'onboarding'=> $request->user()->onboarding,
                'message'=> 'Token authenticated successfully.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'valid'=> false,
                'message'=> 'Token authentication failed.'
            ];

            return response()->json($response, 401);
        }
    }

    // Login Request
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required|min:8'
        ]);

        if($validator->fails()) {
            $response = [
                'success'=>false,
                'message'=>$validator->errors()
            ];

            return response()->json($response, 400);
        }

        if(Auth::attempt(['email'=> strtolower($request->email), 'password'=> $request->password])){
            $user = Auth::user();
            
            $token = $user->createToken('token', [$user->role])->plainTextToken;

            $response = [
                'success'=> true,
                'role'=> $user->role,
                'token'=> $token,
                'message'=> 'User login successful.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success'=> false,
                'message'=> 'Incorrect username or password.'
            ];

            return response()->json($response, 401);
        }
    }

    // Register Request
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'email'=> 'required|email',
            'role'=> 'required',
            'password'=> 'required|min:8',
            'confirm_password'=> 'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $findUser = User::where('email', strtolower($request->email))->exists();

        if($findUser){
            $response = [
                'success'=> false,
                'message'=> 'User already exists.'
            ];

            return response()->json($response, 401);
        }

        $input = $request->all();
        $input['email'] = strtolower($input['email']);
        $input['password'] = bcrypt($input['password']);
        $input['id'] = uniqid('user_');

        while(User::where('id', $input['id'])->exists()){
            $input['id'] = uniqid('user_');
        }

        $user = User::create($input);
        $user->save();

        // Create User Personal Tasks Project
        $projectInputs = [];
        $projectInputs['owner'] = $input['id'];
        $projectInputs['name'] = $request->name."'s ".'Personal Tasks';
        $projectInputs['color'] = '#5080C7';
        $projectInputs['members'] = json_encode([$input['id']]);
        $projectInputs['id'] = uniqid('pipeline_project_');

        while(PipelineProjects::where('id', $projectInputs['id'])->exists()){
            $projectInputs['id'] = uniqid('pipeline_project_');
        }

        $project = PipelineProjects::create($projectInputs);
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
            $item['project_id'] = $projectInputs['id'];
            $item['id'] = uniqid('pipeline_priority_');
            while(PipelineProjectPriority::where('id', $item['id'])->exists()){
                $item['id'] = uniqid('pipeline_priority_');
            }

            $priority = PipelineProjectPriority::create($item);
            $priority->save();
        }

        foreach ($deafult_progress as $item){
            $item['project_id'] = $projectInputs['id'];
            $item['id'] = uniqid('pipeline_progress_');
            while(PipelineProjectProgress::where('id', $item['id'])->exists()){
                $item['id'] = uniqid('pipeline_progress_');
            }

            $progress = PipelineProjectProgress::create($item);
            $progress->save();
        }

        (new NotificationsHelper)->sendEmail((object) [
            "name"=> $user->name,
            "email"=> $user->email,
            "system"=> "system",
            "type"=> "success",
            "header"=> "New Account Created",
            "body"=> "Welcome to the Rocket Admin Portal ".$user->name.", your account has just been created. To login to the portal head to - https://admin.rocketflood.com/login, if you have forgotten or need to reset your password you can do so by going to - https://admin.rocketflood.com/reset-password"
        ]);

        $response = [
            'success'=> true,
            'message'=> 'User registered successfully.'
        ];

        return response()->json($response, 200);
    }

    // Reset Password Request
    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required|min:8',
            'confirm_password'=> 'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false, 
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        $user = User::where('email', $request->email)->first();

        if($user){
            $user->password = bcrypt($request->password);
            $user->save();

            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> $user->id,
                'header'=> "Password Reset",
                'body'=> "The password for your account was reset successfully. If you did not request this please reach out to the support team.",
                'type'=> "success",
                'system'=> 'system'
            ]);

            $response = [
                'success'=> true,
                'message'=> 'Password reset successfully.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success'=> false,
                'message'=> 'User does not exist.'
            ];

            return response()->json($response, 401);
        }
    }

    // Reset Email Request
    public function resetEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'confirm_email'=> 'required|same:email',
            'password'=> 'required|min:8',
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false, 
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        $user = User::where('email', $request->user()->email)->first();

        if(Auth('web')->attempt(['email'=> $request->user()->email, 'password'=> $request->password])){
            (new NotificationsHelper)->sendEmail((object) [
                'name'=> $user->name,
                'email'=> $user->email,
                'header'=> "Email Reset",
                'body'=> "The email for your account was reset successfully to ".$request->email.". If you did not request this please reach out to the support team.",
                'type'=> "info",
                'system'=> 'system'
            ]);

            $user->email = strtolower($request->email);
            $user->save();

            (new NotificationsHelper)->createNotification((object) [
                'user_id'=> $user->id,
                'header'=> "Email Reset",
                'body'=> "The email for your account was reset successfully. If you did not request this please reach out to the support team.",
                'type'=> "success",
                'system'=> 'system'
            ]);

            $response = [
                'success'=> true,
                'message'=> 'Email reset successfully.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success'=> false,
                'message'=> 'Password is incorrect.'
            ];

            return response()->json($response, 401);
        }
    }

    // Update User Information
    public function update(Request $request){
        if($request->has('password')){
            $validator = Validator::make($request->all(), [
                'id'=> 'required',
                'role'=> 'required',
                'name'=> 'required',
                'email'=> 'required|email',
                'password'=> 'required|min:8',
                'confirm_password'=> 'required|same:password'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'id'=> 'required',
                'role'=> 'required',
                'name'=> 'required',
                'email'=> 'required|email'
            ]);
        }

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $user = User::find($request->id);

        if($request->has('password')){
            $input = $request->all();
            $input['email'] = strtolower($input['email']);
            $input['password'] = bcrypt($input['password']);

            $user->fill($input);
            $user->save();
        } else {
            $input = $request->all();
            $input['email'] = strtolower($input['email']);

            $user->fill($input);
            $user->save();
        }

        $response = [
            'success'=> true,
            'message'=> 'User updated successfully.'
        ];

        return response()->json($response, 200);
    }

    // Logout Request
    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json(['success'=> true, 'message'=> 'User logged out successfully.'], 200);
    }
}
