<?php

namespace App\Console\Commands;

use App\Jobs\ExtractCountryInfoJob;
use Illuminate\Console\Command;

class testComand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send drip e-mails to a user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    public function handle()
    {
        (new ExtractCountryInfoJob(['es','gb']))->handle();
    }
}