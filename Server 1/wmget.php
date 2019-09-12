<?php 

include "fonksiyon.php";
include "veritabani.php";

$jsonDizi = array();
$jsonDizi["mesajlar"] = array();
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

	$alici = Temizle(trim($_POST["nickname"]));
	if($alici != null){

			$_kod = 200;
			$sql = "call sp_WAIT_MESSAGE_SEND('$alici')";
			$output = mysqli_query($baglanti,$sql);
			if(mysqli_num_rows($output)> 0){
				while($satir = mysqli_fetch_assoc($output)){
						extract($satir);
						$bilgilerArray = array(
						"MESSAGE" => $MESSAGE, 
						"DATE" => $DATE,
						"SENDER" => $SENDER,
						"IMAGE" => $DOMAIN."profil/".$IMAGE.".jpg",
						"SENDER_ID" => $ID
					);
				array_push($jsonDizi["mesajlar"], $bilgilerArray);
				}
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