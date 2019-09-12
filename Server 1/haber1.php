<?php 
include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
ini_set('max_execution_time', 3000);


function ara($bas, $son, $yazi)
{
    @preg_match_all('/' . preg_quote($bas, '/') .
    '(.*?)'. preg_quote($son, '/').'/i', $yazi, $m);
    return @$m[1];
}

function baslikFonk($baslik){
	$gelenBaslik = array();
	$index = count($baslik);
	$b = 0;
	for ($i=15; $i < $index; $i++) { 
		$veri = $baslik[$i];
		if($veri == "ÇOK OKUNANLAR"){
			break;
		}
		else{
			 $gelenBaslik[$b]= $veri;
			 $b = $b + 1;
		}
	}
	return $gelenBaslik;
}


function linkFonk($link){
	$gelenLink = array();
	$index = count($link);
	$a = 0;
	for ($i=0; $i <$index; $i++) { 
		$veri = $link[$i];
		if ($i > 44) {
			if ($veri !='https://www.internethaber.com/son-dakika"') {	
				$gelenLink[$a] = $veri;
				$a = $a + 1;
			}
			else{
				break;
			}
			
		}
	}

	return $gelenLink;
}

function resimFonk($resim){
	$gelenResim = array();
	$rindex = count($resim);

	for ($i=0; $i <$rindex; $i++) { 
		$veri = $resim[$i];
		$gelenResim[$i] = $veri;
	}
	return $gelenResim;
}



$sayfa = 0;

DON:
$sayfa++;
if($sayfa <= 4){
$site = "https://www.internethaber.com/magazin?page=".$sayfa;
$icerik = file_get_contents($site);

$baslik = ara("<span>", "</span>", $icerik);
$link = ara('<a href="','"', $icerik);
$resim = ara('<picture><img src="','"', $icerik);

$Basliklar = array();
$Basliklar = baslikFonk($baslik);
$bIndex = count($Basliklar);


$hbrLink = array();
$Linkler = array();
$Linkler = linkFonk($link);
$LIndex = count($Linkler);
$p = 0;
for ($i=16; $i < $LIndex; $i++) {

	if ($i< 29) {
		$hbrLink[$p] = $Linkler[$i];
		$p = $p + 1;
	}
}

$hbrResim = array();
$Resimler = array();
$Resimler = resimFonk($resim);
$RIndex = count($Resimler);
$s = 0;
for ($i=13; $i <26; $i++) { 
	$hbrResim[$s] = $Resimler[$i];
	$s = $s + 1;

}


$sql2 = "truncate tb_news";
if(mysqli_query($baglanti, $sql2)){
    for ($i=0; $i <13; $i++) {

	$p_Baslik = $Basliklar[$i];
	$p_Link = $hbrLink[$i];
	$p_Resim = $hbrResim[$i];

	if($p_Baslik != null && $p_Resim != null){
    
    	
	$sql = "insert into tb_news(TITLE,LINK,IMG_LINK) values('$p_Baslik','$p_Link','$p_Resim')";
	if(mysqli_query($baglanti, $sql)){

    	

	} else{
    	echo "hata" . mysqli_error($link);
	}

	}

 }
}


GOTO DON;
}
else{
	
}


?>