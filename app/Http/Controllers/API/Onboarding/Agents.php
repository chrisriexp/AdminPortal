<?php

namespace App\Http\Controllers\API\Onboarding;

use App\Http\Controllers\Controller;
use App\Models\mga_companies;
use App\Helper\NotificationsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Agents extends Controller
{
    public $mga_carriers = [
        'aon'=> 'AON Edge',
        'beyond'=> 'Beyond Flood',
        'dual'=> 'Dual Flood',
        'flow'=> 'Flow Flood',
        'neptune'=> 'Neptune',
        'palomar'=> 'Palomar',
        'sterling'=> 'Sterling',
        'wright'=> 'Wright - NFIP'
    ];

    public function index(Request $request){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->post('https://onboarding.rocketmga.com/api/admin-portal/agents', [
            "filters"=> $request->filters
        ]);

        $response = json_decode($data);

        return response()->json($response, $data->status());
    }

    public function agency(Request $request, $rocket_id, $category){
        if($category !== 'carriers'){
            $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://onboarding.rocketmga.com/api/admin-portal/agency/'.$rocket_id.'/'.$category,);

            $response = json_decode($data);

            return response()->json($response, $data->status());
        }else{
            $carrier_data = json_decode(Http::get('https://backend.agentportal.rocketmga.com/api/services/client/get-mga-company-carriers/'.$rocket_id))->data->carriers_list;
            $data = [];

            $company_exists = mga_companies::where('rocket_id', $rocket_id)->exists();

            if($company_exists){
                $existingCompany = mga_companies::find($rocket_id);

                // For Each Carrier in the array from MGA Portal if the name property is equal to the carrier value when going through each carrier
                // in the above array then assign the username and commision id to that carrier object
                foreach($carrier_data as $carrier){
                    foreach($this->mga_carriers as $key=>$value){
                        if($value == $carrier->name){
                            $carrier_info = json_decode($existingCompany->$key);

                            $carrier->carrier_username = $carrier_info->carrier_username;
                            $carrier->commission_id = $carrier_info->commission_id;
                        }
                    }

                    if(!str_contains(strtolower($carrier->name), 'wright')){
                        array_push($data, $carrier);
                    }elseif($carrier->name == 'Wright - NFIP'){
                        array_push($data, $carrier);
                    }
                }
            }else{
                $newCompany = new mga_companies();
                $newCompany->rocket_id = $rocket_id;
                $newCompany->save();

                // For Each Carrier in the array from MGA Portal if the name property is equal to the carrier value when going through each carrier
                // in the above array then assign the username and commision id to that carrier object
                foreach($carrier_data as $carrier){
                    foreach($this->mga_carriers as $key=>$value){
                        if($value == $carrier->name){
                            $carrier->carrier_username = "";
                            $carrier->commission_id = "";
                        }
                    }

                    if(!str_contains(strtolower($carrier->name), 'wright')){
                        array_push($data, $carrier);
                    }elseif($carrier->name == 'Wright - NFIP'){
                        array_push($data, $carrier);
                    }
                }
            }

            $response = [
                'success'=> true,
                'data'=> $data
            ];

            return response()->json($response, 200);
        }
    }

    public function admin_update(Request $request, $rocket_id, $category){
        if($category !== 'carriers'){
            $requestData = [];

            if($request->has('agency')){
                $requestData['agency'] = $request->agency;
            }

            if($request->has('approvals')){
                $requestData['approvals'] = $request->approvals;
            }

            $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->put('https://onboarding.rocketmga.com/api/admin-portal/agency/'.$rocket_id.'/'.$category, $requestData);

            $response = json_decode($data);

            return response()->json($response, $data->status());
        }else{
            $mga_company = mga_companies::find($rocket_id);

            $mga_data = (object) [
                "rocket_id"=> $rocket_id,
                "carriers_list"=> [
                    
                ]
            ];

            $wright_data = (object) [];

            foreach($request->carriers as $carrier){
                foreach($this->mga_carriers as $key=>$value){
                    if($value == $carrier['name']){
                        $carrier_info = json_decode($mga_company->$key);
                        $carrier_info->carrier_username = $carrier['carrier_username'];
                        $carrier_info->commission_id = $carrier['commission_id'];
                        $mga_company->$key = json_encode($carrier_info);
                    }
                }

                if(!str_contains(strtolower($carrier['name']), 'wright')){
                    // Push Carrier Data for MGA Portal for all carriers but Wright
                    $carrier_data = (object) [
                        "name"=> $carrier['name'],
                        "direct"=> $carrier['direct'],
                        "uip_fields"=> $carrier['uip_fields'],
                    ];
    
                    array_push($mga_data->carriers_list, $carrier_data);
                }else{
                    // Save Wright Infomation to add it to all the Wright Objects Later
                    if($carrier['name'] == 'Wright - NFIP'){
                        $wright_data = (object) [
                            "direct"=> $carrier['direct'],
                            "uip_fields"=> $carrier['uip_fields'],
                        ];
                    }
                }
            }

            $wright_market = [' - Hiscox', ' - ResiFlood', ' - NFIP'];

            foreach($wright_market as $carrier){
                $carrier_data = (object) [
                    "name"=> 'Wright'.$carrier,
                    "direct"=> $wright_data->direct,
                    "uip_fields"=> $wright_data->uip_fields,
                ];

                array_push($mga_data->carriers_list, $carrier_data);
            }

            $mga_company->save();

            $data = Http::post('https://backend.agentportal.rocketmga.com/api/services/client/update-api-config', $mga_data);

            $response = [
                'success'=> true,
                'message'=> 'Agency Carriers Updated Successfully',
            ];

            return response()->json($response, 200);
        }
    }

    public function approve(Request $request, $rocket_id){
        $data = Http::timeout(60)->withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://onboarding.rocketmga.com/api/admin-portal/approve/'.$rocket_id);

        $response = json_decode($data);

        if($response->success){
            // Create New MGA Company and enter email as username for the corresponding carriers
            $email_user_carriers = ['aon', 'dual', 'flow', 'palomar', 'sterling'];

            $newMGA_company = new mga_companies();
            $newMGA_company->rocket_id = $rocket_id;
            $newMGA_company->name = $response->name;
            $newMGA_company->save();

            $mga_company = mga_companies::find($rocket_id);

            foreach($email_user_carriers as $carrier_name){
                $carrier_data = json_decode($mga_company->$carrier_name);

                $carrier_data->carrier_username = $response->email;
                $mga_company->$carrier_name = json_encode($carrier_data);
                $mga_company->save();
            }

            if(!is_null($response->agentFLNo)){
                $neptuneData = json_decode($mga_company->neptune);
                $neptuneData->carrier_username = $response->agentFLNo;
                $neptuneData->commission_id = $response->agentFLNo;

                $mga_company->neptune = json_encode($neptuneData);
                $mga_company->save();
            }

            // Onboarding Notification for agency approval
            (new NotificationsHelper)->createOnboardingNotification((object) [
                'header'=> 'NEW MGA Agency Approved',
                'body'=> $response->name."'s onboarding application was just approved successfully. You can access their onboarding information at https://admin.rocketflood.com/onboarding/agency/". $rocket_id ."/agency",
                'type'=> 'success',
                'system'=> 'onboarding' 
            ]);
        }

        return response()->json($response, $data->status());
    }

    public function finalize(Request $request, $rocket_id, $force){
        $data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://onboarding.rocketmga.com/api/admin-portal/finalize/'.$rocket_id.'/'.$force);

        $response = json_decode($data);
        
        $carrier_logins_start = "<!DOCTYPE html><html><head><style>body{font-family: arial, sans-serif;}table{font-family: arial, sans-serif;border-collapse: collapse;width: 100%;}{border: 2px solid #dddddd;}td, th {border: 2px solid #dddddd;text-align: left;padding: 8px;}</style></head><body><h2>Flood Carrier Website Credentials:</h2><p><i>The carrier has sent you an email containing your password directly. In case you need to change your password, you can do so by visiting the carrier sites listed below and using the 'reset password' option along with your username</i></p><table><tr><th>Carrier</th><th>Username</th><th>Login Page</th></tr>";
        $carrier_logins_end = "</table></body></html>";
        $carrier_logins = "";

        $carrier_links = [
            "AON Edge"=> "https://c68-prod.diamondasaservice.com/DiamondWeb/(S(bwzhni2uocizhp1ynvlnrcj5))/agency",
            "Beyond Flood"=> "https://natgenagency.com/",
            "Dual Flood"=> "https://app.dualna.com/flood/login?returnUrl=%2Fquotes",
            "Flow Flood"=> "https://agents.flowinsurance.com/Public/AgentLogin",
            "Neptune"=> "https://neptuneflood.com/agent-hub/#/login?redirect=%2Fhome",
            "Palomar"=> "https://pass.palomarspecialty.com/prweb/sso",
            "Sterling"=> "https://backoffice.sterlingsu.com/login",
            "Wright - NFIP"=> "https://www.wrightflood.net/praesidium/app/sign-in"
        ];

        if($response->success){
            $mga_company = mga_companies::find($rocket_id);

            foreach($response->rocket_carriers as $carrier){
                foreach($this->mga_carriers as $key=>$value){
                    if($carrier == $value){
                        $carrier_logins = $carrier_logins.'<tr><td>'.$value.'</td><td>'. (empty(json_decode($mga_company->$key)->carrier_username) ? 'Pending' : json_decode($mga_company->$key)->carrier_username) .'</td><td><a href="'.$carrier_links[$value].'" target="_blank">Portal Login</a></td></tr>';
                    }
                }
            }

            $carrier_logins = $carrier_logins_start.$carrier_logins.$carrier_logins_end;

            $adminEmail = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                "service_id"=> "service_nf9yozb",
                "user_id"=> "h29zXRTKkaswfKPkp",
                "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                "template_id"=> "template_0qzq3oq",
                "template_params"=> (object) [
                    "agency_name"=> $response->agency_name,
                    "email"=> $response->admin->email,
                    "password"=> $response->admin->password,
                    "agent_logins"=> $response->agent_logins,
                    "carrier_logins"=> $carrier_logins
                ]
            ]);

            (new NotificationsHelper)->createOnboardingNotification((object) [
                'header'=> 'NEW MGA Agency Appointed',
                'body'=> $response->agency_name." was just fully appointed with Rocket MGA. You can access their onboarding information at https://admin.rocketflood.com/onboarding/agency/". $rocket_id ."/agency",
                'type'=> 'success',
                'system'=> 'onboarding' 
            ]);
        }

        return response()->json($response, $data->status());
    }
}
