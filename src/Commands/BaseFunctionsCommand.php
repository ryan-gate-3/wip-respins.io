<?php

namespace Respins\BaseFunctions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BaseFunctionsCommand extends Command
{
    public $signature = 'base-functions {command}';

    public $description = 'Commands available';

    public function handle(): int
    {
        $arguments = $this->argument('seeder');
            foreach($arguments as $arg) {

            if($arg === 'migrate-reset')
            {
                $refresh = \Artisan::call('migrate:refresh');

                $this->comment($refresh);

            }
        }
        return self::SUCCESS;
    }
}