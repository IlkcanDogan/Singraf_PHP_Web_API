<?php

$h1 = 'https://ilkcandogan.com/haber1.php';
$h2 = 'https://ilkcandogan.com/haber2.php';

Gonder($h1);
Gonder($h2);

function Gonder($link){
	$durum = false;
	
	$ch = curl_init($link);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$cevap = curl_exec($ch);
	return $durum;
}

?>