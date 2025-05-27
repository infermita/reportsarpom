<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Lib;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Lib\GenetecApi;
use Illuminate\Support\Facades\Cache ;
use Illuminate\Support\Carbon;
use App\Lib\ElaborateResult;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;


/**
 * Description of ReportExport
 *
 * @author alberto
 */
class ReportExport implements WithMultipleSheets
{
    
    use Exportable;
    
    public function sheets(): array
    {
        //$sheets = [];
        //$sheets["Sarpom"] = new ReportExportSheet();
        $sheets = [];
        $company = Cache::get('credentials') ;
        
        foreach ($company as $key => $users){
            
            $cardholder = implode('@', array_keys($users));
                
            $sheets[] = new ReportExportSheet("2025-04-10",$key,$cardholder,$users);
            
            
            
        }
        
        return $sheets;
        
    }
    
}

class ReportExportSheet implements FromCollection,WithHeadings,WithStyles, ShouldAutoSize,WithTitle
{
    
    protected $company;
    protected $date;
    protected $cardholder;
    protected $users;
    
    public function __construct($date,$company,$cardholder,$users)
    {
        $this->company = $company;
        $this->date = $date;
        $this->cardholder = $cardholder;
        $this->users = $users;
    }
    
    public function collection()
    {
        //$collection = collect(['first', 'second']);
        //Excel::store($export, $filePath);
        //$company = Cache::get('credentials') ;
        
        $usersList = [];
        
        $param = [];
        
        $param["area"] = "f00843a3-1dba-421a-880e-23851725783c";
        $param["start"] = $this->date;
        $param["cardholder"] = $this->cardholder;
        /* 
        foreach ($company as $key => $users){
            
            if($key=="SARPOM"){
                
                $param["cardholder"] = implode('@', array_keys($users));
                
            }
            
            foreach ($users as $key => $user){
                $usersList[$key] = $user;
            }
            
        }
        * 
        */
        $api = new GenetecApi();
        $res = $api->getReport($param);
        /*
        $api = new GenetecApi();
        $res = $api->getReport($param);
        
        
        $collection = [];
        foreach ($res["Rsp"]["Result"] as $key => $users){
            
            $name = $usersList[  $res["Rsp"]["Result"][$key]["CardholderGuid"] ];
            $dateIn = Carbon::createFromDate($users["FirstTimeIn"])->format("d-m-Y H:i");
            $dateOut = Carbon::createFromDate($users["LastExitTime"])->format("d-m-Y H:i");
            $hourMin = sprintf("%02d:%02d",floor($users["TotalMinutes"]/60),$users["TotalMinutes"]%60);
            
            $collection[$name] = [$name,$dateIn,$dateOut,$hourMin];
            
            
            
        }
        ksort($collection);
        */
        
        return new Collection(ElaborateResult::elaborate($res["Rsp"]["Result"], $this->users));
    }
    public function headings(): array
    {
        return [
            'Nome',
            'Cod. Sarpom',
            'Badge',
            'Data ora ingresso',
            'Data ora uscita',
            'Ore:Minuti Effettive',
            'Ore:Minuti Totali',
            'Minuti Effettivi',
            'Minuti Totali',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
    public function title(): string
    {
        return $this->company;
    }
    
    
}
