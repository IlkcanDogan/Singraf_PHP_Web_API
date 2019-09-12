<?php  

include "veritabani.php";
include "fonksiyon.php";

error_reporting(0); 
headerEkle();
$jsonDizi = array();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$mail = TemizleMail(trim($_POST["mail"]));

	if($mail != null){
		$_kod = 200;
		$onayKodu = OnayKoduUret();
		$sql = "CALL sp_FORGET_PASS('$mail','$onayKodu')";
		$output = mysqli_query($baglanti,$sql);
		if(mysqli_num_rows($output) > 0){
			while($satir = mysqli_fetch_assoc($output)){
				extract($satir);
				$hata = $satir['@p_ERROR'];
			}
		}
		if($hata == 111){
		    $hangiMail = MailSec($mail);
		    if($hangiMail == 'gmail'){
		        $jsonDizi['hata'] = 0; //Onay linki gönderildi.
			    $mailSonucu = SifremiUnuttumMailGonder(SifreliTokenUret($mail,$onayKodu),$mail,$DOMAIN);
		    }
		    else if($hangiMail == 'hotmail'){
		        $jsonDizi['hata'] = 0; //Onay linki gönderildi.
			    $mailSonucu = SifremiUnuttumMailGonderHotmail(SifreliTokenUret($mail,$onayKodu),$mail,$DOMAIN);
		    }
		    
			
			$jsonDizi['mailHata'] = $mailSonucu;
			
		}
		elseif($hata == 1){
			$jsonDizi["hata"] = 1; //Mail adresi bulunamadı!.
		}

		BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}
}

function MailSec($mail){
    $mailDurum;
    
    $Maildizi = explode("@", $mail);
    $diziUzunluk = count($Maildizi);
    
    if($Maildizi[$diziUzunluk-1] == "gmail.com" || $Maildizi[$diziUzunluk-1] == "yandex.com"){
		$mailDurum = 'gmail';
	}
	else if($Maildizi[$diziUzunluk-1] == "hotmail.com" || $Maildizi[$diziUzunluk-1] == "outlook.com" || $Maildizi[$diziUzunluk-1] == "outlook.com.tr"){
	    $mailDurum = 'hotmail';
	}
	
	return $mailDurum;
}

?>