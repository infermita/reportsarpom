<?php

namespace App\Lib;

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

        $homepage = file_get_contents($url, false, $context);

        $pos = strpos($homepage, '{', 1);

        $homepage = substr($homepage, $pos);

        return $homepage;
    }
    public function getEntity($LogicalId) {

        $url = "WebSdk/entity/$LogicalId";
        

        return $this->getContent($url);

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
    
    public function getDoors() {
        
        $url = "WebSdk/report/EntityConfiguration?q=EntityTypes@Door";
        
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
    public function getReport($param) {
        
        $url = "WebSdk/report/DoorActivity?q=Doors@".$param["door"].",TimeRange.SetTimeRange(".$param["start"]."T00:00:00,".$param["end"]."T23:59:59),Events@AccessGranted";
        //echo $url;exit;
        
        $res = $this->getContent($url);
        echo "<pre>";
        $list = json_decode($res);
        if($list->Rsp->Status=='Ok'){
            foreach ($list->Rsp->Result as $item){

                if($item->EventType==35){
                    print_r($item);
                    echo date("Y-m-d H:i", strtotime($item->Timestamp));
                    print_r(json_decode($this->getEntity($item->SourceGuid)));
                    exit;

                }


            }
        }
        
    }
    
}
