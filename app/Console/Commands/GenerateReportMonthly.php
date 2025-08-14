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
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportEmail;

class GenerateReportMonthly extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-report-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate report monthly';

    /**
     * Execute the console command.
     */
    public function handle() {
        $prevMonth = Carbon::now()->startOfMonth()->subMonth();
        $dateinM = $prevMonth->format("Y-m");
        $endOfMonth = $prevMonth->daysInMonth();//Carbon::createFromDate(2025, 2, 1)->daysInMonth();
        
        $companyC = Cache::get('credentials');

        $companies = MailingList::where("scheduled","MENSILE")->get();

        foreach ($companies as $company) {

            $cardholder = implode('@', array_keys($companyC[$company->company]));

            $htmlString = "<table style='width:100%'>";

            for ($i = 1; $i <= $endOfMonth; $i++) {

                $param["area"] = "f00843a3-1dba-421a-880e-23851725783c";
                $param["start"] = $dateinM ."-". sprintf("%02d", $i);
                $param["cardholder"] = $cardholder;

                echo "Esamino data: " . $param["start"] . PHP_EOL;

                $api = new GenetecApi();
                $res = $api->getReport($param);
                //print_r($res);exit;
                $res = ElaborateResult::elaborate($res["Rsp"]["Result"], $companyC[$company->company], $param["start"]);

                $titles = [
                    'Nome',
                    'Badge',
                    'Data ora ingresso',
                    'Data ora uscita',
                    'Ore:Minuti Totali',
                    'Ore:Minuti Effettivi',
                ];

                if ($i > 1) {
                    $htmlString .= "<tr><td colspan=6>-</td></tr>";
                }

                $htmlString .= "<tr><td colspan=6>Azienda: " . $company->company . " " . date("d-m-Y", strtotime($param["start"])) . "</td></tr><tr>";

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
            }
            $htmlString .= '</table>';

            //file_put_contents("/tmp/out.html", $htmlString);

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            //$htmlString = '<table><tr><td style="border: 1px solid black;">NOT SPANNED</td><td rowspan="2" style="border: 1px solid black;">SPANNED</td></tr><tr><td style="border: 1px solid black;">NOT SPANNED</td></tr></table>';

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($htmlString, $spreadsheet);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
            $writer->save($company->company . ".pdf");
            
            $send = Mail::to(explode(",",$company->emails))->send(new ReportEmail($company->company.".pdf","mese ".$dateinM." ".$company->company));
        }
    }
}
