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

    public static function elaborate($result, $usersList, $dateSel) {

        $area = [];
        $area["f00843a3-1dba-421a-880e-23851725783c"] = "RAFFINERIA TRECATE";
        $area["7877aabb-8f00-442a-8739-4f5e30c370ca"] = "PALAZZINA DIREZIONE";

        $collection = [];
        foreach ($result as $key => $users) {

            $areaName = $area[$result[$key]["AreaGuid"]];
            if ($areaName == "")
                continue;

            $name = $usersList[$result[$key]["CardholderGuid"]]["name"];
            $cardid = $usersList[$result[$key]["CardholderGuid"]]["cardid"];
            $code = $usersList[$result[$key]["CardholderGuid"]]["code"];
            $dateIn = $dateInC = Carbon::createFromDate($users["FirstTimeIn"])->format("d-m-Y H:i");
            $dateOut = $dateOutC = Carbon::createFromDate($users["LastExitTime"])->format("d-m-Y H:i");

            $dateInCk = Carbon::createFromDate($users["FirstTimeIn"])->format("Y-m-d");
            $dateOutCk = Carbon::createFromDate($users["LastExitTime"])->format("Y-m-d");

            $minutes = $users["TotalMinutes"];
            $totMinutes = $users["TotalMinutesInclusive"];

            $dateCkIn = strtotime($dateSel . " 07:57");
            $dateCkOut = strtotime($dateSel . " 16:45");
            //$hourMin = sprintf("%02d:%02d",floor($minutes/60),$minutes%60);

            $dateSelIniTime = strtotime($dateSel . " 06:00:00");
            $dateSelPause = strtotime($dateSel . " 12:45:00");

            if (!isset($collection[$name]))
                $collection[$name] = [];

            if ($dateInCk == $dateOutCk && $dateInCk == $dateSel) {

                $change = false;
                if (strtotime($dateIn) <= $dateCkIn && strtotime($dateIn) > $dateSelIniTime) {

                    $dateInC = date("d-m-Y H:i", $dateCkIn);
                }
                /*
                  if(strtotime($dateOut) >= $dateCkOut){

                  $dateOutC = date("d-m-Y H:i",$dateCkOut);
                  }
                 */
                $totHourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                $diff = strtotime($dateOutC) - strtotime($dateInC);
                $minutes = ($diff / 60);
                $totMinutes = $minutes;

                //echo "$dateInCk==$dateOutCk "



                if (strtotime($dateIn) < $dateSelPause && strtotime($dateIn) > $dateSelIniTime && strtotime($dateOut) > $dateSelPause) {

                    $totMinutes -= 45;
                }

                $hourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                if (isset($collection[$name][$areaName])) {

                    //if (strtotime($dateIn) > strtotime($collection[$name][3]) && strtotime($dateOut) < strtotime($collection[$name][4]))
                    //    continue;

                    $minutes = $users["TotalMinutes"] + $collection[$name][$areaName][7];
                    $totMinutes = $users["TotalMinutesInclusive"] + $collection[$name][$areaName][8];

                    $hourMin = sprintf("%02d:%02d", floor($minutes / 60), $minutes % 60);
                    $totHourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                    //$totMinutes -= 45;
                    $hourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                    $collection[$name][$areaName] = [$name, $code, $cardid, $collection[$name][$areaName][3], $dateOut, $hourMin, $totHourMin, $minutes, $totMinutes];
                } else {
                    //echo $totMinutes." - $hourMin".PHP_EOL;
                    $collection[$name][$areaName] = [$name, $code, $cardid, $dateIn, $dateOut, $hourMin, $totHourMin, $minutes, $totMinutes];
                }
            } else {
                //if ($result[$key]["CardholderGuid"] == "b12fb477-d703-43bc-9010-d0bd4963a25c") {

                if (isset($collection[$name][$areaName])) {

                    $minutes = $users["TotalMinutesInclusive"] + $collection[$name][$areaName][7];
                    $totMinutes = $users["TotalMinutesInclusive"] + $collection[$name][$areaName][8];

                    $totHourMin = sprintf("%02d:%02d", floor($minutes / 60), $minutes % 60);
                    $hourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                    //$totMinutes -= 45;
                    //$hourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                    $dateInCk = Carbon::createFromDate($collection[$name][$areaName][3])->format("Y-m-d");

                    if ($dateInCk == $dateSel)
                        $collection[$name][$areaName] = [$name, $code, $cardid, $collection[$name][$areaName][3], $dateOut, $hourMin, $totHourMin, $minutes, $totMinutes];
                } else {

                    $minutes = $totMinutes;

                    $totHourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                    if (strtotime($dateIn) < $dateSelPause && strtotime($dateIn) > $dateSelIniTime && strtotime($dateOut) > $dateSelPause) {
                        $totMinutes -= 45;
                    }

                    $hourMin = sprintf("%02d:%02d", floor($totMinutes / 60), $totMinutes % 60);

                    if ($dateInCk == $dateSel)
                        $collection[$name][$areaName] = [$name, $code, $cardid, $dateIn, $dateOut, $hourMin, $totHourMin, $minutes, $totMinutes];
                }
                //}
            }
        }

        ksort($collection);

        return $collection;
    }
}
