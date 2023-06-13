<?php

namespace App\Http\Controllers\API\REACT;

use App\Http\Controllers\Controller;
use App\Models\react_commissions;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helper\NotificationsHelper;
use App\Models\mga_companies;
use App\Models\react_commission_policies;
use Illuminate\Support\Facades\Log;

class CommissionStatementController extends Controller
{
    public $carriers = [
        "aon" => [
            "name" => "Aon Edge",
            "comm" => [
                "NBS" => 0.135,
                "RWL" => 0.12
            ],
            "override" => [
                "NBS" => 0.02,
                "RWL" => 0.02
            ],
            "trans_types" => [
                "New Business" => "NBS",
                "Renewal" => "RWL",
                "Reinstatement" => "REI",
                "Endorsement" => "PCH",
                "Cancel-Rewrite" => "REW",
                "Cancellation"=> "XLC"
            ],
            "if_nbs" => "0.155",
            "rocketPay" => true
        ],
        "beyond" => [
            "name" => "Beyond Flood",
            "comm" => [
                "NBS" => 0.13,
                "RWL" => 0.13
            ],
            "override" => [
                "NBS" => 0.03,
                "RWL" => 0.02
            ],
            "trans_types" => [
                "NB" => "NBS",
                "RB" => "RWL",
                "CN"=> "XLC"
            ],
            "if_nbs" => "NB",
            "rocketPay" => true
        ],
        "flow" => [
            "name" => "Flow Flood",
            "comm" => [
                "NBS" => 0.15,
                "RWL" => 0.10
            ],
            "override" => [
                "NBS" => 0.02,
                "RWL" => 0.05
            ],
            "trans_types" => [
                "FLOOD - NB" => "NBS",
                "FLOOD - RN" => "RWL",
                "FLOOD - END" => "PCH",
                "FLOOD - CXL"=> "XLC"
            ],
            "if_nbs" => "0",
            "rocketPay" => true
        ],
        "neptune" => [
            "name" => "Neptune",
            "comm" => [
                "NBS" => 0.12,
                "RWL" => 0.12
            ],
            "override" => [
                "NBS" => 0.02,
                "RWL" => 0.02
            ],
            "trans_types" => [
                "N" => "NBS",
                "R" => "RWL",
                "E" => "PCH",
                "C"=> "XLC",
                "P"=> "XLC"
            ],
            "if_nbs" => "NB",
            "rocketPay" => false
        ],
        "palomar" => [
            "name" => "Palomar",
            "comm" => [
                "NBS" => 0.15,
                "RWL" => 0.15
            ],
            "override" => [
                "NBS" => 0.025,
                "RWL" => 0.025
            ],
            "trans_types" => [
                "New Policy" => "NBS",
                "Rewrite – New Policy" => "NBS",
                "Accept Renewal Quote" => "RWL",
                "Reissue – Renewal" => "RWL",
                "Reinstate Policy" => "REI",
                "Reissue" => "REW",
                "Amend – Policy Change" => "PCH",
                "Cancel Flat – Void"=> "XLC",
                "Insured Requested Cancellation"=> "XLC"
            ],
            "if_nbs" => "0",
            "rocketPay" => true
        ],
        "sterling" => [
            "name" => "Sterling",
            "comm" => [
                "NBS" => 0.10,
                "RWL" => 0.10
            ],
            "override" => [
                "NBS" => 0.025,
                "RWL" => 0.025
            ],
            "trans_types" => [
                "nbs" => "NBS",
                "ren" => "RWL",
                "add" => "PCH",
                "ret" => "PCH",
                "cxl"=> "XLC",
            ],
            "if_nbs" => "nbs",
            "rocketPay" => false
        ]
    ];

    public function check(Request $request, $month){
        $data = (object) [];

        foreach($this->carriers as $key=>$value){
            $data->{$key} = react_commissions::where('statement_id', $month.'_'.$key)->exists();;
        }

        $response = [
            'success'=> true,
            'carriers'=> $data
        ];

        return response()->json($response, 200);
    }

    public function upload(Request $request, $month){
        $reader = IOFactory::createReader('Xlsx');

        // Check to see if an ID for for statement already exists
        if(react_commissions::where('statement_id', $month.'_'.$request['type'])->exists()){
            $response = [
                'success' => false,
                'message'=> "A commission statement has already been uploaded for the month of ".$month." for ". $this->carriers[$request['type']]['name']
            ];

            // Log Unsuccessful Upload
            $logInfo = [
                "Commission Statement Upload - Unsuccessful",
                "A ".$this->carriers[$request['type']]['name']." commission statement has already been uploaded for the month of ".Carbon::parse($month)->format('M')." ".Carbon::parse($month)->year,
                "uploaded_by"=> $request->user()->name
            ];
            
            Log::channel('commissions')->info(json_encode($logInfo));

            return response()->json($response, 400);
        }

        // Crete file name and Save file
        $file_name = $month.'_'.$request['type'].'.' . $request->file('file')->getClientOriginalExtension();
        $file_path = $request->file('file')->storeAs('commission_statements', $file_name, 'react');
        ini_set('mbstring.substitute_character', "none");

        $path = storage_path().'/app/react/'.$file_path;
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());

        $data = [];
        $totalPrem = 0;
        $totalComm = 0;
        $totalOverride = 0;

        // Get every policy on the statement skipping row 1
        foreach($sheet->getRowIterator() as $row){
            if($row->getRowIndex() == 1){
                continue;
            }

            $cells = iterator_to_array($row->getCellIterator("A", "H"));

            $policy = (object) [
                "carrier_id"=> $cells['A']->getValue(),
                "policy"=> $cells['B']->getValue(),
                "insured"=> $cells['C']->getValue(),
                "prem"=> (float) number_format($cells["D"]->getValue(), 2, '.', ''),
                "eff"=> date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cells["F"]->getValue())),
                // Check to see if the value of the Trans Type column is in the "trans_types" array for the carrier if its is then map the value else the value is null
                "trans_type"=> array_key_exists($cells['G']->getValue(), $this->carriers[$request['type']]['trans_types']) ? $this->carriers[$request['type']]['trans_types'][$cells['G']->getValue()] : null,
                "month"=> $month,
                "carrier"=> $request['type']
            ];

            $policy->policy_type = $this->carriers[$request['type']]['if_nbs'] == (string) $cells['H']->getValue() ? 'NBS' : 'RWL';
            $policy->comm = (float) number_format($policy->prem * $this->carriers[$request['type']]['comm'][$policy->policy_type], 2, '.', '');
            $policy->override = (float) number_format($policy->prem * $this->carriers[$request['type']]['override'][$policy->policy_type], 2, '.', '');

            array_push($data, $policy);
            $totalPrem = $totalPrem + $policy->prem;
            $totalComm = $totalComm + $policy->comm;
            $totalOverride = $totalOverride + $policy->override;
        }

        // Add every policy to the database
        foreach($data as $item){
            $inputs = (array) $item;
            $newPolicy = new react_commission_policies();
            $newPolicy->fill($inputs);
            $newPolicy->save();
        }

        // Add new entry to commission table
        $commission = new react_commissions();
        $commission->statement_id = $month.'_'.$request['type'];
        $commission->carrier = $request['type'];
        $commission->month = $month;
        $commission->override = (float) number_format($totalOverride, 2, '.', '');
        $commission->prem = (float) number_format($totalPrem, 2, '.', '');
        $commission->comm = (float) number_format($totalComm, 2, '.', '');
        $commission->policies = count($data);
        $commission->uploaded_by = $request->user()->name;
        $commission->save();

        // Send Notification to all system Super Admins that a new commission statement was uploaded
        (new NotificationsHelper)->createSuperAdminNotification((object) [
            "header"=> "REACT - Commission Statement Upload",
            "body"=> "A new ". $this->carriers[$request['type']]['name'] ." commission statment has been uploaded by ".$request->user()->name." for ".Carbon::parse($month)->format('M'). " " .Carbon::parse($month)->year.". The commission statement has been processed successfully.",
            "type"=> "success",
            "system"=> "react"
        ]);

        // Create a new Log for the Commission Statement Upload
        $logInfo = [
            "Commission Statement Upload - Successful",
            Carbon::parse($month)->format('M')." ".Carbon::parse($month)->year,
            $this->carriers[$request['type']]['name'],
            "override"=> (float) number_format($totalOverride, 2, '.', ''),
            "comm"=> (float) number_format($totalComm, 2, '.', ''),
            "prem"=> (float) number_format($totalPrem, 2, '.', ''),
            "policies"=> count($data),
            "uploaded_by"=> $request->user()->name
        ];

        Log::channel('commissions')->info(json_encode($logInfo));

        $response = [
            'success'=> true,
            'message'=> $this->carriers[$request['type']]['name'].' commission statement processed successfully.'
        ];

        return response()->json($response, 200);
    }

    public function month_report(Request $request, $month){
        $rocketPayCarriers = [];

        foreach($this->carriers as $key=>$value){
            if($value['rocketPay']){
                array_push($rocketPayCarriers, $key);
            }
        }

        $mga_companies = mga_companies::orderBy('created_at', 'asc')->get();
        $month_policies = react_commission_policies::where('month', $month)->whereIn('carrier', $rocketPayCarriers)->get();
        $data = [];
        $totalPolicies = 0;
        $totalComm = 0;
        $totalOverride = 0;

        foreach($mga_companies as $agency){
            $agency_data = (object) [
                "name"=> $agency->name,
                "rocket_id"=> $agency->rocket_id,
                "policies"=> 0,
                "prem"=> 0,
                "comm"=> 0,
            ];

            // Find the carrier policies that Rocket Pay
            foreach($rocketPayCarriers as $carrier){
                $carrier_data = json_decode($agency->$carrier);

                $carrier_policies = react_commission_policies::where('month', $month)->where('carrier_id', $carrier_data->commission_id)->get();

                foreach($carrier_policies as $policy){
                    $agency_data->policies += 1;
                    $agency_data->prem = $agency_data->prem + $policy->prem;
                    $agency_data->comm = $agency_data->comm + $policy->comm;

                    // Remove from Month Policies Array
                    foreach($month_policies as $mpKey=>$mpValue){
                        if($policy->id == $mpValue->id){
                            // Increase Totoal Policies, Comm, and Override
                            $totalPolicies += 1;
                            $totalComm = $totalComm + $policy->comm;
                            $totalOverride = $totalOverride + $policy->override;

                            unset($month_policies[$mpKey]);
                        }
                    }
                }
            }

            $agency_data->prem = (float) number_format($agency_data->prem, 2, '.', '');
            $agency_data->comm = (float) number_format($agency_data->comm, 2, '.', '');

            if($agency_data->comm > 0){
                array_push($data, $agency_data);
            }
        }

        $na_policies = (object) [
            "name"=> "Unassociated Commissions",
            "rocket_id"=> "00000",
            "policies"=> 0,
            "prem"=> 0,
            "comm"=> 0,
        ];

        // Find the Un Associated carrier policies that Rocket Pay
        foreach($rocketPayCarriers as $carrier){
            foreach($month_policies as $policy){
                if($policy->carrier == $carrier){
                    $na_policies->policies += 1;
                    $na_policies->prem = $na_policies->prem + $policy->prem;
                    $na_policies->comm = $na_policies->comm + $policy->comm;

                    // Increase Totoal Policies, Comm, and Override
                    $totalPolicies += 1;
                    $totalComm = $totalComm + $policy->comm;
                    $totalOverride = $totalOverride + $policy->override;
                }
            }
        }

        $na_policies->prem = (float) number_format($na_policies->prem, 2, '.', '');
        $na_policies->comm = (float) number_format($na_policies->comm, 2, '.', '');

        array_push($data, $na_policies);
        

        $response = [
            'success'=> true,
            'message'=> 'Please see the below statements for the month of '.$month,
            'commissions'=> array_reverse($data, false),
            'policies'=> $totalPolicies,
            'comm'=> (float) number_format($totalComm, 2, '.', ''),
            'override'=> (float) number_format($totalOverride, 2, '.', '')
        ];

        return response()->json($response, 200);
    }

    public function download_month(Request $request, $month){
        $collection = react_commission_policies::where('month', $month)->get(['policy', 'prem', 'comm']);

        $data = [
            "today"=> Carbon::now()->format('D, M d, Y'),
            "month"=> Carbon::parse($month)->format('M')." ".Carbon::parse($month)->year,
            'collection'=> $collection
        ];

        $PDF = PDF::loadView('PDF/commission', $data);

        return $PDF->download('pdf_file.pdf');
    }
}
