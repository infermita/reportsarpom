<?php

namespace App\Lib;
use Illuminate\Support\Facades\Cache ;

class GenetecApi {
    //put your code here
    
    protected $host = "192.168.1.250";
    protected $port = 4590;
    private $auth = "";
    protected $protocol = "http";
    
    public function __construct() {
        
        $this->auth = base64_encode("admin;jo7n8pLpRYH0zb8VUWMhDy0Qnuz/SeCqMjgbY8aX6QdUSDGZQLPE84RCNvYPz29J:Gecoitalia0!");
        
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
        $homepage = file_get_contents($url, false, $context);
        
        $pos = strpos($homepage, '{', 1);

        $homepage = substr($homepage, $pos);
        
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

                            foreach ($entity->Rsp->Result->Cardholder->CustomFields as $custF){

                                if($custF->Name=="Ditta Appartenenza"){

                                    if(!isset($resp[$custF->Value])){

                                        $resp[$custF->Value] = [];
                                        //$resp[$custF->Value][$inc] = [];



                                    }
                                    $resp[$custF->Value][$entity->Rsp->Result->Cardholder->Guid] = $entity->Rsp->Result->Cardholder->FirstName." ".$entity->Rsp->Result->Cardholder->LastName;
                                    //$resp[$custF->Value][$inc]["id"] = $val->Guid ;

                                    $inc++;

                                }

                            }
                            //dump($resp);
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
        
        //$url = "WebSdk/report/DoorActivity?q=Doors@".$param["door"].",TimeRange.SetTimeRange(".$param["start"]."T00:00:00,".$param["end"]."T23:59:59)";
        $url = "WebSdk/report/TimeAttendanceActivity?q=Areas@b14150e7-24e3-4092-b0f9-4c17c34ebb9a,TimeRange.SetTimeRange(".$param["start"]."T00:00:00,".$param["end"]."T23:59:59)";
        //echo $url;exit;
        
        $res = $this->getContent($url);
        echo "<pre>";
        $list = json_decode($res);
        
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
