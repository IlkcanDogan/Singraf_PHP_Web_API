<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$jsonDizi = array();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();
	if($token != null){
		$_kod = 200;
		$token = SifreliTokenCoz($token);
		$mail = $token[0];
		$pass = $token[1];


		$sql = "CALL sp_COIN_REWARD('$mail','$pass')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output)> 0){

				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$hata = $satir['@p_ERROR'];

				}
		}
		if($hata == 111){
			$jsonDizi['hata'] = 0; //işlem başarılı.
		}
		elseif($hata == 2){
			$jsonDizi['hata'] = 2; //reklam hakkı kalmamış
		}
		elseif($hata == 3){
			$jsonDizi['hata'] = 3; //token hatası.
		}

		BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}
}

/*function yaz($string){
	$dosya = fopen("loggg.txt", "a"); //
     fwrite($dosya, $string);
     fclose($dosya);
}
yaz("-------------------------FULL SERVER-----------------------------\n");

foreach ($_SERVER As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}

yaz("---------------------------HEADERS----------------------------\n");

foreach (apache_request_headers() As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}

yaz("---------------------------NORMAL POST----------------------------\n");

foreach ($_POST As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}
yaz("---------------------------JSON POST----------------------------\n");

foreach (json_decode(file_get_contents('php://input'), true) As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}
yaz("---------------------------NORMAL GET----------------------------\n");

foreach ($_GET As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}*/


?>