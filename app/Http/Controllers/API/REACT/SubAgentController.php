<?php

namespace App\Http\Controllers\API\REACT;

use App\Http\Controllers\Controller;
use App\Models\react_commission_policies;
use App\Models\react_commissions;
use App\Models\react_sub_agents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubAgentController extends Controller
{
    public $carriers = [
        "aon"=> "Aon Edge",
        "beyond"=> "Beyond Flood",
        "cat"=> "Cat Coverage",
        "flow"=> "Flow Flood",
        "neptune"=> "Neptune",
        "palomar"=> "Palomar",
        "sterling"=> "Sterling",
        "wright"=> "Wright National"
    ];

    public function index(Request $request){
        $agents = react_sub_agents::orderBy('created_at', 'desc')->get();

        $carriers = ['aon', 'beyond', 'cat', 'flow', 'neptune', 'palomar', 'sterling', 'wright'];

        $data = [];

        foreach($agents as $agent){
            foreach($carriers as $carrier){
                $agent[$carrier] = json_decode($agent[$carrier]);
            }

            $rocketPlusValid = false;
            $carriersValid = false;

            // Filter Rocket Appointed Carriers
            foreach($request->filter['carriers'] as $carrier){
                if(!$carriersValid && $agent[$carrier['code']]->rocket){
                    $carriersValid = true;
                }
            }

            // Filter Rocket Plus Status
            foreach($request->filter['rocketPlus'] as $value){
                if(!$rocketPlusValid && $agent->rocketPlus == $value['code']){
                    $rocketPlusValid = true;
                }
            }

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
            'rocket_id'=> 'required',
            'rocketPlus'=> 'required',
            'aon'=> 'required',
            'beyond'=> 'required',
            'cat'=> 'required',
            'flow'=> 'required',
            'neptune'=> 'required',
            'palomar'=> 'required',
            'sterling'=> 'required',
            'wright'=> 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        if(react_sub_agents::where('rocket_id', $request->rocket_id)->exists()){
            $response = [
                'success'=> false,
                'message'=> 'Rocket ID already exists. Please update and try again.'
            ];
            
            return response()->json($response, 422);
        }

        $noCarriers = ['name', 'rocket_id', 'rocketPlus'];

        $inputs = $request->all();

        foreach($inputs as $key => $value){
            if(!in_array($key, $noCarriers)){
                $inputs[$key] = json_encode($value);
            }
        }

        $inputs['id'] = uniqid('react_subagent_');
        while(react_sub_agents::where('id', $inputs['id'])->exists()){
            $inputs['id'] = uniqid('react_subagent_');
        }

        $subagent = react_sub_agents::create($inputs);
        $subagent->save();

        $response = [
            'success'=> true,
            'message'=> 'New sub agent created successfully.'
        ];

        return response()->json($response, 200);
    }
    
    public function update(Request $request, $id){
        $subagent = react_sub_agents::find($id);

        $noCarriers = ['id', 'name', 'rocket_id', 'rocketPlus'];

        $inputs = $request->all();

        foreach($inputs as $key => $value){
            if(!in_array($key, $noCarriers)){
                $inputs[$key] = json_encode($value);
            }
        }

        $subagent->fill($inputs);
        $subagent->save();

        $response = [
            'success'=> true,
            'message'=> 'New sub agent details updated successfully.'
        ];

        return response()->json($response, 200);
    }

    public function report(Request $request, $id){
        $agency = react_sub_agents::find($id);

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
            $agency->$key->name = $value;

            $carrierPolicies = 0;
            $carrierPrem = 0;

            // Check to see if the carrier is Rocket Appointed and if it is pull all the policies attached to the carrier code
            if($agency->$key->rocket){
                $carrier_policies = react_commission_policies::where('carrier_id', $agency->$key->code)->get();

                $carrierPolicies = count($carrier_policies);

                foreach($carrier_policies as $policy){
                    $carrierPrem = $carrierPrem + $policy->prem;

                    array_push($agency_policies, $policy);
                }
            }

            $agency->$key->prem = (float) number_format($carrierPrem, 2, '.', '');
            $agency->$key->policies = $carrierPolicies;

            array_push($pieChart->labels, $value);
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
}
