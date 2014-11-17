<?php

header('Content-type: application/json; charset=UTF-8');

$url = 'https://docs.google.com/spreadsheet/pub?key=0Aiz9YvG1OtuddFRmYWx3OVd2MDVoRWp6VDkyVDY5V0E&single=true&gid=0&output=html';

// $file = file_get_contents($url);
// $file = str_replace('âœ”', 'check', $file);

$ch = curl_init();
curl_setopt_array($ch,
	array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true
	)
);

$file = curl_exec($ch);

$doc = new DOMDocument();
$doc->loadHTML($file);
$xpath = new DOMXpath($doc);

//display column order for Drupal table
$drupalHeaders = array(
	'Dataset Name',
	'Data Provider',
	'CH4',
	'CO',
	'CO2',
	'NO2',
	'O3',
	'SO2',
	'Temporal Resolution',
	'Spatial Resolution',
	'Catalog',
	'FTP',
	'HTTP',
	'OPeNDAP',
	'WCS',
	'WMS',
	'Notes',
	'Metadata Source'
);

//google's column index
$googleHeaders = array(
	'name' => 2,
	'provider' => 3,
	'ch4' => 4,
	'co' => 5,
	'co2' => 6,
	'no2' => 7,
	'o3' => 8,
	'so2' => 9,
	'tempRes' => 10,
	'spatRes' => 11,
	'catalog' => 12,
	'ftp' => 13,
	'http' => 14,
	'opendap' => 15,
	'wcs' => 16,
	'wms' => 17,
	'notes' => 18,
	'source' => 19
);

$output = array();
// array_push($output, $drupalHeaders);

//content table has id "tblMain"
//header table has class "colHead_0"

$table = $xpath->query('//table[@id="tblMain"]');
if ($table->length == 0){
	//error
}
$table = $table->item(0);

$trs = $xpath->query('.//tr[@dir="ltr"]', $table);
if ($trs->length == 0){
	//error
}

foreach($trs as $tr){
	$cells = $xpath->query('.//td', $tr);
	$name = getCell($cells, 'name');
	$provider = getCell($cells, 'provider');
	$ch4 = getCell($cells, 'ch4');
	$co = getCell($cells, 'co');
	$co2 = getCell($cells, 'co2');
	$no2 = getCell($cells, 'no2');
	$o3 = getCell($cells, 'o3');
	$so2 = getCell($cells, 'so2');
	$tempRes = getCell($cells, 'tempRes');
	$spatRes = getCell($cells, 'spatRes');
	$catalog = getCell($cells, 'catalog');
	$ftp = getCell($cells, 'ftp');
	$http = getCell($cells, 'http');
	$opendap = getCell($cells, 'opendap');
	$wcs = getCell($cells, 'wcs');
	$wms = getCell($cells, 'wms');
	$notes = getCell($cells, 'notes');
	$source = getCell($cells, 'source');

	//must be in same column order as drupal headers above
	$row = array();
	array_push($row, getHtml($name));
	array_push($row, getHtml($provider));
	array_push($row, getHtml($ch4));
	array_push($row, getHtml($co));
	array_push($row, getHtml($co2));
	array_push($row, getHtml($no2));
	array_push($row, getHtml($o3));
	array_push($row, getHtml($so2));
	array_push($row, getHtml($tempRes));
	array_push($row, getHtml($spatRes));
	array_push($row, getHtml($catalog));
	array_push($row, getHtml($ftp));
	array_push($row, getHtml($http));
	array_push($row, getHtml($opendap));
	array_push($row, getHtml($wcs));
	array_push($row, getHtml($wms));
	array_push($row, getHtml($notes));
	array_push($row, getHtml($source));

	array_push($output, $row);
}

echo json_encode(array('data' => $output));

/*$headers = getHeaders($xpath);


function getHeaders($xpath){
	$table = $xpath->query('//table[contains(@class, "colHead")]');
	if (count($table) == 0){
		//error
	}
	$table = $table->item(0);
	$rows = $xpath->query('.//tr[@dir="ltr"]', $table);

	if (count($rows != 2)){
		//error
	}

	//first row -> dataset name, etc.
	$row = $rows->item(0);
	$cells = $xpath->query('.//td', $row);
	$colIndx = 0;
	for ($i = 0; $i < count($cells); $i++){
		if ($cell->hasAttribute('class') && ($cell->getAttribute('class') == 'dn' || $cell->getAttribute('class') == 'hd')){
			//dn is display:none, so hidden column, hd is useless no idea why its there
			continue;
		}
		//unfinished, screw it can't reorder columns yet
	}
}*/

function getCell($cells, $index){
	global $googleHeaders;

	if (!isset($googleHeaders[$index])){
		//error
	}

	return $cells->item($googleHeaders[$index]);
}
function getHtml($node){
	if ($node->hasChildNodes()){
		return $node->ownerDocument->saveHTML($node->firstChild);
	}else{
		return $node->nodeValue;
	}
}
?>