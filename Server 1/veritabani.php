<?php 

error_reporting(0);
	$sunucu = "localhost";
	$veritabani_adi = "";
	$kullanici_adi = "";
	$sifre = "";
	
			$baglanti = new mysqli($sunucu,$kullanici_adi,$sifre,$veritabani_adi);
			$baglanti->set_charset("utf8");
			if($baglanti->connect_error){
				//echo "Bağlantı Hatası.";
			}
	
?>