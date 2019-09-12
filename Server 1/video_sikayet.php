<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();
	$video_id = Temizle(trim($_POST['video_id']));

	if($token != null && $video_id != null){
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];
		$_kod = 200;

		$sql = "CALL sp_MUSIC_REPORTS('$mail','$pass','$video_id')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output)> 0){

				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$hata = $satir['@p_ERROR'];

				}
		}
		if($hata == 111){
			$jsonDizi['hata'] = 0; //işlem başarılı.
		}
		elseif($hata == 1){
			$jsonDizi['hata'] = 1; //token hatası.
		}
		elseif($hata == 2){
			$jsonDizi['hata'] = 2; //Video yok.
		}
		elseif($hata == 3){
			$jsonDizi['hata'] = 3; //zaten şikayet edildi.
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