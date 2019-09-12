<?php 
include "veritabani.php";
include "fonksiyon.php";

error_reporting(0);
headerEkle();
$jsonDizi = array();

$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$n_name = Temizle(trim($_POST["n_name"]));
	$f_name = Temizle(trim($_POST["f_name"]));
	$l_name = Temizle(trim($_POST["l_name"]));
	$mail = TemizleMail(trim($_POST["mail"]));
	$pass = TemizleMail(trim($_POST["pass"]));
	
	
	if($n_name != NULL && $f_name != NULL && $l_name != NULL && $mail != NULL && $pass != NULL){
		$_kod = 200;
		$pass = Sifrele($pass);
		$check_code = OnayKoduUret();
		$ip = IpBul();
        $kontrolDurum = MailKontrol($mail);
        $destekMail = DesteklenenMail($mail);
        
        $hangiMail = MailSec($mail);
    if($kontrolDurum == true && $destekMail == true){   
		$sql = $baglanti->prepare("CALL sp_REGISTER(?,?,?,?,?,?,?,@out_ERROR)");
		$sql->bind_param('sssssss',$n_name,$f_name,$l_name,$mail,$pass,$check_code,$ip);
		$sql->execute();

		$output = $baglanti->query("select @out_ERROR");
		$sp_Error = $output->FETCH_ASSOC();
		$sp_Error =  $sp_Error['@out_ERROR'];

    }
    
    
       	if($sp_Error == 0 && $kontrolDurum == true && $destekMail == true){
       		$jsonDizi['hata'] = 0; //Kayıt olundu.
            
            if($hangiMail == 'gmail'){
                $mailSonucu = MailGonder(SifreliTokenUret($n_name,$check_code),$mail,$DOMAIN);
                $jsonDizi['mailHata'] = $mailSonucu;
            }
    		else if($hangiMail == 'hotmail'){
    		    $mailSonucu = MailGonderHotmail(SifreliTokenUret($n_name,$check_code),$mail,$DOMAIN);
    		    $jsonDizi['mailHata'] = $mailSonucu;
    		}
    		
       		
       		
       	}
       	elseif($sp_Error == 1){
       		$jsonDizi['hata'] = 1; //kullanıcı adı kullanılıyor.
       	}
       	elseif($sp_Error == 2){
       		$jsonDizi['hata'] = 2; //mail kullanılıyor.
       	}
       	else if($kontrolDurum == false){
       	    $jsonDizi['hata'] = 2;
       	}else if($kontrolDurum == true && $destekMail == false){
       	    $jsonDizi['hata'] = 5;
       	}
       	BaslikAyarla($_kod);
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
       
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}
	
}

function MailKontrol($eposta){
    $durum = false;
    if (filter_var($eposta, FILTER_VALIDATE_EMAIL) ){ 
        $durum = true;
    }
    
    return $durum;
}

function DesteklenenMail($mail){
	$durum = false;

	$Maildizi = explode("@", $mail);
	$diziUzunluk = count($Maildizi);

	$DesteklenenMailarray = array("gmail.com","yandex.com", "hotmail.com", "outlook.com","outlook.com.tr");
	
	if(in_array($Maildizi[$diziUzunluk-1], $DesteklenenMailarray)){
		$durum = true;
	}
	

	return $durum;
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