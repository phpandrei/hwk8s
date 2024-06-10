<?php
echo '<h1>CURL</h1>'; 
echo '<hr>'; 

$ch = curl_init('http://hwk8s-gogo-s:8080/test?abc=444444&cde=5555erte');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$html = curl_exec($ch);
curl_close($ch);

echo '<pre>';
var_dump($html);
echo '</pre>';