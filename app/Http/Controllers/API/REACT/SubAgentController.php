<?php

namespace App\Http\Controllers\API\REACT;

use App\Http\Controllers\Controller;
use App\Models\mga_companies;
use App\Models\react_commission_policies;
use App\Models\react_commissions;
use App\Models\react_sub_agents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SubAgentController extends Controller
{
    public $carriers = [
        "aon"=> [
            "name"=> "AON Edge",
        ],
        "beyond"=> [
            "name"=> "Beyond Flood",
        ],
        "cat"=> [
            "name"=> "Cat Coverage",
        ],
        "dual"=> [
            "name"=> "Dual Flood",
        ],
        "flow"=> [
            "name"=> "Flow Flood",
        ],
        "neptune"=> [
            "name"=> "Neptune",
        ],
        "palomar"=> [
            "name"=> "Palomar",
        ],
        "sterling"=> [
            "name"=> "Sterling",
        ],
        "wright"=> [
            "name"=> "Wright - NFIP",
        ]
    ];

    public function index(Request $request){
        $agents = mga_companies::orderBy('created_at', 'desc')->get();

        $data = [];

        foreach($agents as $agent){
            $carrier_data = json_decode(Http::get('https://backend.agentportal.rocketmga.com/api/services/client/get-mga-company-carriers/'.$agent->rocket_id))->data->carriers_list;
            
            foreach ($carrier_data as $carrier) { 
                foreach($this->carriers as $key=>$value){
                    if(!str_contains(strtolower($carrier->name), 'wright') && $carrier->name == $value['name']){
                        $agent->$key = json_decode($agent->$key);
                        $agent->$key->direct = $carrier->direct;
                    }
                }

                if($carrier->name == "Wright - NFIP"){
                    $agent->wright = json_decode($agent->wright);
                    $agent->wright->direct = $carrier->direct;
                }
            }

            // Decode Cat Coverage
            $agent->cat = json_decode($agent->cat);
            $agent->cat->direct = false;

            $rocketPlusValid = true;
            $carriersValid = false;

            // Filter Rocket Appointed Carriers
            foreach($request->filter['carriers'] as $carrier){
                if($carrier['code'] !== 'cat' && !$carriersValid && !$agent->{$carrier['code']}->direct){
                    $carriersValid = true;
                }
            }

            // Filter Rocket Plus Status
            // foreach($request->filter['rocketPlus'] as $value){
            //     if(!$rocketPlusValid && $agent->rocketPlus == $value['code']){
            //         $rocketPlusValid = true;
            //     }
            // }

            if($carriersValid && $rocketPlusValid){
                array_push($data, $agent);
            }
        }

        $response = [
            'success'=> true,
            'agents'=> $data
        ];

        return response()->json($response, 200);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'rocket_id'=> 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        if(mga_companies::where('rocket_id', $request->rocket_id)->exists()){
            $response = [
                'success'=> false,
                'message'=> 'Rocket ID already exists. Please update and try again.'
            ];
            
            return response()->json($response, 422);
        }

        $noCarriers = ['name', 'rocket_id'];

        $inputs = $request->all();

        foreach($inputs as $key => $value){
            if(!in_array($key, $noCarriers)){
                $inputs[$key] = json_encode((object) [
                    "carrier_username"=> null,
                    "commission_id"=> $value
                ]);
            }
        }

        $subagent = mga_companies::create($inputs);
        $subagent->save();
        Log::channel('mga_companies')->info('New MGA Company', (array) $subagent);

        $response = [
            'success'=> true,
            'message'=> 'New sub agent created successfully.'
        ];

        return response()->json($response, 200);
    }
    
    public function update(Request $request, $rocket_id){
        // Save MGA Carrier Commission ID for each carrier
        $subagent = mga_companies::find($rocket_id);

        $noCarriers = ['name', 'rocket_id', 'created_at', 'updated_at'];

        $inputs = $request->all();

        foreach($inputs as $key=>$value){
            if(!in_array($key, $noCarriers)){
                unset($value['direct']);
                $inputs[$key] = json_encode($value);
            }
        }

        $subagent->fill($inputs);
        $subagent->save();

        // Save MGA Company Direct disposition for Each carrier
        $carrier_data = json_decode(Http::get('https://backend.agentportal.rocketmga.com/api/services/client/get-mga-company-carriers/'.$rocket_id))->data->carriers_list;

        foreach($carrier_data as $carrier){
            foreach($this->carriers as $key=>$value){
                if(!str_contains(strtolower($carrier->name), 'wright') && $carrier->name == $value['name']){
                    $carrier->direct = $request->$key['direct'];
                }
            }

            if(str_contains(strtolower($carrier->name), 'wright')){
                $carrier->direct = $request->wright['direct'];
            }
        }

        Http::post('https://backend.agentportal.rocketmga.com/api/services/client/update-api-config', [
            "rocket_id"=> $rocket_id,
            "carriers_list"=> $carrier_data
        ]);

        $response = [
            'success'=> true,
            'message'=> 'Sub agent details updated successfully.'
        ];

        return response()->json($response, 200);
    }

    public function report(Request $request, $rocket_id){
        $agency = mga_companies::find($rocket_id);
        $carrier_data = json_decode(Http::get('https://backend.agentportal.rocketmga.com/api/services/client/get-mga-company-carriers/'.$rocket_id))->data->carriers_list;

        $months = [];
        foreach(react_commissions::select('month')->distinct()->get() as $key=>$value){
            array_push($months, $value->month);
        }

        $revenue = (object) [];
        $agency_policies = [];

        $pieChart = (object) [
            "labels"=> [],
            "datasets"=> [
                (object) [
                    "data"=> []
                ]
            ]
        ];

        $lineChart = (object) [
            "labels"=> $months,
            "datasets"=> [
                (object) [
                    "label"=> "Override Revenue",
                    "data"=> []
                ]
            ]
        ];
        
        foreach($this->carriers as $key=>$value){
            // Decode Sub Agent Carrier information and add Carrier name to Object
            $agency->$key = json_decode($agency->$key);
            $agency->$key->name = $value['name'];

            foreach ($carrier_data as $carrier) { 
                if(!str_contains(strtolower($carrier->name), 'wright') && $carrier->name == $value['name']){
                    $agency->$key->direct = $carrier->direct;
                }

                if($carrier->name == "Wright - NFIP" && $key == 'wright'){
                    $agency->$key->direct = $carrier->direct;
                }
            }

            if($key == 'cat'){
                $agency->$key->direct = false;
            }

            $carrierPolicies = 0;
            $carrierPrem = 0;

            // Check to see if the carrier is Rocket Appointed and if it is pull all the policies attached to the carrier code
            if(!$agency->$key->direct){
                $carrier_policies = react_commission_policies::where('carrier_id', $agency->$key->commission_id)->get();

                $carrierPolicies = count($carrier_policies);

                foreach($carrier_policies as $policy){
                    $carrierPrem = $carrierPrem + $policy->prem;

                    array_push($agency_policies, $policy);
                }
            }

            $agency->$key->prem = (float) number_format($carrierPrem, 2, '.', '');
            $agency->$key->policies = $carrierPolicies;

            array_push($pieChart->labels, $value['name']);
            array_push($pieChart->datasets[0]->data, $carrierPrem);
        }

        $revenue->months = [];

        foreach($months as $month){
            $monthRev = 0;

            foreach($agency_policies as $policy){
                if($policy->month == $month){
                    $monthRev = $monthRev + $policy->override;
                }
            }

            array_push($lineChart->datasets[0]->data, $monthRev);
            array_push($revenue->months, (object) ["month"=> $month, "revenue"=> (float) number_format($monthRev, 2, '.', '')]);
        }

        $total_prem = 0;
        $total_override = 0;
        foreach($agency_policies as $policy){
            $total_prem = $total_prem + $policy->prem;
            $total_override = $total_override + $policy->override;
        }

        $revenue->override = (float) number_format($total_override, 2, '.', '');
        $revenue->avgOverride = (float) number_format($total_override / count($months), 2, '.', '');
        $revenue->prem = (float) number_format($total_prem, 2, '.', '');
        // Make Sure that the policy count is more than 0 so that we can divide
        $revenue->avgPrem = count($agency_policies) > 0 ? (float) number_format($total_prem / count($agency_policies), 2, '.', '') : 0;
        $revenue->avgPolicies = count($agency_policies) / count($months);
        $revenue->policies = count($agency_policies);

        $response = [
            'success'=> true,
            'agency'=> $agency,
            'revenue'=> $revenue,
            'pieChart'=> $pieChart,
            'lineChart'=> $lineChart
        ];

        return response()->json($response, 200);
    }

    public function bulkUpload(Request $request){
        $reader = IOFactory::createReader('Xlsx');
        $date = Carbon::now()->format('Y-m-d');

        // Crete file name and Save file
        $file_name = $date.'_bulk_agent_upload'.'.' . $request->file('file')->getClientOriginalExtension();
        $file_path = $request->file('file')->storeAs('agent_bulk_upload', $file_name, 'react');
        ini_set('mbstring.substitute_character', "none");

        $path = storage_path().'/app/react/'.$file_path;
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());

        $data = [];
        
        // For Each Agent on the Sheet Skipping Row 1
        foreach($sheet->getRowIterator() as $row){
            if($row->getRowIndex() == 1){
                continue;
            }

            $cells = iterator_to_array($row->getCellIterator("A", "K"));
            $carriers = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

            $agent = (object) [
                "rocket_id" => $cells['A']->getValue(),
                "name" => $cells['B']->getValue(),
            ];

            $i = 0;
            foreach($this->carriers as $key=>$value){
                if(!empty($cells[$carriers[$i]]->getValue())){
                    $agent->$key = $cells[$carriers[$i]]->getValue();
                }else{
                    $agent->$key = null;
                }

                $i++;
            }

            array_push($data, $agent);
        }

        foreach($data as $agency){
            $exists = mga_companies::where('rocket_id', $agency->rocket_id)->exists();

            if($exists){
                $existingAgency = mga_companies::where('rocket_id', $agency->rocket_id)->first();

                foreach($this->carriers as $key=>$value){
                    $carrier_data = json_decode($existingAgency->$key);
                    $carrier_data->commission_id = $agency->$key;

                    $existingAgency->$key = json_encode($carrier_data);
                }

                $existingAgency->save();

                Log::channel('mga_companies')->info('Bulk Upload - Exisitng MGA Company Update:', (array) $agency);
            }else {
                foreach($this->carriers as $key=>$value){
                    $carrierObj = (object) [
                        "carrier_username"=> null,
                        "commission_id"=> $agency->$key
                    ];

                    $agency->$key = json_encode($carrierObj);
                }

                $newAgency = new mga_companies();
                $inputs = (array) $agency;

                $newAgency->fill($inputs);
                $newAgency->save();

                Log::channel('mga_companies')->info('Bulk Upload - New MGA Company Created:', (array) $agency);
            }
        }

        Log::channel('mga_companies')->info('New MGA Company Bulk Upload - Count: '.count($data));

        $response = [
            'success'=> true,
            'message'=> "Agents uploaded successfuly."
        ];

        return response()->json($response, 200);
    }
}
