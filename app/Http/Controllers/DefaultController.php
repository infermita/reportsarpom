<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache ;

use Illuminate\Http\Request;
use App\Lib\GenetecApi;

class DefaultController extends Controller
{
    //
    public function index(Request $request){
        
        $api = new GenetecApi();
        
        $companySel = "";
        
        $company = Cache::get('credentials') ;
        
        ksort($company);
        
        $res =[];
        
        if($request->all()){
            $usersList = [];
            
            foreach ($company as $key => $users){
                
                $guids = implode('@', array_keys($users));
                
                if($guids==$request->all()["cardholder"]){
                    
                    $companySel = $key;
                    
                }
                
                foreach ($users as $key => $user){
                    $usersList[$key] = $user;
                }
                
            }
            
            $res = $api->getReport($request->all());
            
            foreach ($res["Rsp"]["Result"] as $key => $users){
                
                $res["Rsp"]["Result"][$key]["Name"] = $usersList[  $res["Rsp"]["Result"][$key]["CardholderGuid"] ];
                
            }
            $res = $res["Rsp"]["Result"];
            //echo "<pre>";
            //print_r($res);exit;
            
            
        }
                        
        //dump($api->getCardholdcderGroup());exit;
        //dump(json_decode($api->getEntity("498d16e4-9a87-4c9b-a1f8-526836b985a9")));exit;
        
                
        
        
        
        //$doors = Cache::get( 'doors' ) ;
        
        $areasC = Cache::get( 'areas' ) ;
        
        $areas = [];
        
        foreach ($areasC as $key => $value) {
            if($value == "RAFFINERIA TRECATE"){
                
                $areas[$key] = $value;
                
            }
        }
        /*
        $credentials = Cache::remember( 'credentials',600, function () use ($api){
            //$api = new GenetecApi();
            return $api->getCredential();
        } ) ;
        
        $cards = Cache::remember( 'cards',600, function () use ($api){
            //$api = new GenetecApi();
            return $api->getCardHolders();
        } ) ;
        $doors = Cache::remember( 'doors',600, function () use ($api) {
            //$api = new GenetecApi();
            return $api->getDoors();
        } ) ;
        /*
        dump($cards);
        $api->getCardholderGroup();
        $u =  $api->getEntity("00000000-0000-0000-0000-000000000000");
        $u1 = $api->getEntity("28549ace-3808-4314-b440-cc29e1089e1d");
        dump(json_decode($u));
        dump(json_decode($u1));exit;
        */
        
        return view('index',compact('company','areas','res','request','companySel'));
        
    }
}
