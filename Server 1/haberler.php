<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
$jsonDizi['bilgi'] = array();
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "GET"){
	function clear($str){
	$string = str_replace('&#039;', "'", $str);
		$string = str_replace('&quot;', "", $string);
		$string = trim($string);
		$string = ltrim($string);

		$pattern = '@<img src=(.*?)>@si';
		preg_match_all($pattern,$string,$yazilar);
		$vIndex = count($yazilar[1]);
		for ($i=0; $i <$vIndex; $i++) { 
			$stringD = $yazilar[0][$i];
			$string = str_replace($stringD, "", $string);		
		}
		
		$pattern = '@<(.*?)>@si';
		preg_match_all($pattern,$string,$yazilar);
		$vIndex = count($yazilar[1]);
		for ($i=0; $i <$vIndex; $i++) { 
			$stringD = $yazilar[0][$i];
			$string = str_replace($stringD, "", $string);		
		}


		return $string;
	}
	$start = Temizle(trim($_GET["start"]));
	if($start != null){
		$_kod = 200;
		$sql = "CALL sp_NEWS_GET('$start')";
		$output = mysqli_query($baglanti,$sql);
		$son;
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				
				extract($satir);
				$_ID = $satir['ID'];
				$_BASLIK = clear($satir['TITLE']);
				$_RESIM = $satir['IMG_LINK'];
				$son = $_ID;
				$bilgilerArray = array(
					"ID" => $_ID,
					"BASLIK" => $_BASLIK,
					"RESIM" => $_RESIM
				);
				array_push($jsonDizi["bilgi"], $bilgilerArray);
				$jsonDizi['SON_ID'] = $son;
			}

		}
	
		BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);

	}
}	
else{
	$_kod = 400;
	BaslikAyarla($_kod);
}

?>