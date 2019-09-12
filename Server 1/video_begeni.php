<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];
if($_metot == "POST"){
    $token = TokenOku();
	$muzik_id = Temizle(trim($_POST["muzik_id"]));
	$begeni = Temizle(trim($_POST["begeni"]));
	$begenme = Temizle(trim($_POST["begenme"]));


	if($token != null && $muzik_id != null && ($begeni != null || $begenme != null)){
		$_kod = 200;
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];
	    if($begeni == 1){
	        	$sql = $baglanti->prepare("CALL sp_VIDEO_LIKE_VIEW(?,?,?,?,?,@PLAYER_ID,@N_NAME,@ERROR)");
		        $sql->bind_param('sssss',$mail,$pass,$muzik_id,$begeni,$izlenme);
		        $sql->execute();
	    }
	    else if($begenme == 0){
	            $sql = $baglanti->prepare("CALL sp_DISLIKE(?,?,?,?,@ERROR)");
		        $sql->bind_param('ssss',$mail,$pass,$muzik_id,$begenme);
		        $sql->execute();
	    }

		$out1 = $baglanti->query("select @ERROR");
		$hata = $out1->FETCH_ASSOC();
		$hata = $hata['@ERROR'];

		$out2 = $baglanti->query("select @N_NAME");
		$n_name = $out2->FETCH_ASSOC();
		$n_name = $n_name['@N_NAME'];

		$out3 = $baglanti->query("select @PLAYER_ID");
		$ply_id = $out3->FETCH_ASSOC();
		$ply_id = $ply_id['@PLAYER_ID'];

		if($hata == 111){
		    if($begeni == 1){
		        PushSend($ply_id,$n_name,'paylaşımınızı beğendi.');
		    }
			
			$jsonDizi['hata'] = 0; //işlem başarılı.
			
		}
		elseif($hata == 1){
			$jsonDizi['hata'] = 1; //token / kullanıcı hatası.
		}
		elseif($hata == 2){
			$jsonDizi['hata'] = 2; // zaten beğenmiş.
		}
		elseif ($hata == 3) {
			$jsonDizi['hata'] = 3; //ilk önce beğenmesi gerekki geri alabilsin beğeniyi.
		}
		elseif($hata == 4){
			$jsonDizi['hata'] = 4; //zaten izlemiş.
		}
		elseif($hata == 5){
			$jsonDizi['hata'] = 5; //bu ID'ye sahip video yok.
		}
		elseif($hata == 6){
			$jsonDizi['hata'] = 6; //zaten dislike etmiş.
		}
		elseif ($hata == 7) {
			$jsonDizi['hata'] = 7; //zaten dislike kaldırmış.
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