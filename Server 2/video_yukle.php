<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Custom-Header, API_KEY');
$_metot = $_SERVER['REQUEST_METHOD'];
$Dizi = array();
$Dizi = apache_request_headers();
$API_KEY = $Dizi["API_KEY"];
$TOKEN = $Dizi["Authorization"];
$music_name = urldecode(htmlspecialchars($_GET["muzik_adi"]));
$melody_name = urldecode(htmlspecialchars($_GET["yazar_adi"]));
$category_id = urldecode(Temizle(trim($_GET["kategori_id"])));
if ($_metot == "POST" && $API_KEY == "15386b116a2a9e75fbd890841ed50aca" && $music_name!= null && $melody_name!= null && $category_id != null
&& $TOKEN != null) {
	$video_no = null;
    $tip = $_FILES['video']['type'];
    $jsonCevap = array();
    $videoAdi = basename($_FILES['video']['name']);
        $uzanti = substr($videoAdi, strrpos($videoAdi, '.') + 1);
        if($_FILES){
            if($tip == "video/mp4" && $uzanti == "mp4" || $uzanti == "3gp"){
                $yeniVideoNo = rand(100000000, 999999999);
                $sonuc = 1;
                while ($sonuc == 1) {
                    
                    $sonuc = file_exists("video/".$yeniVideoNo.".mp4");
                    if($sonuc == 1){
                        $yeniResimNo = rand(100000000, 999999999);
                    }
                    
                }
                $yukleme_dizini = "video/".$yeniVideoNo.".mp4";
                if(move_uploaded_file($_FILES['video']['tmp_name'], $yukleme_dizini)){
                        $video_no = $yeniVideoNo;
						$komut2 = "ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 video/".$video_no.".mp4";
						$orjinalBoyut;
						if($orjinalBoyut = shell_exec($komut2)){
							$videoDosyasi = "video/".$video_no.".mp4";
							$ssBoyutuDizi = explode("x", trim($orjinalBoyut));
							$ssBoyutuX = $ssBoyutuDizi[0];
							$ssBoyutuY = $ssBoyutuDizi[1];
							$ssBoyutu = $ssBoyutuY."x".$ssBoyutuX;
							$resimDosyasi = $video_no.".jpg";
							$saniye = 0;
							
							$komut = "ffmpeg -i $videoDosyasi -an -ss 0 -s $ssBoyutu thumb/$resimDosyasi";
							if(!shell_exec($komut)){
								
								$link = 'https://ilkcandogan.com/video_yukle.php';
								$no = $video_no;
								//$api_key = 'ED801F932FB8BEDCE53ADB196B91CD12';
								$ip_adress = IpBul();
								
                                $komut3 = "ffmpeg -i $videoDosyasi -c:v libx264 videos/".$video_no.".mp4";
                                if(!shell_exec($komut3)){
                                    if(unlink($videoDosyasi)){
                                        if (Gonder($link, $no, $music_name, $melody_name, $category_id, $TOKEN, $ip_adress)) {
                                        $jsonCevap['hata'] = 0;
                                        }
                                        else{
                                            
                                            unlink($videoDosyasi);
                                            $jsonCevap['hata'] = 1;
                                        }
                                    }
                                     
                                }
                                else{
                                    unlink($videoDosyasi);
                                    $jsonCevap['hata'] = 'donusturme hatasi';
                                }
							}
							else{
								
								$jsonCevap['hata'] = 2;
							}
						}
						else{
							
							if (unlink('video/'.$video_no.'.mp4')) {
								
								$jsonCevap['hata'] = 3;
							}
							else{
								
								$jsonCevap['hata'] = 4;
							}
						}
                }
                else{
                    $jsonCevap['hata'] = 5;
                }
            }
            else{
            	$jsonCevap['hata'] = 6;
            }
        }
        else{
			$jsonCevap['hata'] = 7;
        }
}else{
	$jsonCevap['hata'] = 8;
}
echo json_encode($jsonCevap,JSON_UNESCAPED_UNICODE);
function Gonder($serverLink, $numara, $music_name, $melody_name, $category_id, $token, $ip){
	$durum = false;
	$JsonDizi = array(
		'no' => $numara,
		'muzik_adi' => $music_name,
		'yazar_adi' => $melody_name,
		'kategori_id' => $category_id,
		'ip_adresi' => $ip
	);
	$JsonData = json_encode($JsonDizi);
	$ch = curl_init($serverLink);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $JsonData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: '.strlen($JsonData),
		'Authorization: '.$token
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
        $veri =str_replace(' ',"",$veri);
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
?>