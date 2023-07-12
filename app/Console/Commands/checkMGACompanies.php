<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\mga_companies;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class checkMGACompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:mga_companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all companies provided to see if they are in the mga_companies table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companies = json_decode(Storage::disk('local')->get('json/mga_companies.json'));

        Log::channel('mga_companies')->info('Check MGA Companies - '.count($companies).'; Admin Portal Count - '.count(mga_companies::get()));

        $created = 0;

        foreach($companies as $company){
            if(!mga_companies::where('rocket_id', $company->rocket_id)->exists()){
                $created += 1;

                $mga_data = json_decode(Http::get('https://backend.agentportal.rocketmga.com/api/services/client/get-mga-company-carriers/'.$company->rocket_id))->data;

                $newCompany = new mga_companies();
                $newCompany->rocket_id = $company->rocket_id;
                $newCompany->name = $mga_data->company_name;
                $newCompany->save();

                Log::channel('mga_companies')->info('New MGA Company '.$mga_data->company_name);
            }
        }

        Log::channel('mga_companies')->info('MGA Companies Added - '.$created);

        dd('success');
    }
}
