<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\GenetecApi;
use App\Lib\ElaborateResult;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Cache;
use App\Mail\ReportEmail;

class GenerateReportMonthlyHoursPresence extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-report-monthly-hours-presence';

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
	/*
	  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	  $spreadsheet = $reader->loadFromString(file_get_contents("/tmp/people.html"));
	  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	  $writer->save("persone.xls");
	  exit;
	 */
	//$prevMonth = Carbon::createFromDate(2025, 7, 1); //Carbon::now()->startOfMonth()->subMonth();
	$prevMonth = Carbon::now()->startOfMonth()->subMonth();
	$dateinM = $prevMonth->format("Y-m");
	$endOfMonth = $prevMonth->daysInMonth(); //Carbon::createFromDate(2025, 9, 1)->daysInMonth();

	$companies = Cache::get('credentials');
	ksort($companies);
	$tot = count($companies);
	$cnt = 0;
	foreach ($companies as $key => $value) {

	    $cnt++;

	    if ($key == "" || $key == "SARPOM" || $key == "SARPOM QUILIANO" || array_keys($value) == 0)
		continue;

	    $cardholder = implode('@', array_keys($value));
	    $peoplePresent[$key] = [];
	    $hourPresent[$key] = 0;

	    for ($i = 1; $i <= $endOfMonth; $i++) {

		$param["area"] = "f00843a3-1dba-421a-880e-23851725783c@7877aabb-8f00-442a-8739-4f5e30c370ca";
		$param["start"] = $dateinM . "-" . sprintf("%02d", $i);
		$param["cardholder"] = $cardholder;

		echo "$cnt di $tot Esamino $key data: " . $param["start"] . PHP_EOL;

		$api = new GenetecApi();
		$res = $api->getReport($param);
		//print_r($res);exit;
		$res = ElaborateResult::elaborate($res["Rsp"]["Result"], $value, $param["start"]);

		$cntPeople = 0;

		foreach ($res as $value1) {
		    foreach ($value1 as $pp) {
			$cntPeople++;
			[$ore, $minuti] = explode(':', $pp[5]);
			$hourPresent[$key] += ($ore * 60) + $minuti;
		    }
		}
		$peoplePresent[$key][$i] = $cntPeople;
	    }
	}

	$htmlString = "<table style='width:100%'>" . PHP_EOL;
	$htmlString .= "<tr style='heigth:80px'><td colspan=30><b>PERSONE PRESENTI IN RAFFINERIA PER OGNI SETTIMANA - MESE  $dateinM - DAL GIORNO 01 AL GIORNO $endOfMonth</b></td></tr>" . PHP_EOL;
	$start = 1;
	$totDays = [];
	foreach ($peoplePresent as $company => $values) {

	    if ($start) {
		$htmlString .= "<tr><td style='background-color:#a9d18e;width:180px'>DITTA</td>" . PHP_EOL;
		foreach ($values as $day => $value) {

		    if (Carbon::createFromFormat("Y-m-d", "$dateinM-" . sprintf("%02d", $day))->isSunday()) {
			$htmlString .= "<td style='background-color:#ffc000;width:35px'>$day</td>" . PHP_EOL;
			$htmlString .= "<td style='background-color:#a9d18e;width:35px'>T.Set.</td>" . PHP_EOL;
		    } else {
			$htmlString .= "<td style='background-color:#a9d18e;width:35px'>$day</td>" . PHP_EOL;
		    }
		}
		$htmlString .= "<td style='background-color:#ffc000;width:35px'>T.Progr.</td>" . PHP_EOL;
		$htmlString .= "</tr>";
		$start = 0;
	    }
	    $htmlString .= "<tr><td>" . str_replace("&", "&amp;", $company) . "</td>" . PHP_EOL;
	    $totWeek = $totMon = 0;
	    foreach ($values as $day => $value) {

		$htmlString .= "<td>$value</td>" . PHP_EOL;
		$totMon += $value;
		$totWeek += $value;
		if (!isset($totDays[$day]))
		    $totDays[$day] = 0;

		$totDays[$day] += $value;

		if (Carbon::createFromFormat("Y-m-d", "$dateinM-" . sprintf("%02d", $day))->isSunday()) {
		    $htmlString .= "<td style='background-color:#e2f0d9;width:35px'>$totWeek</td>" . PHP_EOL;
		    $totWeek = 0;
		}
	    }
	    $htmlString .= "<td>$totMon</td>" . PHP_EOL;
	    $htmlString .= "</tr>" . PHP_EOL;
	}
	$htmlString .= "<tr><td>TOTALE</td>" . PHP_EOL;

	$totGen = 0;
	$totSet = 0;
	foreach ($totDays as $day => $value) {

	    $totSet += $value;

	    if (Carbon::createFromFormat("Y-m-d", "$dateinM-" . sprintf("%02d", $day))->isSunday()) {
		$htmlString .= "<td>$value</td>" . PHP_EOL;
		$htmlString .= "<td style='background-color:#e2f0d9;width:35px'>$totSet</td>" . PHP_EOL;
		$totSet = 0;
	    } else {
		$htmlString .= "<td>$value</td>" . PHP_EOL;
	    }
	    $totGen += $value;
	}
	$htmlString .= "<td>$totGen</td>" . PHP_EOL;
	$htmlString .= "</tr>" . PHP_EOL;

	$htmlString .= "</table>";
	file_put_contents("/tmp/people.html", $htmlString);

	$htmlString1 = "<table style='width:100%'>" . PHP_EOL;
	$htmlString1 .= "<tr style='heigth:80px'><td colspan=2><b>Totale ore Contrattori $dateinM</b></td></tr>";
	$htmlString1 .= "<tr><td style='width:180px'>DITTA</td><td style='width:180px'>Sum of TOTALE ORE:MINUTI</td></tr>";

	$totMin = 0;

	foreach ($hourPresent as $company => $value) {

	    $totMin += $value;

	    $oreTot = intdiv($value, 60);
	    $minTot = $value % 60;

	    $risultato = sprintf('%02d:%02d', $oreTot, $minTot);

	    $htmlString1 .= "<tr><td>" . str_replace("&", "&amp;", $company) . "</td><td>$risultato</td></tr>";
	}

	$oreTot = intdiv($totMin, 60);
	$minTot = $totMin % 60;

	$risultato = sprintf('%02d:%02d', $oreTot, $minTot);

	$htmlString1 .= "<tr><td>TOTALE</td><td>$risultato</td></tr>";

	$htmlString1 .= "</table>";

	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($htmlString);
	$spreadsheet->getActiveSheet()->setTitle('Presenze');

	$tmpSpreadsheet = $reader->loadFromString($htmlString1);
	$tmpSheet = $tmpSpreadsheet->getActiveSheet();
	$tmpSheet->setTitle('Ore');

// copia il foglio
	$spreadsheet->addExternalSheet($tmpSheet);

	$reader->setSheetIndex(1);

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save("Presenze-ore-contrattori-del-$dateinM.xls");

	$emails = "michela.pozzato@eseitalia.it,veronica.canever@eseitalia.it,marco.grassi@eseitalia.it,ilenia.d.zanardi@eseitalia.it";

	$send = Mail::to(explode(",", $emails))->send(new ReportEmail("Presenze-ore-contrattori-del-$dateinM.xls", "mese $dateinM presenze ore contrattori"));
    }
}
