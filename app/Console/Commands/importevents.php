<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Eventtype;
use App\Lib\GenetecApi;
use Illuminate\Support\Facades\Cache ;

class importevents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:importevents';

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
        $api = new GenetecApi();
        $credentials = Cache::rememberForever( 'credentials',function () use ($api){
            //$api = new GenetecApi();
            return $api->getCredential();
        } ) ;
        
        $doors = Cache::rememberForever( 'doors',function () use ($api) {
            //$api = new GenetecApi();
            return $api->getDoors();
        } ) ;
        
        $areas = Cache::rememberForever( 'areas',function () use ($api) {
            //$api = new GenetecApi();
            return $api->getAreas();
        } ) ;
        
        print_r($areas);
        
        /*
        $path = base_path();
        
        $row = 0;
        $found = 0;
        
        if (($handle = fopen($path."/EventTypes.txt", "r")) !== FALSE) {
            
            while (($data = fgetcsv($handle, 1000, ":")) !== FALSE) {
                $num = count($data);
                if($num > 1) {
                    
                    $item = [];
                    $item["name"] = $data[1];
                    $item["id"] = $data[0];
                    $e = new Eventtype();
                    $e->id = $data[0];
                    $e->name = $data[1];
                    $e->save();
                    
                }
                
            }
            
        }
        * 
        */
    }
}
