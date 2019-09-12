<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$jsonDizi['bilgi'] = array();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();
	$music_id = Temizle(trim($_POST['muzik_id']));
	$yorum = htmlspecialchars($_POST['yorum']);
	
	$rpDizi = explode(" ", $yorum);
	$rp_name = $rpDizi[0];
	
	if(!(strlen($rp_name) <= 41 && strstr($rp_name, "@"))){
	    $rp_name = 333;
    }
    else{
        $rp_name = str_replace("@","",$rp_name);
    }
	

	if( $music_id != null && $yorum != null){
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];
		$IP = IpBul();
		$_kod = 200;

        $sql = $baglanti->prepare("CALL sp_COMMENT_POST(?,?,?,?,?,?,@PLAYER_ID,@N_NAME,@ERROR,@cmPLAYER_ID)");
		$sql->bind_param('ssssss',$mail,$pass,$music_id,$yorum,$IP,$rp_name);
		$sql->execute();
		
		$out1 = $baglanti->query("select @ERROR");
		$hata = $out1->FETCH_ASSOC();
		$hata = $hata['@ERROR'];

		$out2 = $baglanti->query("select @N_NAME");
		$n_name = $out2->FETCH_ASSOC();
		$n_name = $n_name['@N_NAME'];

		$out3 = $baglanti->query("select @PLAYER_ID");
		$ply_id = $out3->FETCH_ASSOC();
		$ply_id = $ply_id['@PLAYER_ID'];
		
		$out4 = $baglanti->query("select @cmPLAYER_ID");
		$CMply_id = $out4->FETCH_ASSOC();
		$CMply_id = $CMply_id['@cmPLAYER_ID'];
		
		
		if($hata == 1){
			$jsonDizi['hata'] = 1; //token hatası
		}
		elseif($hata == 2){
			$jsonDizi['hata'] = 2; //bu ID ye sahip video yok.
		}
		else{
			//array_push($jsonDizi["bilgi"], $bilgilerArray);
			if($rp_name == 333){
			    PushSend($ply_id,$n_name,' gönderinize yorum yaptı.');
			    $jsonDizi['hata'] = 0; //hata yok
			}
			else{
			    PushSend($CMply_id,$n_name,' yorumunuzu cevapladı.');
			    $jsonDizi['hata'] = 0; //hata yok
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