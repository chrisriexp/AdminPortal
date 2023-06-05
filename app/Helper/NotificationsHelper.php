<?php

namespace App\Helper;

use App\Models\Notifications;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotificationsHelper
{
    public $templates = [
        "info"=> "template_hxd2ym7",
        "success"=> "template_x2jjbzl",
        "error"=> "template_4mno82r"
    ];

    public $systems = [
        "system"=> "System Notification",
        "notebooks"=> "Notebook Notification",
        "pipeline"=> "Pipeline Notification",
        "react"=> "REACT Notifications",
        "onboarding"=> "MGA Onboarding"
    ];

    public function createNotification($data){
        $user = User::find($data->user_id);

        // Create Notification in Database
        $notification = new Notifications();
        $notification->id = uniqid('notification_');
        while(Notifications::where('id', $notification->id)->exists()){
            $notification->id = uniqid('notification_');
        }

        $notification->fill((array) $data);
        $notification->save();

        // Check if user has email notifications on
        if($user->email_notifications){
            $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                "service_id"=> "service_0vyim6r",
                "user_id"=> "h29zXRTKkaswfKPkp",
                "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                "template_id"=> $this->templates[$data->type],
                "template_params"=> (object) [
                    "name"=> $user->name,
                    "email"=> $user->email,
                    "header"=> $data->header,
                    "body"=> $data->body,
                    "system"=> $this->systems[$data->system]
                ]
            ]);
        }

        $log_info = [
            "email_sent"=> $user->email_notifications,
            "user"=> $user->name,
            "email"=> $user->email,
            "data"=> $data
        ];

        Log::channel('notifications')->info(json_encode($log_info));

        return;
    }

    public function sendEmail($data){
        $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
            "service_id"=> "service_0vyim6r",
            "user_id"=> "h29zXRTKkaswfKPkp",
            "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
            "template_id"=> $this->templates[$data->type],
            "template_params"=> (object) [
                "name"=> $data->name,
                "email"=> $data->email,
                "header"=> $data->header,
                "body"=> $data->body,
                "system"=> $this->systems[$data->system]
            ]
        ]);

        Log::channel('notifications')->info('Notification email sent to '.$data->email." for ".$data->header." - ".$data->body);

        return;
    }

    public function createSuperAdminNotification($data){
        $users = User::where('role', 'super-admin')->get();

        foreach($users as $user){
            // Create Notification in Database
            $notification = new Notifications();
            $notification->id = uniqid('notification_');
            while(Notifications::where('id', $notification->id)->exists()){
                $notification->id = uniqid('notification_');
            }

            $notification->fill((array) $data);
            $notification->user_id = $user->id;
            $notification->save();

            // Check if user has email notifications on
            if($user->email_notifications){
                $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                    "service_id"=> "service_0vyim6r",
                    "user_id"=> "h29zXRTKkaswfKPkp",
                    "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                    "template_id"=> $this->templates[$data->type],
                    "template_params"=> (object) [
                        "name"=> $user->name,
                        "email"=> $user->email,
                        "header"=> $data->header,
                        "body"=> $data->body,
                        "system"=> $this->systems[$data->system]
                    ]
                ]);
            }

            $log_info = [
                "email_sent"=> $user->email_notifications,
                "user"=> $user->name,
                "email"=> $user->email,
                "data"=> $data
            ];

            Log::channel('notifications')->info(json_encode($log_info));
        }

        return;
    }

    public function createOnboardingNotification($data){
        $users = User::where('onboarding', true)->get();

        foreach($users as $user){
            // Create Notification in Database
            $notification = new Notifications();
            $notification->id = uniqid('notification_');
            while(Notifications::where('id', $notification->id)->exists()){
                $notification->id = uniqid('notification_');
            }

            $notification->fill((array) $data);
            $notification->user_id = $user->id;
            $notification->save();

            // Check if user has email notifications on
            if($user->email_notifications){
                $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                    "service_id"=> "service_0vyim6r",
                    "user_id"=> "h29zXRTKkaswfKPkp",
                    "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                    "template_id"=> $this->templates[$data->type],
                    "template_params"=> (object) [
                        "name"=> $user->name,
                        "email"=> $user->email,
                        "header"=> $data->header,
                        "body"=> $data->body,
                        "system"=> $this->systems[$data->system]
                    ]
                ]);
            }

            $log_info = [
                "email_sent"=> $user->email_notifications,
                "user"=> $user->name,
                "email"=> $user->email,
                "data"=> $data
            ];

            Log::channel('notifications')->info(json_encode($log_info));
        }

        return;
    }
}