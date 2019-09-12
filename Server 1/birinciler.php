<?php 

include "fonksiyon.php";
include "veritabani.php";

headerEkle();
error_reporting(0);
$jsonDizi['bilgi'] = array();

$_metot = $_SERVER['REQUEST_METHOD'];

$count = 0;
if($_metot == "GET"){
	if(true){
		$_kod = 200;
		$sql = "CALL sp_FIRSTS()";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
                $count++;
				$bilgilerArray = array( 
					"USER_ID" => $USER_ID,
					"N_NAME" => $N_NAME,
					"F_NAME" => $F_NAME,
					"L_NAME" => $L_NAME,
					"IMAGE" => $DOMAIN."profil/".$PIC_NO.".jpg",
					"REG_FRAME_ID" => $REG_FRAME_ID,
					"MUSIC_NAME" => $MUSIC_NAME,
					"MELODY_NAME_SURNAME" => $MELODY_NAME_SURNAME,
					"LIKES" => $LIKES,
					"VIEWS" => $VIEWS,
					"CATEGORY_ID" => $CATEGORY_ID,
					"CATEGORY_NAME" => $CATEGORY_NAME,
					"VIDEO" => "http://127.0.0.1/server2/videos/".$VIDEO_NO.".mp4",
					"THUMB" => "http://127.0.0.1/server2/thumb/".$VIDEO_NO.".jpg",
					"UPLOAD_DATE" => $UPLOAD_DATE
				);
				array_push($jsonDizi["bilgi"], $bilgilerArray);
			}

		}
		
		BaslikAyarla($_kod);
		if($count > 0){
			echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
		}
		else{
			$jsonDizi['uzunluk'] = 0;
			$count = 0;
			echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
		}
		
		

	}
}	
else{
	$_kod = 400;
	BaslikAyarla($_kod);
}

?>

