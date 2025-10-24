<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use App\Lib\GenetecApi;
use App\Lib\ElaborateResult;
use Illuminate\Support\Facades\Cache;
use App\Models\MailingList;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportEmail;
use Carbon\Carbon ;

class GenerateReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {

        $companyC = Cache::get('credentials');
        
        $companies = MailingList::where("scheduled","GIORNALIERO")->get();
        
        $day = Carbon::now()->yesterday()->format("Y-m-d");
        
        foreach ($companies as $company) {

            $cardholder = implode('@', array_keys($companyC[$company->company]));

            $param["area"] = "f00843a3-1dba-421a-880e-23851725783c@7877aabb-8f00-442a-8739-4f5e30c370ca";
            $param["start"] = $day;
            $param["cardholder"] = $cardholder;

            $api = new GenetecApi();
            $res = $api->getReport($param);
            
            $res = ElaborateResult::elaborate($res["Rsp"]["Result"], $companyC[$company->company],$param["start"]);
            
            $titles = [
                'Nome',
                'Badge',
                'Data ora ingresso',
                'Data ora uscita',
                'Ore:Minuti Totali',
                'Ore:Minuti Effettivi',
            ];
            $htmlString = "<table style='width:100%'><tr><td colspan=6>Azienda: ".$company->company."</td></tr><tr>";

            foreach ($titles as $value) {

                $htmlString .= '<td style="border: 1px solid black;width:200px">' . $value . '</td>';
            }
            $htmlString .= '</tr>';
            foreach ($res as $value) {

                $htmlString .= '<tr>';
                $htmlString .= '<td style="border: 1px solid black;width:200px;">' . $value[0] . '</td>';
                $htmlString .= '<td style="border: 1px solid black;width:200px;text-align:center">' . $value[2] . '</td>';
                $htmlString .= '<td style="border: 1px solid black;width:200px">' . $value[3] . '</td>';
                $htmlString .= '<td style="border: 1px solid black;width:200px">' . $value[4] . '</td>';
                $htmlString .= '<td style="border: 1px solid black;width:200px">' . $value[6] . '</td>';
                $htmlString .= '<td style="border: 1px solid black;width:200px">' . ($value[5]) . '</td>';
                $htmlString .= '</tr>';
            }
            $htmlString .= '</table>';

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            //$htmlString = '<table><tr><td style="border: 1px solid black;">NOT SPANNED</td><td rowspan="2" style="border: 1px solid black;">SPANNED</td></tr><tr><td style="border: 1px solid black;">NOT SPANNED</td></tr></table>';

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($htmlString, $spreadsheet);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
            $writer->save(str_replace("/","_",$company->company).".pdf");
            
            $send = Mail::to(explode(",",$company->emails))->send(new ReportEmail(str_replace("/","_",$company->company).".pdf","giorno ".date("d-m-Y", strtotime($day))." ".$company->company));
            
            //print_r($send);
        }
    }
}
