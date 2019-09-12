<?php
include "fonksiyon.php";
include "veritabani.php";

ini_set('max_execution_time', 3000);
error_reporting(0);
headerEkle();

$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();
	
	$music_name = htmlspecialchars($_POST["muzik_adi"]);
	$melody_name = htmlspecialchars($_POST["yazar_adi"]);
	$category_id = Temizle(trim($_POST["kategori_id"]));
    $video_no = trim($_POST["no"]);
    $ip = htmlspecialchars(trim($_POST["ip_adresi"]));
    						
    					
	if($token != null && $music_name != null && $melody_name != null && $category_id != null && $video_no != null && $ip != null){
		$_kod = 200;
		$tokenDizisi = SifreliTokenCoz($token);
		$mail = $tokenDizisi[0];
		$pass = $tokenDizisi[1];
		$hata;
		$sql = "CALL sp_VIDEO_UPLOAD('$mail','$pass','$music_name','$melody_name','$category_id', '$video_no','$ip')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
				$hata = $satir['p_ERROR'];
			}
		}

		if($hata == 1){
			$jsonDizi = array('durum' => 'error');
		}
		elseif($hata == 0){
           $jsonDizi = array('durum' => 'ok');
		}
		BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
  }

}
else{
	$_kod = 400;
	BaslikAyarla($_kod);
}


     
?>