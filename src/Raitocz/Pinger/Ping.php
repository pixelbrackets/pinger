<?php

$url = $argv[1];
$cl = curl_init();

curl_setopt($cl, CURLOPT_URL, $url);
curl_setopt($cl, CURLOPT_USERAGENT, md5(rand(6713678, 8746316834)));

//curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($cl, CURLOPT_POSTFIELDS, $post);

curl_exec($cl);
curl_close($cl);

exit(0);