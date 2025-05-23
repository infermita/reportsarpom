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
        
        if($request->all()){
            
            $api->getReport($request->all());
            
        }
        //dump($api->getCardholdcderGroup());exit;
        dump(json_decode($api->getEntity("498d16e4-9a87-4c9b-a1f8-526836b985a9")));exit;
        
                
        
        $company = Cache::remember( 'credentials',600, function () use ($api){
            //$api = new GenetecApi();
            return $api->getCredential();
        } ) ;
        $doors = Cache::remember( 'doors',600, function () use ($api) {
            //$api = new GenetecApi();
            return $api->getDoors();
        } ) ;
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
        
        return view('index',compact('company','doors'));
        
    }
}
