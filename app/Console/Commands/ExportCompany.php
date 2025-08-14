<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ExportCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $companyC = Cache::get('credentials');
        ksort($companyC);
        $ccrtl = "";
        foreach ($companyC as $key => $values) {
            
            if($ccrtl!=$key){
                echo PHP_EOL;
                $ccrtl = $key;
            }
            
            
            foreach ($values as $value) {
                
                if(substr($key, strlen($key)-1)==" "){
                        
                    echo $key."S,".$value["name"].PHP_EOL;
                    
                }else{
                
                    echo $key.",".$value["name"].PHP_EOL;
                }
            }
            
        }
    }
}
