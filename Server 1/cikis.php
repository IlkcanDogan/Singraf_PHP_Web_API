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

		$sql = $baglanti->prepare("CALL sp_LOGOUT(?,?,@out_ERROR)");
		$sql->bind_param('ss',$mail,$pass);
		$sql->execute();

		$output = $baglanti->query("select @out_ERROR");
		$sp_Error = $output->FETCH_ASSOC();
		$sp_Error = $sp_Error['@out_ERROR'];

		
		if($sp_Error == 0){
			$jsonDizi['hata'] = 0;
		}
		elseif($sp_Error == 1){
			$jsonDizi['hata'] = 1;
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