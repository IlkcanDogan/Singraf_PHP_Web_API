<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();

	$video_id = Temizle(trim($_POST['video_id']));
	$yorum_id = Temizle(trim($_POST['yorum_id']));


	if($video_id != null && $yorum_id != null){
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];
		$_kod = 200;


		$sql = "call sp_SELF_VIDEO_COMM_DEL('$mail','$pass','$video_id','$yorum_id')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output)> 0){

			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
				$hata = $satir['@p_ERROR'];
			}
		}
		if($hata == 1){
			$jsonDizi['hata'] = 1; //kullanıcının böyle bir videosu yok
		}
		elseif($hata == 2){
			$jsonDizi['hata'] = 2; //kullanıcı videosunda bu id de yorum yok
		}
		else{
			$jsonDizi['hata'] = 111; //hata yok
		}

		
		BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}

}




/*function yaz($string){
	$dosya = fopen("loggg.txt", "a"); 
     fwrite($dosya, $string);
     fclose($dosya);
}
yaz("-------------------------FULL SERVER-----------------------------\n");

foreach ($_SERVER As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}

yaz("---------------------------HEADERS----------------------------\n");

foreach (apache_request_headers() As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}

yaz("---------------------------NORMAL POST----------------------------\n");

foreach ($_POST As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}
yaz("---------------------------JSON POST----------------------------\n");

foreach (json_decode(file_get_contents('php://input'), true) As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}
yaz("---------------------------NORMAL GET----------------------------\n");

foreach ($_GET As $Key => $Value){
    $string = "[".$Key."]".$Value."\n";
    yaz($string);
     	
}*/

?>