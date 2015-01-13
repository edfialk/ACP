<?php

/**
 *  SO, Google Spreadsheet API is hacked together and left behind. Formulas (i.e. hyperlinks) are ONLY retrievable through a private cell feed.
 *  I could have created a google user, requested permission to the sheet, use OAuth and parse cell feed.
 *  Instead I went with using the spreadsheet html table as a datasource, assuming it will change as infrequently as google's spreadsheet api.
 *  The cons are it might change and the table ID or row query here will need to be updated, but it's much, much faster.
 */

header('Content-type: application/json; charset=UTF-8');

$_GOOGLE_URL = 'https://docs.google.com/spreadsheet/pub?key=0Aiz9YvG1OtuddFRmYWx3OVd2MDVoRWp6VDkyVDY5V0E&single=true&gid=0&output=html';
$_CONTENT_TABLE_ID = 'tblMain';

//column index at google url
$_GOOGLE_COLUMNS = array(
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

$ch = curl_init();
curl_setopt_array($ch,
	array(
		CURLOPT_URL => $_GOOGLE_URL,
		CURLOPT_RETURNTRANSFER => true
	)
);
$file = curl_exec($ch);

$doc = new DOMDocument();
$doc->loadHTML($file);
$xpath = new DOMXpath($doc);

$output = array('data' => array());

$temporals = array();
$spatials = array();

$table = $xpath->query('//table[@id="'.$_CONTENT_TABLE_ID.'"]');
if ($table->length == 0){
	error('Cannot find google table at <a href="'.$url.'">request url</a>. Please verify content table still has id: "'.$_CONTENT_TABLE_ID.'" or update in goog.php');
}
$table = $table->item(0);

$trs = $xpath->query('.//tr', $table);
if ($trs->length == 0){
	error('Cannot find any rows in google table at <a href="'.$url.'">request url</a>.');
}

foreach($trs as $tr){
	if ($tr->hasAttribute('class') && $tr->getAttribute('class') == 'rShim'){
		continue; //useless google table row for column widths;
	}

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

	array_push($output['data'], $row);

	$temp = trim($tempRes->nodeValue);
	$spat = trim($spatRes->nodeValue);

	if ($temp != '' && !in_array($temp, $temporals)){
		array_push($temporals, $temp);
	}
	if ($spat != '' && !in_array($spat, $spatials)){
		array_push($spatials, $spat);
	}
}

$output['temporals'] = $temporals;
$output['spatials'] = $spatials;

echo json_encode($output);

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
		//unfinished, so can't reorder columns yet
	}
}*/

function getCell($cells, $index){
	global $_GOOGLE_COLUMNS;

	if (!isset($_GOOGLE_COLUMNS[$index])){
		warning('Cannot find google spreadsheet column for index: '.$index.', this column will be blank.');
		return null;
	}

	return $cells->item($_GOOGLE_COLUMNS[$index]);
}
function getHtml($node){
	if (is_null($node)){
		return '';
	}
	if ($node->hasChildNodes()){
		return $node->ownerDocument->saveHTML($node->firstChild);
	}else{
		return $node->nodeValue;
	}
}
function error($msg){
	echo json_encode(
		array(
			'status' => 'error',
			'message' => $msg
		)
	);
	die();
}
function warning($msg){
	global $output;
	if (!isset($output['status'])){
		$output['status'] = 'warning';
	}
	if (!isset($output['message'])){
		$output['message'] = array();
	}
	array_push($output['message'], $msg);
}
?>