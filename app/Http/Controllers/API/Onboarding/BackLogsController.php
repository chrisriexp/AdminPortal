<?php

namespace App\Http\Controllers\API\Onboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\appoint_back_cat;
use App\Models\mga_companies;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BackLogsController extends Controller
{
    public $carrierKey = [
        "cat_coverage"=> [
            "table"=> "appoint_back_cats",
            "stages"=> ['Information Required', 'Awaiting Agreement', 'Appointed']
        ]
    ];

    public function carrier(Request $request, $carrier){
        $agents = DB::table($this->carrierKey[$carrier]['table'])->get();

        $report = [];
        
        foreach($this->carrierKey[$carrier]['stages'] as $stage){
            array_push($report, [
                "name"=> $stage,
                "count"=> count(DB::table($this->carrierKey[$carrier]['table'])->where('status', $stage)->get())
            ]);
        }

        foreach($agents as $agent){
            $agent->data = json_decode($agent->data);
            
            if(gettype($agent->data->agency_type) == 'string'){
                $agent->data->agency_type = json_decode($agent->data->agency_type);
            }
        }

        $completion_rate = number_format(count(DB::table($this->carrierKey[$carrier]['table'])->where('appointed', true)->get()) / count($agents) * 100, 2, '.', '');

        $response = [
            "success"=> true,
            "agents"=> $agents,
            "report"=> $report,
            "completion_rate"=> $completion_rate
        ];

        return response()->json($response, 200);
    }

    public function update_carrier(Request $request, $carrier){
        switch($carrier){
            case 'cat_coverage':
                $agent = appoint_back_cat::find($request->agent['rocket_id']);
                break;
            default:
                $agent = null;
                return;
                break;
        }

        $data = $request->agent;

        $fieldsValid = true;

        foreach($data['data'] as $key=> $value){
            if($key != 'address2' && empty($value)){
                $fieldsValid = false;
            }
        }

        $data['data'] = json_encode($data['data']);

        foreach($data as $key=>$value){
            $agent->$key = $value;
        }
        
        $agent->save();

        if($fieldsValid && $carrier == 'cat_coverage'){
            $this->catCoverage($request->agent['rocket_id']);
        }

        $response = [
            "success"=> true,
            "message"=> "Agency Updated."
        ];

        return response()->json($response, 200);
    }

    public function catCoverage($rocket_id){
        // Cat Coverage API Token
        $catTokenRequest = Http::asForm()->post('https://login.microsoftonline.com/CatCoverage.secureleader.co/oauth2/token', [
            "grant_type"=> "client_credentials",
            "client_id"=> "fa7a11b7-ef74-4434-81b0-6723d594de5f",
            "client_secret"=> "WN~8Q~s.pXZbJ8Vc1sFn3yLfev4QXlYQl7V~IduW",
            "resource"=> "https://catcoverage.secureleader.co/catcoverageapplication",
            "scope"=> "openid"
        ]);

        $catToken = json_decode($catTokenRequest)->access_token;

        $agency = appoint_back_cat::find($rocket_id);

        $catData = json_decode($agency->data);
        $agency->pass = (string) Str::random(8).rand().'!';
        $agency->save();

        $cat_brokenName = explode(" ", $catData->agent_name);

        $catAddress = [
            "addressLine1" => $catData->address1,
            "addressLine2" => empty($catData->address2) ? "" : $catData->address2,
            "city" => $catData->city,
            "state" => $catData->state,
            "postalCode" => $catData->zip
        ];

        $agencyTypeCode = json_decode($catData->agency_type)->code;

        if($agencyTypeCode == 'Sole'){
            $cat_businessType = 692;
        }elseif(Str::contains($agencyTypeCode, "Corp")){
            $cat_businessType = 691;
        }elseif(Str::contains($agencyTypeCode, "LLC")){
            $cat_businessType = 694;
        }elseif($agencyTypeCode == 'Partner'){
            $cat_businessType = 693;
        }else{
            $cat_businessType = 695;
        }

        $catPayload = [
            "companyName"=> $catData->agency_name,
            "businessType_Id"=> $cat_businessType,
            "heardThrough_Id"=> 9,
            "physicalAddress"=> $catAddress,
            "mailingAddress"=> $catAddress,
            "phone"=> $catData->phone,
            "fax"=> "",
            "taxId"=> $catData->agency_tax_id,
            "residentStateLicenseNumber"=> $catData->agency_license,
            "nationalProducerLicenseNumber"=> $catData->agent_npn,
            "producerAssociationId"=> 11498,
            "errorsAndOmissionsData"=> [
                "carrierName"=> $catData->eo_insurer,
                "policyNumber"=> $catData->eo_policy,
                "limitPerClaim"=> $catData->eo_limit,
                "aggregateLimit"=> $catData->eo_limit,
                "policyExpirationDate"=> $catData->eo_exp
            ],
            "firstName"=> $cat_brokenName[0],
            "lastName"=> $cat_brokenName[1],
            "emailAddress"=> $catData->email,
            "password"=> $agency->pass
        ];
    
        $catSubAgentRequest = Http::withHeaders(['Authorization' => "Bearer ".(string) $catToken])->post("https://app-api.catcoverage.com/api/SignUp/createproducer", $catPayload);

        if($catSubAgentRequest->status() == 200){
            $cat_agent_id = (string) $catSubAgentRequest->body();
            $agency->subagent_api = true;
            $agency->save();
            Log::channel('carrier-appointment-requests')->info($rocket_id.' Successful Cat Coverage Sub Agent API request for '.$catData->agency_name.'; Status Code: '.$catSubAgentRequest->status(), [$catSubAgentRequest->body(), $catPayload]);
            
            $company = mga_companies::find($rocket_id);

            // Update commission ID for Cat Coverage
            $mgaCatData = json_decode($company->cat);
            $mgaCatData->commission_id = $cat_agent_id;

            $company->cat = json_encode($mgaCatData);
            $company->save();
        }else{
            Log::channel('carrier-appointment-requests')->info($rocket_id.' Failed Cat Coverage Sub Agent API request for '.$catData->agency_name.'; Status Code: '.$catSubAgentRequest->status(), [$catSubAgentRequest->body(), $catPayload]);
        }

        // Cat Coverage Agreement
        $document_data = (object) [
            "name"=> "Rocket MGA - ".$catData->agency_name." Cat Coverage Agreement",
            "template_uuid"=> "7kw84aq2ZaFcNJj6xotrE7",
            "recipients"=> [(object) [
                "email"=> $catData->email,
                "first_name"=> $cat_brokenName[0],
                "last_name"=> $cat_brokenName[1],
                "role"=> "Client"
            ]],
            "fields"=> (object) [
                "agency_name"=> (object) [
                    "value"=> $catData->agency_name,
                    "role"=> "Client"
                ],
                "agent_name"=> (object) [
                    "value"=> $catData->agent_name,
                    "role"=> "Client"
                ],
            ]
        ];

        // Create Pandadoc Agreement and save the ID
        $catCovAgreement = Http::withHeaders([
            "Authorization"=> "API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3",
            "Content-Type"=> "application/json"
        ])
        ->post('https://api.pandadoc.com/public/v1/documents', $document_data);
        
        if($catCovAgreement->status() == 201){
            // Save Document ID
            $agency->document = json_decode($catCovAgreement)->id;
            $agency->status = 'Awaiting Agreement';
            $agency->save();

            Log::channel('documents')->info($company->rocket_id.' - '.$catData->agency_name.': Cat Coverage Agreement document created');
 
            sleep(5);

            // Send Agreement
            $sendCatCovAgreement = Http::withHeaders([
                "Authorization"=> "API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3",
                "Content-Type"=> "application/json"
            ])
            ->post('https://api.pandadoc.com/public/v1/documents/'.$agency->document.'/send', (object) [
                "message"=> "Cat Coverage Agreement - New Rocket MGA Carrier!",
                "silent"=> false
            ]);

            // Get Document Signing Link
            $documentEmbed = Http::withHeaders([
                "Authorization"=> "API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3",
                "Content-Type"=> "application/json"
            ])
            ->post('https://api.pandadoc.com/public/v1/documents/'.$agency->document.'/session', (object) [
                "recipient"=> $catData->email,
                "lifetime"=> 9999999
            ]);
    
            $agreementLink = json_decode($documentEmbed)->id;

            // Send Email with the document Link to Sign
            $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                "service_id"=> "service_nf9yozb",
                "user_id"=> "h29zXRTKkaswfKPkp",
                "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                "template_id"=> "template_gvs4h65",
                "template_params"=> (object) [
                    "agency_name"=> $catData->agency_name,
                    "email"=> $catData->email,
                    "link"=> "https://app.pandadoc.com/s/".$agreementLink
                ]
            ]);
        }else{
            Log::channel('documents')->error($company->rocket_id.' - '.$catData->agency_name.': Cat Coverage Agreement failed to create', (array) json_decode($catCovAgreement));
        }

        return;
    }
}
