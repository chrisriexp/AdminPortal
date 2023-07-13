<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\mga_companies;
use App\Models\appoint_back_cat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class appointBackCatCov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appoint_back:cat_coverage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Appointment Backlog for Cat Coverage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fieldKey = [
            "agency_name"=> "Agency Name",
            "agent_name"=> "Agent Name",
            "phone"=> "Phone Number",
            "email"=> "Email",
            "address1"=> "Address 1",
            "city"=> "City",
            "state"=> "State",
            "zip"=> "Zip",
            "agency_type"=> "Agency Type i.e. LLC-C",
            "agency_tax_id"=> "Agency Tax ID",
            "agency_license"=> "Agency License",
            "agent_npn"=> "Producer NPN",
            "eo_policy"=> "E&O Policy Number",
            "eo_insurer"=> "E&O Insurer",
            "eo_limit"=> "E&O Limit",
            "eo_exp"=> "E&O Exp",
        ];

        $companies = mga_companies::get();
        
        // Cat Coverage API Token
        $catTokenRequest = Http::asForm()->post('https://login.microsoftonline.com/CatCoverage.secureleader.co/oauth2/token', [
            "grant_type"=> "client_credentials",
            "client_id"=> "fa7a11b7-ef74-4434-81b0-6723d594de5f",
            "client_secret"=> "WN~8Q~s.pXZbJ8Vc1sFn3yLfev4QXlYQl7V~IduW",
            "resource"=> "https://catcoverage.secureleader.co/catcoverageapplication",
            "scope"=> "openid"
        ]);

        $catToken = json_decode($catTokenRequest)->access_token;

        foreach($companies as $company){
            // Get Agency Data from Onboarding DB
            $mga_data = Http::withHeaders(['token'=>'27b00fca-4d9e-4e28-85ce-54f16af26c0b'])->get('https://onboarding.rocketmga.com/api/admin-portal/get-agency/'.$company->rocket_id);

            if($mga_data->status() == 200){
                $agency_data = json_decode($mga_data)->agency;
            }else{
                $agency_data = null;
            }

            // Check if Entry Exists
            if(appoint_back_cat::where('rocket_id', $company->rocket_id)->exists()){
                $catCovEntry = appoint_back_cat::find($company->rocket_id);
            }else{
                // Create AppointmentBack Log Entry 
                $catCovEntry = new appoint_back_cat();
                $catCovEntry->rocket_id = $company->rocket_id;
                $catCovEntry->save();
            }

            // If there is data from the onboarding portal, assign all the values in the data column
            if(!empty($agency_data)){
                $tempCatData = json_decode($catCovEntry->data);

                foreach($tempCatData as $key=>$value){
                    $tempCatData->$key = $agency_data->$key;
                }

                $catCovEntry->data = json_encode($tempCatData);
                $catCovEntry->save();
            }

            // Check to see if Cat Coverage Commission ID is entered
            if(!empty(json_decode($company->cat)->commission_id)){
                $catCovEntry->appointed = true;
                $catCovEntry->status = "Appointed";
                $catCovEntry->save();
                continue;
            }

            $pendingFields = [];

            // Check to see if all data values have been entered
            $dataValid = true;
            foreach(json_decode($catCovEntry->data) as $key=>$value){
                if($key != 'address2' && empty($value)){
                    array_push($pendingFields, $fieldKey[$key]);
                    $dataValid = false;
                }
            }

            $catData = json_decode($catCovEntry->data);

            // Able to Create the Agency Through the API
            if($dataValid){
                $catCovEntry->pass = (string) Str::random(8).rand().'!';
                $catCovEntry->save();

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
                    "password"=> $catCovEntry->pass
                ];

                $catSubAgentRequest = Http::withHeaders(['Authorization' => "Bearer ".(string) $catToken])->post("https://app-api.catcoverage.com/api/SignUp/createproducer", $catPayload);

                if($catSubAgentRequest->status() == 200){
                    $cat_agent_id = (string) $catSubAgentRequest->body();
                    $catCovEntry->subagent_api = true;
                    $catCovEntry->save();
                    Log::channel('carrier-appointment-requests')->info($company->rocket_id.' Successful Cat Coverage Sub Agent API request for '.$catData->agency_name.'; Status Code: '.$catSubAgentRequest->status(), [$catSubAgentRequest->body(), $catPayload]);
                    
                    // Update commission ID for Cat Coverage
                    $mgaCatData = json_decode($company->cat);
                    $mgaCatData->commission_id = $cat_agent_id;

                    $company->cat = json_encode($mgaCatData);
                    $company->save();


                }else{
                    Log::channel('carrier-appointment-requests')->info($company->rocket_id.' Failed Cat Coverage Sub Agent API request for '.$catData->agency_name.'; Status Code: '.$catSubAgentRequest->status(), [$catSubAgentRequest->body(), $catPayload]);
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
                    $catCovEntry->document = json_decode($catCovAgreement)->id;
                    $catCovEntry->status = 'Awaiting Agreement';
                    $catCovEntry->save();
        
                    Log::channel('documents')->info($company->rocket_id.' - '.$catData->agency_name.': Cat Coverage Agreement document created');
         
                    sleep(5);

                    // Send Agreement
                    $sendCatCovAgreement = Http::withHeaders([
                        "Authorization"=> "API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3",
                        "Content-Type"=> "application/json"
                    ])
                    ->post('https://api.pandadoc.com/public/v1/documents/'.$catCovEntry->document.'/send', (object) [
                        "message"=> "Cat Coverage Agreement - New Rocket MGA Carrier!",
                        "silent"=> false
                    ]);

                    // Get Document Signing Link
                    $documentEmbed = Http::withHeaders([
                        "Authorization"=> "API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3",
                        "Content-Type"=> "application/json"
                    ])
                    ->post('https://api.pandadoc.com/public/v1/documents/'.$catCovEntry->document.'/session', (object) [
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
            }
            // Information Still Required
            else{
                $pendingFieldsStr = "";

                foreach($pendingFields as $key=>$value){
                    $pendingFieldsStr = $pendingFieldsStr.($key == 0 ? '' : ', ').$value;
                }

                // Send Email asking for all the required fields
                $email = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
                    "service_id"=> "service_nf9yozb",
                    "user_id"=> "h29zXRTKkaswfKPkp",
                    "accessToken"=> "77MJk1G5Dy2wGTpULFmVI",
                    "template_id"=> "template_vrafqww",
                    "template_params"=> (object) [
                        "agency_name"=> $catData->agency_name,
                        "email"=> $catData->email,
                        "fields"=> $pendingFieldsStr
                    ]
                ]);
            }
        }

        dd('success');
    }
}
