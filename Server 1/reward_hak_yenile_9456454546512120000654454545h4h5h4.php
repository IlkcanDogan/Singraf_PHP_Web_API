<?php 
include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
ini_set('max_execution_time', 3000);


    	
	$sql = "update tb_users set COIN_H = 48";
	if(mysqli_query($baglanti, $sql)){

    	

	} else{
    	echo "hata" . mysqli_error($link);
	}


?>