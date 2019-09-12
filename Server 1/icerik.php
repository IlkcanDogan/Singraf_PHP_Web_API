<?php 

include "fonksiyon.php";
include "veritabani.php";
headerEkle();
error_reporting(0);
$jsonDizi['bilgi'] = array();

$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "GET"){
	function clear($str){
		$string = str_replace('&#039;', "'", $str);
		$string = str_replace('&amp;', "", $string);
		$string = str_replace('??', "", $string);
		$string = str_replace('&quot;', "", $string);
		$string = trim($string);
		$string = ltrim($string);

		$pattern = '@<(.*?)>@si';
		preg_match_all($pattern,$string,$yazilar);
		$vIndex = count($yazilar[1]);
		for ($i=0; $i <$vIndex; $i++) { 
			$stringD = $yazilar[0][$i];
			$string = str_replace($stringD, "", $string);		
		}
		
		
		$pattern = '@<figure(.*?)</iframe></figure>@si';
		preg_match_all($pattern,$string,$yazilar);
		$vIndex = count($yazilar[1]);
		for ($i=0; $i <$vIndex; $i++) { 
			$stringD = $yazilar[0][$i];
			$string = str_replace($stringD, "", $string);		
		}

		return $string;
	}
	$id = Temizle(trim($_GET["id"]));
	if($id != null){
		$_kod = 200;
		$sql = "CALL sp_NEWS_CONTENT_GET('$id')";
		$output = mysqli_query($baglanti,$sql);

		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				
				extract($satir);
				$_CONTENT = clear($satir['CONTENT']);
				$_TARIH = $satir['DATE'];
				$_IMG1 = $satir['IMG1'];
				$_IMG2 = $satir['IMG2'];

				$bilgilerArray = array(
					"ICERIK" => $_CONTENT,
					"TARIH" => $_TARIH,
					"RESIM1" => $_IMG1,
					"RESIM2" => $_IMG2
				);
				array_push($jsonDizi["bilgi"], $bilgilerArray);
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