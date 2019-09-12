<?php 
include "veritabani.php";
include "fonksiyon.php";

error_reporting(0); 
headerEkle();
$jsonDizi = array();

$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$mail = TemizleMail(trim($_POST["mail"]));
	$pass = TemizleMail(trim($_POST["pass"]));
	$uuid = TemizleMail(trim($_POST["uuid"]));
		
	if($mail != null && $pass != null && $uuid != null){
		$_kod = 200;
		$pass = Sifrele($pass);
		
		$sql = "CALL sp_LOGIN('$mail','$pass','$uuid')";
		$output = mysqli_query($baglanti,$sql);
		if (mysqli_num_rows($output) > 0) {
			$SifreliToken = SifreliTokenUret($mail,$pass);
			while ($satir = mysqli_fetch_assoc($output)) {
				extract($satir);
				$hata = $satir['@p_ERROR'];
				if($hata != 111){
					$N_NAME = $satir['N_NAME'];	
					$jsonDizi= array(
						"ID" => $ID,
						"N_NAME" => $N_NAME,
						"F_NAME" => $F_NAME,
						"L_NAME" => $L_NAME,
						"MAIL" => $MAIL,
						"CITY" => $CITY,
						"B_DATE" => $B_DATE,
						"SCORE" => $SCORE,
						"BIO" => $BIO,
						"COIN" => $COIN,
						"IMAGE" => $DOMAIN.'profil/'.$PIC_NO.'.jpg',
						"REG_FRAME_ID" => $REG_FRAME_ID
					);		
				}			
				
			}
			
		}
		if($N_NAME != null){
			$jsonDizi["TOKEN"] = $SifreliToken;
			
		}
		if($hata == 1){
			$jsonDizi['hata'] = 1; //Böyle bir mail hesabı yok.(Eğer bu yanlışsa Tüm bilgiler yanlış.)
		}
		elseif($hata == 2){
			$JsonDizi['hata'] = 2; //Şifre yanlış
		}
		elseif($hata == 3){
			$jsonDizi['hata'] = 3; //Hesap Banlanmış.
		}
		elseif($hata == 4){
			$jsonDizi['hata'] = 4; //Hesap Aktif Değil.
		}
		elseif($hata == 5){
			$jsonDizi['hata'] = 5; // Bu hesap Başka bir cihazda zaten aktif (LOGIN olunmuş durumda.)
		}
		else{ 
			$jsonDizi["hata"] = 0; //Giriş Başarılı.
			
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