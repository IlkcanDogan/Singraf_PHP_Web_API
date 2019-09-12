<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();
	$yorum_id = Temizle(trim($_POST['yorum_id']));
	$yeniYorum = htmlspecialchars($_POST['yeni_yorum']);
	$sil = Temizle(trim($_POST['sil']));

	if($yeniYorum == null && $yeniYorum == ""){ 
	    $yeniYorum = "&X";
	    
	}
	
	if($sil == null){
	    $sil = "0";
	}
	if($yorum_id != null && $yeniYorum != null){
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];
		$_kod = 200;


		$sql = "call sp_COMMENT_EDIT ('$mail','$pass','$yorum_id','$sil','$yeniYorum')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output)> 0){

			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
				$hata = $satir['@p_ERROR'];
			}
		}
		if($hata == 1){
			$jsonDizi['hata'] = 1; //token hatası.
		}
		elseif($hata == 2){
			$jsonDizi['hata'] = 2; //kullanıcının boyle yorumu yok.
		}
		else{
			$jsonDizi['hata'] = 0; //hata yok
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