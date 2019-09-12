<?php  

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

//
if($_metot == "POST"){
  $token = TokenOku();
  $video_id = Temizle(trim(urldecode($_POST['video_id'])));
  $music_name = htmlspecialchars(urldecode($_POST['muzik_adi']));
  $melody_name_surname =  htmlspecialchars(urldecode($_POST['muzik_yazar']));
  $category_id = Temizle(trim($_POST['kategori_id']));
  $delete = Temizle(trim($_POST['sil']));
  if($token != null){
    $tokenDizisi = SifreliTokenCoz($token);
    $mail = $tokenDizisi[0];
    $pass = $tokenDizisi[1];

    if($music_name == null) $music_name = 333;
    if($melody_name_surname == null) $melody_name_surname = 333;
    if($category_id == null) $category_id = 333;
    if($delete == null && $delete != "1") $delete = 3; // bilerek 3 yaptım.
    $_kod = 200;

    $sql = "CALL sp_VIDEO_EDIT('$mail','$pass','$music_name','$melody_name_surname','$category_id','$delete','$video_id')";
    $output = mysqli_query($baglanti,$sql);
    if(mysqli_num_rows($output)> 0){

        while($satir = mysqli_fetch_assoc($output)){
          extract($satir);
          $hata = $satir['@p_ERROR'];

        }
    }
    if($hata == 111){
      $jsonDizi['hata'] = 0; //hata yok
    }
    elseif($hata == 1){
      $jsonDizi['hata'] = 1; //token hatası.
    }
    elseif($hata == 2){
      $jsonDizi['hata'] = 2; //Video yok(id hatası) kullanıcıya ait birşey yok.
    }
    else{
      
        if (Gonder('http://127.0.0.1/server2/sil.php', $hata)) {
		    
		    $jsonCevap['hata'] = 0;
	    }
	    else{
	        
		    $jsonCevap['hata'] = 1;
	    }

    }

    
    BaslikAyarla($_kod);
    echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
  }
  else{
    
  }

}
else {
    $_kod = 400;
    BaslikAyarla($_kod);

}

function Gonder($serverLink, $numara){
	$durum = false;
	$JsonDizi = array(
		'no' => $numara
	);
	$JsonData = json_encode($JsonDizi);
	$ch = curl_init($serverLink);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $JsonData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: '.strlen($JsonData)
		)
	);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$cevap = curl_exec($ch);
	$gelenVeri = json_decode($cevap);
	foreach ($gelenVeri as $anahtar => $deger) {
		if ($deger == "ok") {
			$durum = true;
		}
	}
	return $durum;
	
}


?>