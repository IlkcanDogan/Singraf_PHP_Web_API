<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];
$son;
$jsonDizi['profil'] = array();
$jsonDizi['video'] = array();
 if($_metot == "GET"){
 	$id = Temizle(trim($_GET['id']));
 	$token = TokenOku();
    
	$tokenDizisi = SifreliTokenCoz($token);
	$_MAIL = $tokenDizisi[0];
	$_PASS = $tokenDizisi[1];
 	
 	if($id != null && ($id != 0 || $id != '')){
 		$_kod = 200;
 		$sql = "CALL sp_PROFILE_GET('$id')";
 		$output = mysqli_query($baglanti,$sql);
 		if(mysqli_num_rows($output) > 0){
 			while($satir = mysqli_fetch_assoc($output)){
 				extract($satir);
 				   if($B_DATE == null){
 				       $B_DATE = '-';
 				   }
                                   if($CITY == null){
 				       $CITY = '-';
 				   }
                                   
                                   $F_NAME2 = str_replace(' ', "_", $F_NAME);
                                   $L_NAME2 = str_replace(' ', "_", $L_NAME);
 				$profilArray = array(
 					"N_NAME" => $N_NAME,
 					"F_NAME" => $F_NAME,
 					"L_NAME" => $L_NAME,
 					"CITY" => $CITY,
 					"B_DATE" => $B_DATE,
 					"BIO" => $BIO,
 					"COIN" => $COIN,
 					"SCORE" => $SCORE,
 					"INSTAGRAM" => $INSTAGRAM,
 					"TWITTER" => $TWITTER,
 					"FACEBOOK" => $FACEBOOK,
 					"IMAGE" => $DOMAIN."profil/".$PIC_NO.".jpg",
 					"REG_FRAME_ID" => $REG_FRAME_ID,
                                        "F_NAME2" => $F_NAME2,
                                        "L_NAME2" => $L_NAME2
 				);
 				array_push($jsonDizi["profil"], $profilArray);
 			}
 			
 		} 
 		//////////////////////////////////
 		$sunucu = "localhost";
		$veritabani_adi = "";
		$kullanici_adi = "";
		$sifre = "";
	
			$baglanti2 = new mysqli($sunucu,$kullanici_adi,$sifre,$veritabani_adi);
			$baglanti2->set_charset("utf8");
			if($baglanti2->connect_error){
				echo "Bağlantı Hatası.";
			}
 		/////////////////////////////////
 		$sql2 = "CALL sp_PROFILE_GET_VIDEO('$id')";
 		$output = mysqli_query($baglanti2,$sql2);
 		if(mysqli_num_rows($output) > 0){
 			while($satir = mysqli_fetch_assoc($output)){
 				extract($satir);

				$baglanti3 =  mysqli_connect($sunucu,$kullanici_adi,$sifre,$veritabani_adi);
				$baglanti3->set_charset("utf8");
				if($baglanti3->connect_error){
					echo "Bağlantı Hatası.";
				}
				
			    $sql3 = "CALL sp_VIDEO_L('$_MAIL','$_PASS','$MUSIC_ID')";
			    
				$output3 = mysqli_query($baglanti3,$sql3);
				if(mysqli_num_rows($output3) > 0){
					while ($satir3 = mysqli_fetch_assoc($output3)) {
						extract($satir3);
						$ch = 0;
						$ch = $satir3['@p_CH'];
					}
					
				}
				
				mysqli_close($baglanti3);
 				
 				
 				
 				$bilgilerArray = array(
 					"MUSIC_ID" => $MUSIC_ID,
 					"MUSIC_NAME" => $MUSIC_NAME,
 					"MELODY_NAME_SURNAME" => $MELODY_NAME_SURNAME,
 					"LIKES" => $LIKES,
 					"VIEWS" => $VIEWS,
 					"CATEGORY_ID" => $CATEGORY_ID,
 					"CATEGORY_NAME" => $CATEGORY_NAME,
 					"VIDEO" => "http://127.0.0.1/server2/videos/".$VIDEO_NO.".mp4",
					"THUMB" => "http://127.0.0.1/server2/thumb/".$VIDEO_NO.".jpg",
 					"UPLOAD_DATE" => $UPLOAD_DATE,
 					"L" => $ch
 				);
 				array_push($jsonDizi["video"], $bilgilerArray); 
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