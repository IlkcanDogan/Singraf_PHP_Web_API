<?php 

include "fonksiyon.php";
include "veritabani.php";
headerEkle();

error_reporting(0);
$jsonDizi['bilgi'] = array();

$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "GET"){
	$_kod = 200;

		$sql = "CALL sp_CATEGORY_GET()";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){	    
				extract($satir);
				$bilgilerArray = array(
					"ID" => $ID, 
					"CATEGORY" => $CATEGORY_NAME,
					"COUNT" => $COUNT
				);
				array_push($jsonDizi["bilgi"], $bilgilerArray);
			  }
		}
	
		BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
}	
else{
	$_kod = 400;
	BaslikAyarla($_kod);
}


?>