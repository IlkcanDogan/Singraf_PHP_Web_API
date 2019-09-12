<?php 
include "mail/class.phpmailer.php";
include "mail/class.smtp.php";
include('cipher/Crypt/RSA.php');

$DOMAIN = "https://ilkcandogan.com/";

error_reporting(0);
	 function HttpDurumKodlari($kod){
		$durum = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',  
        	307 => 'Temporary Redirect',  
       		400 => 'Bad Request',  
        	401 => 'Unauthorized',  
        	402 => 'Payment Required',  
        	403 => 'Forbidden',  
        	404 => 'Not Found',  
        	405 => 'Method Not Allowed',  
        	406 => 'Not Acceptable',  
        	407 => 'Proxy Authentication Required',  
        	408 => 'Request Timeout',  
        	409 => 'Conflict',  
        	410 => 'Gone',  
        	411 => 'Length Required',  
        	412 => 'Precondition Failed',  
        	413 => 'Request Entity Too Large',  
        	414 => 'Request-URI Too Long',  
        	415 => 'Unsupported Media Type',  
        	416 => 'Requested Range Not Satisfiable',  
        	417 => 'Expectation Failed',  
        	500 => 'Internal Server Error',  
        	501 => 'Not Implemented',  
        	502 => 'Bad Gateway',  
        	503 => 'Service Unavailable',  
        	504 => 'Gateway Timeout',  
        	505 => 'HTTP Version Not Supported');

		return $durum[$kod] ? $durum[$kod] : $durum[500];
	}

    function headerEkle(){
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Custom-Header');
        $_POST = json_decode(file_get_contents('php://input'), true);
    }

    function BaslikAyarla($kod){
		header("HTTP/1.1 ".$kod." ".HttpDurumKodlari($kod));
		header("Content-Type: application/json; charset=utf-8");
	}

    function Sifrele($parola){
        $saltDeger = "c8cbbeffbfd9c84d72f30bcbd907fc28";
        return md5($saltDeger.$parola);
    }

   function MailGonder($OnayToken,$mailAdresi,$domain){
        $mailHata = null;
    
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->Username = 'singraf.check@gmail.com';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls';
        $mail->SetFrom('singraf.check@gmail.com', 'Singraf');
        $mail->AddAddress($mailAdresi,$mailAdresi);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Singraf Onay Kodunuz';
        $mail->MsgHTML('<p>'.$domain.'onay.php?TOKEN='.$OnayToken.'</p>');

        if($mail->Send()){
            $mailHata = false;
        }else{
            $mailHata = $mail->ErrorInfo;
        }

        return $mailHata;

    }

    function SifremiUnuttumMailGonder($OnayTOKEN,$mailAdresi,$domain){
       $mailHata = null;
    
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->Username = 'singraf.check@gmail.com';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls';
        $mail->SetFrom('singraf.check@gmail.com', 'Singraf');
        $mail->AddAddress($mailAdresi,$mailAdresi);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Singraf Şifre Yenileme';
        $mail->MsgHTML('<p>'.$domain.'yeni_sifre.php?TOKEN='.$OnayTOKEN.'</p>');

        if($mail->Send()){
            $mailHata = false;
        }else{
            $mailHata = $mail->ErrorInfo;
        }

        return $mailHata;

    }
    
    function MailGonderHotmail($OnayToken,$mailAdresi,$domain){
        $mailHata = null;
    
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->Username = 'singraf.check@outlook.com';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls'; //STARTTLS
        $mail->SetFrom('singraf.check@outlook.com', 'Singraf');
        $mail->AddAddress($mailAdresi,$mailAdresi);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Singraf Onay Linki';
        $mail->isHTML(true);
        $mail->Body = '<a href='.$domain.'onay.php?TOKEN='.$OnayToken.'>Hesabını etkinleştirmek için tıkla!</a>';
        

        if($mail->Send()){
            $mailHata = false;
        }else{
            $mailHata = $mail->ErrorInfo;
        }

        return $mailHata;

    }

    function SifremiUnuttumMailGonderHotmail($OnayTOKEN,$mailAdresi,$domain){
       $mailHata = null;
    
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->Username = 'singraf.check@outlook.com';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls';
        $mail->SetFrom('singraf.check@outlook.com', 'Singraf');
        $mail->AddAddress($mailAdresi,$mailAdresi);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Singraf Şifre Yenileme Linki';
        $mail->isHTML(true);
        $mail->Body = '<a href='.$domain.'yeni_sifre.php?TOKEN='.$OnayTOKEN.'>Şifrenizi yenilemek için tıklayın!</a>';

        if($mail->Send()){
            $mailHata = false;
        }else{
            $mailHata = $mail->ErrorInfo;
        }

        return $mailHata;

    }
    //////
    function OnayKoduUret(){
        $onayKodu = rand(100000, 999999);
        return $onayKodu;
    }

    function IpBul(){

       if (!empty($_SERVER['HTTP_CLIENT_IP']))  
        {  
            $ip=$_SERVER['HTTP_CLIENT_IP'];  
        }  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
        {  
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];  
        }  
        else  
        {  
            $ip=$_SERVER['REMOTE_ADDR'];  
        }  
        
        return $ip;  
    }

    function Temizle($veri)
    {
        $veri =str_replace("`","",$veri);
        $veri =str_replace("=","",$veri);
        $veri =str_replace("&","",$veri);
        $veri =str_replace("%","",$veri);
        $veri =str_replace("!","",$veri);
        $veri =str_replace("#","",$veri);
        $veri =str_replace("<","",$veri);
        $veri =str_replace(">","",$veri);
        $veri =str_replace("*","",$veri);
        $veri =str_replace("/","",$veri);
        $veri =str_replace("+","",$veri);
        $veri =str_replace("-","",$veri);
        $veri =str_replace(",","",$veri);
        $veri =str_replace(";","",$veri);
        $veri =str_replace("?","",$veri);
        $veri =str_replace(")","",$veri);
        $veri =str_replace("(","",$veri);
        $veri =str_replace("{","",$veri);
        $veri =str_replace("}","",$veri);
        $veri =str_replace("]","",$veri);
        $veri =str_replace("[","",$veri);
        $veri =str_replace("$","",$veri);
        $veri =str_replace("£","",$veri);
        $veri =str_replace("´","",$veri);
        $veri =str_replace(":","",$veri);
        $veri =str_replace("é","",$veri);
        $veri =str_replace("|","",$veri);
        $veri =str_replace('^',"",$veri);
        //$veri =str_replace(' ',"",$veri);
        $veri =str_replace('"',"",$veri);
        $veri =str_replace("@","",$veri);
        $veri =str_replace("½","",$veri);
        $tersSlash = "\ ";
        $veri =str_replace(trim($tersSlash),"",$veri);
        $veri =str_replace("'","",$veri);
        $veri =str_replace("chr(34)","",$veri);
        $veri =str_replace("chr(39)","",$veri);
        return $veri;
    }

    function TemizleMail($veri)
    {
        $veri =str_replace("`","",$veri);
        $veri =str_replace("=","",$veri);
        $veri =str_replace("&","",$veri);
        $veri =str_replace("%","",$veri);
        $veri =str_replace("!","",$veri);
        $veri =str_replace("#","",$veri);
        $veri =str_replace("<","",$veri);
        $veri =str_replace(">","",$veri);
        $veri =str_replace("*","",$veri);
        $veri =str_replace("/","",$veri);
        $veri =str_replace("+","",$veri);
        $veri =str_replace(",","",$veri);
        $veri =str_replace(";","",$veri);
        $veri =str_replace("?","",$veri);
        $veri =str_replace(")","",$veri);
        $veri =str_replace("(","",$veri);   
        $veri =str_replace("{","",$veri);
        $veri =str_replace("}","",$veri);
        $veri =str_replace("]","",$veri);
        $veri =str_replace("[","",$veri);
        $veri =str_replace("$","",$veri);
        $veri =str_replace("£","",$veri);
        $veri =str_replace("´","",$veri);
        $veri =str_replace(":","",$veri);
        $veri =str_replace("é","",$veri);
        $veri =str_replace("|","",$veri);
        $veri =str_replace("_","",$veri);
        $veri =str_replace('^',"",$veri);
        $veri =str_replace(' ',"",$veri);
        $veri =str_replace('"',"",$veri);
        $veri =str_replace("½","",$veri);
        $tersSlash = "\ ";
        $veri =str_replace(trim($tersSlash),"",$veri);
        $veri =str_replace("'","",$veri);
        $veri =str_replace("chr(34)","",$veri);
        $veri =str_replace("chr(39)","",$veri);
        return $veri;
    }
/////////////////////////////////////////////////////////////////////////////////////////////
    function SifreliTokenCoz($token){
    	$SifresizVeri = null;
        $SifrelemeTuru = 'rc4';
        $Anahtar = '2bb19b79d85dea0da4f13a8a9a4e818e';
        $veri = base64_decode(strtr($token,'-_','+/') . str_repeat('=', 3 - ( 3 + strlen( $token )) % 4 ));
        $SifresizVeri = openssl_decrypt($veri, $SifrelemeTuru, $Anahtar);

        $rsa = new Crypt_RSA();
        $rsa->loadKey(file_get_contents('cipher/privatekey.txt'));
    	$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    	$dizi = explode("-", $rsa->decrypt(base64_decode($SifresizVeri)));
        
        if(count($dizi) > 2){

        	$uzunluk = count($dizi);
			$index =$uzunluk - 1; 
			$metin1 = $dizi[$index];

			$a;
			for ($i=0; $i < $index; $i++) { 
				$a = $a."-".$dizi[$i];
			}

			$dizi2[0] = ltrim($a,"-");
			$dizi2[1] = $metin1;
			return $dizi2;
        }
        else{
        	return $dizi;
        }
        
    }

    function SifreliTokenUret($data1,$data2){
    	$veri = $data1.'-'.$data2;

    	$rsa = new Crypt_RSA();
    	$rsa->LoadKey(file_get_contents('cipher/privatekey.txt'));
    	$rsa->LoadKey($rsa->getPublicKey());
    	$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

    	$sifreliVeri = null;
        $SifrelemeTuru = 'rc4';
        $Anahtar = '2bb19b79d85dea0da4f13a8a9a4e818e';

        $token = openssl_encrypt(base64_encode($rsa->encrypt($veri)), $SifrelemeTuru, $Anahtar);
        $token =  rtrim(strtr(base64_encode($token),'+/','-_'),'=');
    	return $token;  
    }
//////////////////////////////////////////////////////////////////////////////////////////

    function ProfilResmi(){
        $resim_no = null;
        $tip = $_FILES['image']['type'];
        $resimAdi = basename($_FILES['image']['name']);
        $uzanti = substr($resimAdi, strrpos($resimAdi, '.') + 1);
        if($_FILES){
            if(($tip == "image/jpeg" || $tip == "image/jpg" || $tip == "image/png") && ($uzanti == "JPG" || $uzanti == "jpg" || $uzanti == "PNG" || $uzanti == "png")){
                $yeniResimNo = rand(100000000, 999999999);
                $sonuc = 1;
                while ($sonuc == 1) {
                    
                    $sonuc = file_exists("profil/".$yeniResimNo.".jpg");
                    if($sonuc == 1){
                        $yeniResimNo = rand(100000000, 999999999);
                    }
                    
                }
                $yukleme_dizini = "profil/".$yeniResimNo.".jpg";

                $resim = getimagesize($_FILES['image']['tmp_name']);
                if(!(is_bool($resim))){
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $yukleme_dizini)){
                        $r = imagecreatefromjpeg($yukleme_dizini);
                        $boyutlar = getimagesize($yukleme_dizini);
                        imagejpeg($r,$yukleme_dizini,100);
                        // chmod($yukleme_dizini,0755);
                        $resim_no = $yeniResimNo;
                    }
                    else{
                        $resim_no = 333;
                    }


                }
                else{
                    $resim_no = 333;
                }
            }
            else{
                $resim_no= 333;
            }
        }
        else{
            $resim_no = 333;
        }

        
        return $resim_no;
    }


    function TokenOku(){
        $Dizi = array();

        $_serv = $_SERVER[''];
        $Dizi = apache_request_headers();
        return $Dizi["Authorization"];
    }  
    
    
    function PushSend($player_id, $n_name, $mesaj){  //One Signal Push Send API bağlantısı
        $content = array(
            "en" => $n_name.' '.$mesaj
        );

        $heading = array(
            "en" => "Singraf" 
        );
        $fields = array(
            'app_id' => "0000000-0000-0000-aea9-ed10af754f4c",
            'include_player_ids' => array($player_id),
            'data' => array("" => "",
                            "" => ""),
            'contents' => $content,
            'headings' => $heading
        );

        $fields = json_encode($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
    }


    
?>