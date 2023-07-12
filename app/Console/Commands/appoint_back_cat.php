<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class appoint_back_cat extends Command
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
        dd('success');
    }
}
