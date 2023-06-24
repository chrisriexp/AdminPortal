<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\NotebookController;
use App\Http\Controllers\API\NotificationsController;
use App\Http\Controllers\API\Onboarding\Agents;
use App\Http\Controllers\API\OpenAI\QuickToolsAIController;
use App\Http\Controllers\API\Pipeline\CommentsController;
use App\Http\Controllers\API\Pipeline\ProjectsController;
use App\Http\Controllers\API\Pipeline\TasksController;
use App\Http\Controllers\API\quickTaskController;
use App\Http\Controllers\API\REACT\CommissionStatementController;
use App\Http\Controllers\API\REACT\SubAgentController;
use App\Http\Controllers\API\Reports\OnboardingReports;
use App\Http\Controllers\API\Reports\REACTReports;
use App\Http\Controllers\API\Reports\ROVERReports;
use App\Http\Controllers\API\ROVER\ErrorsController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\Users\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Update User Theme
Route::middleware('auth:sanctum')->post('/theme/{theme}', function(Request $request, $theme){
    $user = User::find($request->user()->id);
    $user->theme = $theme;
    $user->save();

    return response()->json(['success'=> true, 'message'=> 'Theme update successful.'], 200);
});

// Get System Users
Route::middleware('auth:sanctum')->get('/member-list', [UserController::class, 'member_list']);
Route::middleware(['auth:sanctum', 'ability:super-admin'])->get('/user/{id}', [UserController::class, 'get_user']);
// Delete User
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->delete('/user/{id}', [UserController::class, 'drop']);

// Authentication Routes
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->get('/users', [AuthController::class, 'users']);
Route::middleware('auth:sanctum')->post('/token/validate', [AuthController::class, 'validateToken']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/resetEmail', [AuthController::class, 'resetEmail']);
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->post('/user/update', [AuthController::class, 'update']);

// Unauthenticated Password Reset
Route::get('/reset-password/token/{email}', [ResetPasswordController::class, 'token']);
Route::post('/reset-password', [ResetPasswordController::class, 'update']);

// Update Email Notifications
Route::middleware('auth:sanctum')->post('/update/email-notification', [UserController::class, 'email_notification']);

// Dashboard Routes
Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index']);

// Notebook Routes
Route::middleware('auth:sanctum')->get('/notebooks/{sort}', [NotebookController::class, 'index']);
Route::middleware('auth:sanctum')->get('/notebook/new', [NotebookController::class, 'create']);
Route::middleware('auth:sanctum')->put('/notebook/update/{id}', [NotebookController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/notebook/{id}', [NotebookController::class, 'drop']);
Route::middleware('auth:sanctum')->post('/notebook/personal-note', [NotebookController::class, 'personal_note']);
Route::middleware('auth:sanctum')->post('/notebook/{id}/share', [NotebookController::class, 'share']);
// Notebook Folders
Route::middleware('auth:sanctum')->get('/notebooks/folder/{type}/{sort}', [NotebookController::class, 'folder']);
Route::middleware('auth:sanctum')->post('/notebooks/folder/new', [NotebookController::class, 'new_folder']);

// Quick Task Routes
Route::middleware('auth:sanctum')->get('/quick-tasks/{filter}', [quickTaskController::class, 'index']);
Route::middleware('auth:sanctum')->post('/quick-task', [quickTaskController::class, 'create']);
Route::middleware('auth:sanctum')->delete('/quick-task/{id}', [quickTaskController::class, 'drop']);
Route::middleware('auth:sanctum')->put('/quick-task/{id}/{type}', [quickTaskController::class, 'update']);
Route::middleware('auth:sanctum')->put('/quick-task/personal-note', [quickTaskController::class, 'update_personal_note']);

// Pipeline Routes
// Projects
Route::middleware('auth:sanctum')->post('/pipeline/project', [ProjectsController::class, 'create']);
Route::middleware('auth:sanctum')->get('/pipeline/projects', [ProjectsController::class, 'index']);
Route::middleware('auth:sanctum')->get('/pipeline/project/{id}/details', [ProjectsController::class, 'details']);
Route::middleware('auth:sanctum')->delete('/pipeline/project/{id}', [ProjectsController::class, 'drop']);
// Sections
Route::middleware('auth:sanctum')->post('/pipeline/project/section', [ProjectsController::class, 'create_section']);
// Tasks
Route::middleware('auth:sanctum')->get('/pipeline/task/{id}', [TasksController::class, 'get']);
Route::middleware('auth:sanctum')->put('/pipeline/task/{id}', [TasksController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/pipeline/task/{id}', [TasksController::class, 'drop']);
Route::middleware('auth:sanctum')->get('/pipeline/task/{id}/complete', [TasksController::class, 'complete']);
Route::middleware('auth:sanctum')->post('/pipeline/project/task', [TasksController::class, 'create']);
Route::middleware('auth:sanctum')->post('/pipeline/my-tasks', [TasksController::class, 'index_my_tasks']);
Route::middleware('auth:sanctum')->post('/pipeline/project/{id}/tasks', [TasksController::class, 'index']);
// Task Comments
Route::middleware('auth:sanctum')->post('/pipeline/comment', [CommentsController::class, 'create']);
Route::middleware('auth:sanctum')->delete('/pipeline/comment/{id}', [CommentsController::class, 'drop']);

// Settings
Route::middleware('auth:sanctum')->post('/pipeline/settings', [SettingsController::class, 'pipeline']);
Route::middleware('auth:sanctum')->delete('/pipeline/settings/{type}/{id}', [SettingsController::class, 'delete_pipeline_item']);
Route::middleware('auth:sanctum')->post('/notebooks/settings', [SettingsController::class, 'notebooks']);
Route::middleware('auth:sanctum')->delete('/notebooks/settings/folder/{item}', [SettingsController::class, 'delete_notebook_folder']);
Route::middleware('auth:sanctum')->delete('/notebooks/settings/tags/{id}', [SettingsController::class, 'delete_notebook_tag']);

// Notifications Routes
Route::middleware('auth:sanctum')->get('/notifications', [NotificationsController::class, 'index']);
Route::post('/notification-needToken', [NotificationsController::class, 'create_fromToken']);

// REACT Controller
Route::middleware('auth:sanctum')->post('/react/sub-agents', [SubAgentController::class, 'index']);
Route::middleware('auth:sanctum')->post('/react/sub-agent', [SubAgentController::class, 'create']);
Route::middleware('auth:sanctum')->post('/react/sub-agent/upload', [SubAgentController::class, 'bulkUpload']);
Route::middleware('auth:sanctum')->put('/react/sub-agent/{rocket_id}', [SubAgentController::class, 'update']);
Route::middleware('auth:sanctum')->get('/react/commission/check/{month}', [CommissionStatementController::class, 'check']);
Route::middleware('auth:sanctum')->post('/react/commission/upload/{month}', [CommissionStatementController::class, 'upload']);
Route::middleware('auth:sanctum')->get('/react/sub-agent/report/{rocket_id}', [SubAgentController::class, 'report']);
Route::middleware('auth:sanctum')->get('/react/reports', [REACTReports::class, 'index']);
Route::middleware('auth:sanctum')->get('/react/commissions/{month}', [CommissionStatementController::class, 'month_report']);
Route::middleware('auth:sanctum')->get('/react/reports', [REACTReports::class, 'index']);

// Onboarding Controller
Route::middleware('auth:sanctum')->post('/onboarding', [Agents::class, 'index']);
Route::middleware('auth:sanctum')->post('/onboarding/marketing', [Agents::class, 'marketing']);
Route::middleware('auth:sanctum')->post('/onboarding/marketing/followed-up/{rocket_id}', [Agents::class, 'followed_up']);
Route::middleware('auth:sanctum')->get('/onboarding/reports', [OnboardingReports::class, 'index']);
Route::middleware('auth:sanctum')->get('/onboarding/agency/{rocket_id}/{category}', [Agents::class, 'agency']);
Route::middleware('auth:sanctum')->put('/onboarding/agency/{rocket_id}/{category}', [Agents::class, 'admin_update']);
Route::middleware('auth:sanctum')->get('/onboarding/approve/{rocket_id}', [Agents::class, 'approve']);
Route::middleware('auth:sanctum')->get('/onboarding/finalize/{rocket_id}/{force}', [Agents::class, 'finalize']);

// ROVER Controller
Route::middleware('auth:sanctum')->post('rover/errors', [ErrorsController::class, 'index']);
Route::middleware('auth:sanctum')->get('rover/app/{id}', [ErrorsController::class, 'application']);
Route::middleware('auth:sanctum')->get('rover/error/{app_id}/{carrier}', [ErrorsController::class, 'error']);
Route::middleware('auth:sanctum')->put('rover/stage/{id}', [ErrorsController::class, 'update_stage']);
Route::middleware('auth:sanctum')->post('rover/test/{id}', [ErrorsController::class, 'log_test']);
Route::middleware('auth:sanctum')->post('rover/fixed/{id}', [ErrorsController::class, 'fixed']);
Route::middleware('auth:sanctum')->post('rover/comment', [ErrorsController::class, 'add_comment']);
Route::middleware('auth:sanctum')->delete('rover/comment/{id}', [ErrorsController::class, 'drop_comment']);
Route::middleware('auth:sanctum')->put('rover/assigned/{id}', [ErrorsController::class, 'update_assigned']);
Route::middleware('auth:sanctum')->get('/rover/reports', [ROVERReports::class, 'index']);

// Commission Statements
Route::middleware(['auth:sanctum', 'ability:admin,super-admin,react'])->get('/commission/download/month/{month}/{id?}', [CommissionStatementController::class, 'download_month']);

// OpenAI Routes
Route::middleware('auth:sanctum')->post('/open-ai/quick-tools', [QuickToolsAIController::class, 'prompt']);