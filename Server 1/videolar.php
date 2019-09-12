<?php 

include "fonksiyon.php";
include "veritabani.php";
header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Custom-Header');
        
error_reporting(0);
$jsonDizi['bilgi'] = array();

$_metot = $_SERVER['REQUEST_METHOD'];
$son;
if($_metot == "GET"){
	$start = Temizle(trim($_GET["start"]));
    $token = TokenOku();

	$tokenDizisi = SifreliTokenCoz($token);
	$_MAIL = $tokenDizisi[0];
	$_PASS = $tokenDizisi[1];
	
		$sunucu = "localhost";
		$veritabani_adi = "";
		$kullanici_adi = "";
		$sifre = "";
		
	
	    
		$_kod = 200;
		$sql = "CALL sp_ALL_VIDEOS('$start')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
			    
			    extract($satir);
				$son = $satir['ID'];

				$baglanti2 =  mysqli_connect($sunucu,$kullanici_adi,$sifre,$veritabani_adi);
				$baglanti2->set_charset("utf8");
				if($baglanti2->connect_error){
					echo "Bağlantı Hatası.";
				}
				
			    $sql2 = "CALL sp_VIDEO_L('$_MAIL','$_PASS','$ID')";
			    
				$output2 = mysqli_query($baglanti2,$sql2);
				if(mysqli_num_rows($output2) > 0){
					while ($satir2 = mysqli_fetch_assoc($output2)) {
						extract($satir2);
						$ch = 0;
						$ch = $satir2['@p_CH'];
					}
					
				}
				
				mysqli_close($baglanti2);
				
				
				
				
				$bilgilerArray = array(
					"MUSIC_ID" => $ID, 
					"USER_ID" => $USER_ID,
					"N_NAME" => $N_NAME,
					"F_NAME" => $F_NAME,
					"L_NAME" => $L_NAME,
					"IMAGE" => $DOMAIN."profil/".$PIC_NO.".jpg",
					"REG_FRAME_ID" => $REG_FRAME_ID,
					"MUSIC_NAME" => $MUSIC_NAME,
					"MELODY_NAME_SURNAME" => $MELODY_NAME_SURNAME,
					"LIKES" => $LIKES,
					"COMMENT_TOTAL" => $COM,
					"DISLIKES" => $DISLIKES,
					"VIEWS" => $VIEWS,
					"CATEGORY_ID" => $CATEGORY_ID,
					"CATEGORY_NAME" => $CATEGORY_NAME,
					"VIDEO" => "http://127.0.0.1/server2/videos/".$VIDEO_NO.".mp4",
					"THUMB" => "http://127.0.0.1/server2/thumb/".$VIDEO_NO.".jpg",
					"UPLOAD_DATE" => $UPLOAD_DATE,
					"L" => $ch
				);
				array_push($jsonDizi["bilgi"], $bilgilerArray);
			  }
				
		

		}
	
		BaslikAyarla($_kod);
		$jsonDizi['SON_ID'] = $son;
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
        $jsonDizi = null;
	
}	
else{
	$_kod = 400;
	BaslikAyarla($_kod);
}




?>