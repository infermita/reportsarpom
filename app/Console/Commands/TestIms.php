<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Lib\GenetecApi;

class TestIms extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-ims';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private function normalize($str) {
        $str = strtolower($str);
        $str = trim($str);
        
        //$str = str_replace(["s.r.l.","s.n.c.","s.p.a.","sas"], "", $str);
        
        $str = preg_replace('/[^a-z0-9 ]/', '', $str); // rimuove simboli
        $str = str_replace(["srl","snc","spa","sas","arl"," "], "", $str);
        //echo "qua: $str\n";
        return $str;
    }
    private function bestMatch($rowIms, $haystack) {
        $best = null;
        $bestScore = -1;

        $needle = $this->normalize($rowIms["DITTA"]);
        $imsfirstname = $this->normalize($rowIms["NOME"]);
        $imslastname = $this->normalize($rowIms["COGNOME"]);

        foreach ($haystack as $item => $val) {
            
            $item = $this->normalize($item);

            //echo $item." ".$needle.PHP_EOL;
            /*
            similar_text($needle, $item, $percent);
            $distance = levenshtein($needle, $item);

            // formula combinata (puoi modificarla)
            $score = $percent - $distance;
            */
            //if ($score > $bestScore) {
            if($item == $needle){
                //$bestScore = $score;
                //$best = $item;
                foreach ($val as $guid => $person){

                    $firstname = $this->normalize($person["firstname"]);
                    $lastname = $this->normalize($person["lastname"]);
                    
                    if($imsfirstname == $firstname && $imslastname==$lastname && $rowIms["UIDGENETEC"]==""){

                        $rowIms["UIDGENETEC"] = $guid;
                        $rowIms["BADGE"] = $person["cardid"];
                        $rowIms["DATASCADENZA"] = $person["expiredate"];
                        $rowIms["ABILITATO"] = $person["enabled"];

                        return $rowIms;


                    }

                }
            }
        }

        return false;
    }

    /**
     * Execute the console command.
     */
    public function handle() {

        //7c33853a-cce7-41c0-b85c-b0f7d382e105 guid ditta
        
        $api = new GenetecApi();

        $credentials = Cache::rememberForever('all_credentials', function () use ($api) {
	    //$api = new GenetecApi();
	    return $api->getAllCredential();
	});
        //$api->createCardHolder(1,"","");
        


	$response = Http::withBasicAuth('apigenetec', 'Gen26#7762!aX')->post('https://ims.sarpomapp.com/api/genetec/getnuovicontrattori');
	//$response = Http::withBasicAuth('apigenetec', 'Gen26#7762!aX')->post('https://ims.sarpomapp.com/api/genetec/getcontrattori');
        //echo $response;
        $json = json_decode($response,true);
        
	$companyC = Cache::get('all_credentials');
        $notCExist = [];
        $exist = [];
        
	foreach ($response->json() as $single) {

            $imsDitta = $this->normalize($single["DITTA"]);
            
            $match = $this->bestMatch($single, $companyC);
            if($match){

                $exist[] = $match;
                
                
            }else{
                //if(!in_array($imsDitta, $notCExist)){
                    $notCExist[] = $single;
                //}
            }

        }
        //asort($notCExist);
        //echo count($notCExist);
        //asort($exist);
        //echo json_encode($exist);
        if(count($exist)){
            $response = Http::withBasicAuth('apigenetec', 'Gen26#7762!aX')
                        ->withBody(json_encode($exist), 'application/json')
                        ->post('https://ims.sarpomapp.com/api/genetec/aggiornadaticontrattori');
            echo $response;
        }
        print_r($notCExist[0]);
        $api->createCardHolder($notCExist[0]["IDIMS"], $notCExist[0]["NOME"], $notCExist[0]["COGNOME"],$notCExist[0]["DITTA"]);
        
	
    }
}
