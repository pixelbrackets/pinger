<?php

$url = $argv[1];
$postData = (isset($argv[2]) ? $argv[2] : null);
$getData = (isset($argv[3]) ? $argv[3] : null);


$cl = curl_init();

curl_setopt($cl, CURLOPT_URL, $url . (!isset($getData) ? (strpos($url, '?') === FALSE ? '?' : '') : '') . $getData);
curl_setopt($cl, CURLOPT_USERAGENT, md5(rand(32767, 65535)));

if ($postData) {
    curl_setopt($cl, CURLOPT_POST, 1);
    curl_setopt($cl, CURLOPT_POSTFIELDS, $postData);
}

curl_exec($cl);
curl_close($cl);

exit(0);