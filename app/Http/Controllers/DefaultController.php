<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache ;

use Illuminate\Http\Request;
use App\Lib\GenetecApi;
use App\Lib\ReportExport;
use App\Lib\ElaborateResult;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\MailingList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DefaultController extends Controller
{
    //
    public function index(Request $request){
        
        //return Excel::download(new ReportExport, "prova.xls");
        
        $api = new GenetecApi();
        //$api->getCredential();
        
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
                    
                    $usersList = $users;
                    
                    break;
                    
                    
                }
                
                
                
                
                
            }
            
            $res = $api->getReport($request->all());
            /*
            foreach ($res["Rsp"]["Result"] as $key => $users){
                
                $res["Rsp"]["Result"][$key]["Name"] = $usersList[  $res["Rsp"]["Result"][$key]["CardholderGuid"] ];
                
            }
            $res = $res["Rsp"]["Result"];
            echo "<pre>";
            print_r($res);exit;
            */
            //print_r($res);exit;
            $res = ElaborateResult::elaborate($res["Rsp"]["Result"], $usersList);
            
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
    
    public function users(Request $request){
        
        if($request->all()){
            
            $request->validate([
                'name'      => 'required',
                'email'     => 'required|email|unique:users,email,' .$request->id,
                'password'  => 'required'
            ]);
            
            $user = User::find($request->all()["id"]);
            if($user){
                
                if(!Hash::isHashed( $request->all()["password"])){
                    $user->password = Hash::make($request->all()["password"]);
                }
                $user->name = $request->all()["name"];
                $user->email = $request->all()["email"];
                $user->save();
            }else{
                User::updateOrCreate(['id'=> 0],$request->all());
            }
            
        }
        
        $users = User::where("id",">",1)->get();
        
        $b64 = [];
        
        foreach ($users as $user){
            
            $b64[$user->id]['id'] = $user->id;
            $b64[$user->id]['name'] = $user->name;
            $b64[$user->id]['email'] = $user->email;
            $b64[$user->id]['password'] = $user->password;
            
            
            $b64[$user->id] = base64_encode(json_encode($b64[$user->id]));
            
        }
        
        return view('users', compact('users','b64'));
        
    }
    
    public function delete(Request $request){
        
        $data = $request->all();
        
        $ret["res"] = false;
        
        try {
            $class = 'App\\Models\\'.$data["table"];
            $class::find($data["id"])->delete();
            $ret["res"] = true;
        } catch (Exception $e) {
            $ret["msg"] = $e->getMessage();
            
        }
        echo json_encode($ret);
    }
}
