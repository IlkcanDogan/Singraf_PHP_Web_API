<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
$pDizi = array();
		
		$sql = "CALL sp_USE_FIRST()";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
				array_push($pDizi, $VIDEO_NO);
				
			}
            
		}
		
		    print_r($pDizi);
            Gonder('http://127.0.0.1/server2/birinciSec.php',$pDizi);

function Gonder($serverLink, $JsonDizi){
    
	$JsonData = json_encode($JsonDizi);
	$ch = curl_init($serverLink);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $JsonData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: '.strlen($JsonData),
		'API_KEY: 13dd183da96f031438d991f49f04f0b4'
		)
	);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$cevap = curl_exec($ch);
	$gelenVeri = json_decode($cevap);

	
}


?>