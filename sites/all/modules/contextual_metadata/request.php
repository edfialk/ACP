<?php

header("Content-type: application/json");

if (!isset($_REQUEST['url'])){
	echo "missing required parameter url";
	return;
}
// echo urldecode($_REQUEST['url']);

$c = curl_init();
curl_setopt($c, CURLOPT_URL, $_REQUEST['url']);
curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
curl_setopt($c, CURLOPT_CONNECTTIMEOUT ,0);
curl_setopt($c, CURLOPT_TIMEOUT, 120); //timeout in seconds
$result = curl_exec($c);

if(curl_errno($c)){
	$resp = array('status' => 'error', 'message' => curl_error($c));
	// switch(curl_error($c)){
	// 	case "connect() timed out!":
	// 		$resp['message'] = 'time out';
	// 		break;
	// 	case "couldn't connect to host":
	// 		$resp['message'] = 
	// 	default:
	// 		$resp['message'] = curl_error($c);
	// 		break;
	// }
	echo json_encode($resp);
}else{
	echo $result;
}

curl_close($c);
// $result = mb_convert_encoding($result, "UTF-8", "Windows-1252");

?>