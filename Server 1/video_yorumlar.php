<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$jsonDizi['comments'] = array();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "GET"){
	$music_id = Temizle(trim($_GET['muzik_id']));
	$start = Temizle(trim($_GET['start']));
	$token = TokenOku();

	if($music_id != null){
		$_kod = 200;
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];

		$son_id;
		$sql = "CALL sp_COMMENT_GET('$mail','$pass','$music_id','$start')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output)> 0){

				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$son = $satir['COMMENT_ID'];
					$hata = $satir['@p_ERROR'];
					$bilgilerArray = array (
						"USER_ID" => $USER_ID,
						"N_NAME" => $N_NAME,
						"F_NAME" => $F_NAME,
						"L_NAME" => $L_NAME,
						"IMAGE" => $DOMAIN."profil/".$PIC_NO.".jpg",
						"REG_FRAME_ID" => $REG_FRAME_ID,
						"COMMENT" => $COMMENT,
						"COMMENT_ID" => $COMMENT_ID,
						"DATE" => $DATE
					);
					array_push($jsonDizi["comments"], $bilgilerArray);

				}
		}
		if($hata == 1){
			$jsonDizi['hata'] = 1; //video yok.
		}
		else{
			$jsonDizi['hata'] = 0;
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