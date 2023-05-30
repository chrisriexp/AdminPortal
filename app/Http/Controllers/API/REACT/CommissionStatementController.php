<?php

namespace App\Http\Controllers\API\REACT;

use App\Http\Controllers\Controller;
use App\Models\react_commissions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helper\NotificationsHelper;
use App\Models\react_commission_policies;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\map;

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
            "if_nbs" => "0.155"
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
            "if_nbs" => "NB"
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
            "if_nbs" => "0"
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
            "if_nbs" => "NB"
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
            "if_nbs" => "0"
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
            "if_nbs" => "nbs"
        ]
    ];

    public function check(Request $request){
        $month = Carbon::now()->year."-".Carbon::now()->format('m');
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

    public function upload(Request $request){
        $month = Carbon::now()->year."-".Carbon::now()->format('m');
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
                "A ".$this->carriers[$request['type']]['name']." commission statement has already been uploaded for the month of ".Carbon::now()->format('M')." ".Carbon::now()->year,
                "uploaded_by"=> $request->user()->name
            ];
            
            Log::channel('commissions')->info(json_encode($logInfo));

            return response()->json($response, 400);
        }

        // Crete file name and Save file
        $file_name = $month.'_'.$request['type'].'.' . $request->file('file')->getClientOriginalExtension();
        $file_path = $request->file('file')->storeAs('', $file_name, 'commission_statements');
        ini_set('mbstring.substitute_character', "none");

        $path = storage_path().'/app/commission_statements/'.$file_path;
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());

        $data = [];
        $totalPrem = 0;
        $totalComm = 0;
        $totalOverride = 0;

        // Get every policy on the statement
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
            "body"=> "A new ". $this->carriers[$request['type']]['name'] ." commission statment has been uploaded by ".$request->user()->name." for ".Carbon::now()->format('M'). " " .Carbon::now()->year.". The commission statement has been processed successfully.",
            "type"=> "success",
            "system"=> "react"
        ]);

        // Create a new Log for the Commission Statement Upload
        $logInfo = [
            "Commission Statement Upload - Successful",
            Carbon::now()->format('M')." ".Carbon::now()->year,
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
}
