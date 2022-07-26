<?php

namespace Respins\BaseFunctions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class importerSQL extends Command
{
    public $signature = 'respins:importerSQL {seeder*}';

    public $description = 'Pick between local & external, for example respins:importerSQL external';

    public function handle(): int
    {

        $arguments = $this->argument('seeder');
            foreach($arguments as $arg) {

            if($arg === 'external')
            {
                /*
                $import = DB::unprepared(file_get_contents(''));
                */
                $this->comment('Disabled');
            }
        }

        return self::SUCCESS;
    }
}