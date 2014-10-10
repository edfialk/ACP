<?php

if (!isset($_REQUEST['url'])){
	echo "missing required parameter url";
	return;
}

$c = curl_init();
curl_setopt($c, CURLOPT_URL, urldecode($_REQUEST['url']));
curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
$result = curl_exec($c);

if(curl_errno($c)){
	echo curL_error($c);
}else{
	echo $result;
}

// $result = mb_convert_encoding($result, "UTF-8", "Windows-1252");

?>