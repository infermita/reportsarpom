<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Lib;
use Illuminate\Support\Carbon;

/**
 * Description of ElaborateResult
 *
 * @author alberto
 */
class ElaborateResult {
    //put your code here
    
    public static function elaborate($result,$usersList) {
        
        
        $collection = [];
        foreach ($result as $key => $users){
            
            $name = $usersList[  $result[$key]["CardholderGuid"] ]["name"];
            $cardid = $usersList[  $result[$key]["CardholderGuid"] ]["cardid"];
            $code = $usersList[  $result[$key]["CardholderGuid"] ]["code"];
            $dateIn = Carbon::createFromDate($users["FirstTimeIn"])->format("d-m-Y H:i");
            $dateOut = Carbon::createFromDate($users["LastExitTime"])->format("d-m-Y H:i");
            
            $minutes = $users["TotalMinutes"];
            $totMinutes = $users["TotalMinutesInclusive"];
            
            $hourMin = sprintf("%02d:%02d",floor($minutes/60),$minutes%60);
            $totHourMin = sprintf("%02d:%02d",floor($totMinutes/60),$totMinutes%60);
            
                
            if(isset($collection[$name])){
            
                $minutes = $users["TotalMinutes"] + $collection[$name][7];
                $totMinutes = $users["TotalMinutesInclusive"] + $collection[$name][8];
                
                $hourMin = sprintf("%02d:%02d",floor($minutes/60),$minutes%60);
                $totHourMin = sprintf("%02d:%02d",floor($totMinutes/60),$totMinutes%60);
                
                $collection[$name] = [$name,$code,$cardid, $collection[$name][3], $dateOut, $hourMin, $totHourMin ,$minutes,$totMinutes];
                
            }else{
            
                $collection[$name] = [$name,$code,$cardid,$dateIn,$dateOut,$hourMin, $totHourMin ,$minutes,$totMinutes];
                
            }
            
            
            
        }
        ksort($collection);
        
        return $collection;
        
    }
    
}
