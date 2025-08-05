<?php   

namespace App\Lib;
use Illuminate\Support\Facades\Cache ;

class GenetecApi {
    //put your code here
    
    protected $host = "192.168.1.69";
    protected $port = 4590;
    private $auth = "";
    protected $protocol = "http";
    
    public function __construct() {
        
        $this->auth = base64_encode("GecoAdmin;jo7n8pLpRYH0zb8VUWMhDy0Qnuz/SeCqMjgbY8aX6QdUSDGZQLPE84RCNvYPz29J:Gecoitalia0!");
        
    }
    
    private function getContent($url) {
        
        $host = $this->host;
        $port = $this->port;
        $protocol = $this->protocol;

        $url = "$protocol://$host:$port/$url";
        
        $auth = $this->auth;

        $context = stream_context_create([
            "http" => [
                "header" => "Authorization: Basic $auth\r\nAccept: text/json\r\n"
            ],
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        ]);
        //echo "Chiamo url $url".PHP_EOL;
        try{
            $homepage = file_get_contents($url, false, $context);
        
            $pos = strpos($homepage, '{', 1);

            $homepage = substr($homepage, $pos);
        } catch (\Exception $e){
            echo $e->getMessage()."Url: $url\n";exit;
        }
        
        return $homepage;
    }
    public function getEntity($LogicalId) {

        $url = "WebSdk/entity/$LogicalId";
        

        return $this->getContent($url);

    }
    
    public function getEntityFromCache($LogicalId) {

        $devices = Cache::rememberForever( 'devices',function () use ($LogicalId){
            //$api = new GenetecApi();
            $url = "WebSdk/entity/$LogicalId";
            $dev = json_decode($this->getContent($url));
            
            $res = [];
            
            if($dev->Rsp->Status=="Ok"){
            
                $res[$LogicalId] = $dev->Rsp->Result->Name;
                
            }
            
            return $res;
        } ) ;
        
        if(!array_key_exists($LogicalId, $devices)){
            
            $url = "WebSdk/entity/$LogicalId";
            $dev = json_decode($this->getContent($url));
            $res = [];
            if($dev->Rsp->Status=="Ok"){
            
                $res[$LogicalId] = $dev->Result->Name;
                Cache::put("devices", $res);
                
            }
            
            
            
        }
        
        $ret = Cache::get("devices");
        
        return $ret[$LogicalId];

    }
    
    public function getCardHolders() {
        
        $url = "WebSdk/report/EntityConfiguration?q=EntityTypes@Cardholder";
        
        $res = json_decode($this->getContent($url));
        
        $resp = [];
                
        if($res->Rsp->Status=="Ok"){
            
            
            
            foreach ($res->Rsp->Result as $val){
                
                $entity = json_decode($this->getEntity($val->Guid));
                                
                if($entity->Rsp->Status=='Ok')
                    $resp[$val->Guid] = $entity->Rsp->Result->Name;
                
            }
            
        }
        
        return $resp;
        
    }
    
    private function getCustomFileds($param,&$company,&$code) {
        
        foreach ($param as $custF){
            
            if($custF->Name=="Ditta Appartenenza")
                $company = $custF->Value;
            
            if($custF->Name=="Codice Dipendente")
                $code = $custF->Value;
            
        }
        
    }


    public function getCredential() {
        
        $url = "WebSdk/report/EntityConfiguration?q=EntityTypes@Credential";
        
        $res = json_decode($this->getContent($url));
        
        $resp = [];
                
        if($res->Rsp->Status=="Ok"){
            $inc = 0;
            $sel = 0;
            
            foreach ($res->Rsp->Result as $val){
                
                echo date("H:i:s")." -  Analizzo $sel".PHP_EOL;
                $sel++;
                
                $entity = json_decode($this->getEntity($val->Guid));
                //echo "<pre>";
                //echo $val->Guid."<br />".$entity->Rsp->Result->Cardholder->Status->State."<br />";
                //print_r($entity);exit;
                if($entity->Rsp->Status=='Ok'){
                    
                    if(isset( $entity->Rsp->Result->Cardholder )){
                    
                        if($entity->Rsp->Result->Cardholder->Status->State=="Active"){

                            $this->getCustomFileds($entity->Rsp->Result->Cardholder->CustomFields, $company, $code);
                            //$resp[$company] = [];
                            $resp[$company][$entity->Rsp->Result->Cardholder->Guid]["name"] = $entity->Rsp->Result->Cardholder->LastName." ".$entity->Rsp->Result->Cardholder->FirstName;
                            $resp[$company][$entity->Rsp->Result->Cardholder->Guid]["cardid"] = $entity->Rsp->Result->Format->CardId;
                            $resp[$company][$entity->Rsp->Result->Cardholder->Guid]["code"] = $code;
                            /*
                            foreach ($entity->Rsp->Result->Cardholder->CustomFields as $custF){

                                
                                
                                if($custF->Name=="Ditta Appartenenza" || $custF->Name=="Codice Dipendente"){

                                    if(!isset($resp[$custF->Value])){

                                        $resp[$custF->Value] = [];
                                        //$resp[$custF->Value][$inc] = [];



                                    }
                                    $resp[$custF->Value][$entity->Rsp->Result->Cardholder->Guid]["name"] = $entity->Rsp->Result->Cardholder->FirstName." ".$entity->Rsp->Result->Cardholder->LastName;
                                    $resp[$custF->Value][$entity->Rsp->Result->Cardholder->Guid]["cardid"] = $entity->Rsp->Result->Format->CardId;
                                    
                                    if($custF->Name=="Codice Dipendente"){
                                        $resp[$custF->Value][$entity->Rsp->Result->Cardholder->Guid]["code"] = $custF->Value;
                                    }
                                    
                                    //$resp[$custF->Value][$inc]["id"] = $val->Guid ;
                                    

                                    $inc++;

                                }

                            }
                             * 
                             */
                            //dump($resp);exit;
                            //echo "Analizzo: ".$entity->Rsp->Result->Name.PHP_EOL;


                        }
                    }
                    
                    
                    //$resp[$val->Guid] = $entity->Rsp->Result->Name;
                    
                }
                
            }
            
        }
        
        return $resp;
        
        
    }
    
    public function getCardholderGroup() {
        
        $url = "WebSdk/report/EntityConfiguration?q=EntityTypes@CardholderGroup";
        
        $res = json_decode($this->getContent($url));
        
        $res = json_decode($this->getContent($url));
        
        $resp = [];
                
        if($res->Rsp->Status=="Ok"){
            
            foreach ($res->Rsp->Result as $val){
                
                $entity = json_decode($this->getEntity($val->Guid));
                dump($entity);
                if($entity->Rsp->Status=='Ok')
                    $resp[$val->Guid] = $entity->Rsp->Result->Name;
                
                $members = $entity->Rsp->Result->Members;
                
                if(count($entity->Rsp->Result->Members)){
                    
                    
                }
                
            }
            
        }
        
        
        return $resp;
        
        
        
    }
    
    public function getDoors() {
        
        $url = "WebSdk/report/EntityConfiguration?q=EntityTypes@Door";
        
        $res = json_decode($this->getContent($url));
        
        $resp = [];
        echo count($res->Rsp->Result).PHP_EOL;
        
        if($res->Rsp->Status=="Ok"){
            
            foreach ($res->Rsp->Result as $val){
                
                $entity = json_decode($this->getEntity($val->Guid));
                
                if($entity->Rsp->Status=='Ok')
                    $resp[$val->Guid] = $entity->Rsp->Result->Name;
                
            }
            
        }
        
        return $resp;
    }
    public function getReport($param) {
        
        /****
         * usare solo il 
         * RAFFINERIA TRECATE
         * f00843a3-1dba-421a-880e-23851725783c
         * 
         */
        $dateEnd = date("Y-m-d", strtotime($param["start"])+86400);
        //$url = "WebSdk/report/DoorActivity?q=Doors@".$param["door"].",TimeRange.SetTimeRange(".$param["start"]."T00:00:00,".$param["end"]."T23:59:59)";
        $url = "WebSdk/report/TimeAttendanceActivity?q=Areas@f00843a3-1dba-421a-880e-23851725783c,Cardholders@".$param["cardholder"].",TimeRange.SetTimeRange(".$param["start"]."T00:00:00,".$param["start"]."T23:59:59)";
        //echo $url. PHP_EOL;//exit;
        
        $res = $this->getContent($url);
        
        return json_decode($res,true);
        /*
        if($list->Rsp->Status=='Ok'){
            foreach ($list->Rsp->Result as $item){
                //echo $item->EventType."<br />";
                if($item->EventType==35){
                    print_r($item);
                    echo date("Y-m-d H:i", strtotime($item->Timestamp))."<br />";
                    echo Cache::get("doors")[$item->AccessPointGroupGuid]."<br />";
                    echo Cache::get("cards")[$item->CardholderGuid]."<br />";
                    
                    echo $this->getEntityFromCache($item->SourceGuid)."<br /><br />";
                    exit;

                }


            }
        }
         * 
         */
        
    }
    public function getAreas() {
        
        $url = "WebSdk/report/EntityConfiguration?q=EntityTypes@Area";
        
        $res = json_decode($this->getContent($url));
        
        $resp = [];
        echo count($res->Rsp->Result).PHP_EOL;
        
        if($res->Rsp->Status=="Ok"){
            
            foreach ($res->Rsp->Result as $val){
                
                $entity = json_decode($this->getEntity($val->Guid));
                
                if($entity->Rsp->Status=='Ok')
                    $resp[$val->Guid] = $entity->Rsp->Result->Name;
                
            }
            
        }
        
        return $resp;
    }
    
}
