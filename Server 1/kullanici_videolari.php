<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
$jsonDizi['bilgi'] = array();

$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "GET"){
	$id = Temizle(trim($_GET["id"]));
	if($id != null && $id != 0){
		$_kod = 200;
		$sql = "CALL sp_VIDEO_GET('$id')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
				$bilgilerArray = array(
					"MUSIC_ID" => $ID,
					"MUSIC_NAME" => $MUSIC_NAME,
					"MELODY_NAME_SURNAME" => $MELODY_NAME_SURNAME,
					"LIKES" => $LIKES,
					"VIEWS" => $VIEWS,
					"CATEGORY_ID" => $CATEGORY_ID,
					"VIDEO" => $DOMAIN."video/".$VIDEO_NO.".mp4",
					"UPLOAD_DATE" => $UPLOAD_DATE
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