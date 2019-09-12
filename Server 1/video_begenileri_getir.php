<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$jsonDizi['likedList'] = array();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "GET"){
	$music_id = Temizle(trim($_GET['muzik_id']));
	$start = Temizle(trim($_GET['start']));

	if($music_id != null){
		$_kod = 200;

		$son_id;
		$sql = "CALL sp_VIDEO_LIKED_INF('$start','$music_id')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output)> 0){

				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$son = $satir['ID'];

					$bilgilerArray = array (
						"USER_ID" => $USER_ID,
						"N_NAME" => $N_NAME,
						"F_NAME" => $F_NAME,
						"L_NAME" => $L_NAME,
						"IMAGE" => $DOMAIN."profil/".$PIC_NO.".jpg"
					);
					array_push($jsonDizi["likedList"], $bilgilerArray);

				}
		}

		
		BaslikAyarla($_kod);
		$jsonDizi['SON_ID'] = $son;
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}

}



?>