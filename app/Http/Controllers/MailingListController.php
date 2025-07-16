<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailingList;
use Illuminate\Support\Facades\Cache ;

/**
 * Description of MailingListController
 *
 * @author alberto
 */
class MailingListController extends Controller {
    
    public function index(Request $request){
        
        if($request->all()){
            
            $validate = $request->validate([
                'company'      => 'required',
                'emails'     => 'required',
            ]);
                        
            $user = MailingList::find($request->all()["id"]);
            if($user){
                
                $user->company = $validate["company"];
                $user->emails = $validate["emails"];
                $user->save();
            }else{
                MailingList::updateOrCreate(['id'=> 0],$validate);
            }
            
        }
        
        $company = Cache::get('credentials') ;
        ksort($company);
        
        $users = MailingList::all();
        
        $b64 = [];
        
        foreach ($users as $user){
            
            $b64[$user->id]['id'] = $user->id;
            $b64[$user->id]['company'] = $user->company;
            $b64[$user->id]['emails'] = $user->emails;
                        
            
            $b64[$user->id] = base64_encode(json_encode($b64[$user->id]));
            
        }
        
        return view('mailinglist', compact('users','b64','company'));
    }
    //put your code here
}
