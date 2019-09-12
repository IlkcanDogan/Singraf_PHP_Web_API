<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
		header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Custom-Header, API_KEY');
        $_POST = json_decode(file_get_contents('php://input'), true);
$_metot = $_SERVER['REQUEST_METHOD'];

$Dizi = array();
$Dizi = apache_request_headers();
$API_KEY = $Dizi["API_KEY"];

if($_metot == "POST" && $API_KEY == '15386b116a2a9e75fbd890841ed50aca'){

	$token = Temizle(trim($_POST["token"]));
	if($token != null){
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];

			$_kod = 200;
			$n_name;
			$sql = "CALL sp_MESSAGE_TOKEN('$mail','$pass')";
			$output = mysqli_query($baglanti,$sql);
			if(mysqli_num_rows($output)> 0){
				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$n_name = $satir['N_NAME'];
					$id = $satir['ID'];
					$image = $satir['PIC_NO'];
				}
			}
			$jsonDizi['N_NAME'] = $n_name;
			$jsonDizi['ID'] = $id;
			$jsonDizi['IMAGE'] = $DOMAIN."profil/".$image.".jpg";
			
			BaslikAyarla($_kod);
			echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}

}



?>