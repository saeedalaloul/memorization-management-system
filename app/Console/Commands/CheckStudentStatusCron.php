<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckStudentStatusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckStudentStatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Check Student Status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

    }
}
