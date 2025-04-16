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
        
        $cards = Cache::remember( 'cards',600, function () use ($api){
            //$api = new GenetecApi();
            return $api->getCardHolders();
        } ) ;
        
        $doors = Cache::remember( 'doors',600, function () use ($api) {
            //$api = new GenetecApi();
            return $api->getDoors();
        } ) ;
        
        
        
        return view('index',compact('cards','doors'));
        
    }
}
