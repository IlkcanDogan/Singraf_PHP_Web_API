<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$player_id = htmlspecialchars($_POST['player_id']);
	$token = htmlspecialchars($_POST['Authorization']);
	$uzunluk = strlen($player_id);
	if($token != null && $player_id != null && $uzunluk == 36){
		$tokenDizisi = SifreliTokenCoz($token);
		$_MAIL = $tokenDizisi[0];
		$_PASS = $tokenDizisi[1];

			$_kod = 200;

			$sql = "CALL sp_PLAYER_ID('$_MAIL','$_PASS','$player_id')";
			$output = mysqli_query($baglanti,$sql);
			if(mysqli_num_rows($output)> 0){
				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$hata = $satir['@p_ERROR'];
				}
			}
			
			if($hata == 1){
				$jsonDizi['hata'] = 1; //TOKEN HATASI.
			}
			else{
				$jsonDizi['hata'] = 0; //ISLEM BASARILI.
			}

			BaslikAyarla($_kod);
			echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}

}



?>