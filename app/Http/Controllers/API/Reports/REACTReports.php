<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use App\Models\mga_companies;
use App\Models\react_commission_policies;
use App\Models\react_commissions;
use App\Models\react_sub_agents;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class REACTReports extends Controller
{
    public $carriers = [
        "aon"=> "AON Edge",
        "beyond"=> "Beyond Flood",
        "cat"=> "Cat Coverage",
        "flow"=> "Flow Flood",
        "neptune"=> "Neptune",
        "palomar"=> "Palomar",
        "sterling"=> "Sterling",
        "wright"=> "Wright National"
    ];

    public function index(Request $request){
        $statements = react_commissions::get();

        $totalPrem = 0;
        $totalOverride = 0;
        $totalPolicies = 0;

        foreach($statements as $statement){
            $totalPrem = $totalPrem + $statement->prem;
            $totalOverride = $totalOverride + $statement->override;
            $totalPolicies = $totalPolicies + $statement->policies;
        }

        $months = [];
        foreach(react_commissions::select('month')->distinct()->get() as $key=>$value){
            array_push($months, $value->month);
        }

        $lineChart = (object) [
            "labels"=> $months,
            "datasets"=> [
                (object) [
                    "label"=> "Override Revenue",
                    "borderColor"=> "rgba(167, 120, 229, 1)",
                    "data"=> []
                ]
            ]
        ];

        $revenue = [];

        foreach($months as $month){
            $month_statements = react_commissions::where('month', $month)->get();

            $monthPrem = 0;
            $monthOverride = 0;
            $monthPolicies = 0;

            foreach($month_statements as $statement){
                $monthPrem = $monthPrem + $statement->prem;
                $monthOverride = $monthOverride + $statement->override;
                $monthPolicies = $monthPolicies + $statement->policies;
            }

            array_push($lineChart->datasets[0]->data, $monthOverride);
            array_push($revenue, [
                'month'=> $month,
                'override'=> $monthOverride
            ]);
        }


        $pieChart = (object) [
            "labels"=> [],
            "datasets"=> [
                (object) [
                    "data"=> []
                ]
            ]
        ];

        $carriers = [];

        foreach($this->carriers as $key=>$value){
            $carrier_statements = react_commissions::where('carrier', $key)->get();

            $carrierPrem = 0;
            $carrierOverride = 0;
            $carrierPolicies = 0;

            foreach($carrier_statements as $statement){
                $carrierPrem = $carrierPrem + $statement->prem;
                $carrierOverride = $carrierOverride + $statement->override;
                $carrierPolicies = $carrierPolicies + $statement->policies;
            }

            array_push($pieChart->labels, $value);
            array_push($pieChart->datasets[0]->data, $carrierPrem);

            array_push($carriers, [
                'name'=> $value,
                'prem'=> (float) number_format($carrierPrem, 2, '.', ''),
                'override'=> (float) number_format($carrierOverride, 2, '.', ''),
                'policies'=> $carrierPolicies
            ]);
        }

        $barGraph = (object) [
            "labels"=> [],
            "datasets"=> [
                (object) [
                    "label"=> "Total Override Revenue",
                    "backgroundColor"=> "rgba(167, 120, 229, 1)",
                    "data"=> []
                ]
            ]
        ];

        $sub_agents = mga_companies::get();
        $sub_agent_rev = collect([]);

        foreach($sub_agents as $agency){

            $override = 0;
            foreach($this->carriers as $key=>$value){
                $carrier = json_decode($agency->{$key});

                $agency_policies = react_commission_policies::where('carrier_id', $carrier->commission_id)->get();
                foreach($agency_policies as $policy){
                    $override = $override + $policy->override;
                }
            }

            $sub_agent_rev->push((object) [
                "name"=> $agency->name,
                "override"=> (float) number_format($override, 2, '.', '')
            ]);
        }
        
        $subAgentArr = $sub_agent_rev->sortByDesc(function ($element) {
            return $element->override;
        })->values()->toArray();

        $subAgentData = array_slice($subAgentArr, 0, 10);

        foreach($subAgentData as $agency){
            array_push($barGraph->labels, $agency->name);
            array_push($barGraph->datasets[0]->data, $agency->override);
        }
        
        $response = [
            'success'=> true,
            'message'=> 'Request Successful.',
            'data'=> [
                'total_prem'=> (float) number_format($totalPrem, 2, ',', ''),
                'avg_prem'=> (float) number_format($totalPrem / $totalPolicies, 2, ',', ''),
                'total_override'=> (float) number_format($totalOverride, 2, ',', ''),
                'avg_monthly_override'=> (float) number_format($totalOverride / count($months), 2, ',', ''),
                'total_policies'=> $totalPolicies,
                'avg_monthly_policies'=> (float) number_format($totalPolicies / count($months), 2, ',', ''),
                'carriers'=> $carriers,
                'pieChart'=> $pieChart,
                'lineChart'=> $lineChart,
                'revenue'=> $revenue,
                'sub_agent_rev'=> $subAgentData,
                'barGraph'=> $barGraph
            ]
        ];

        return response()->json($response, 200);
    }
}
