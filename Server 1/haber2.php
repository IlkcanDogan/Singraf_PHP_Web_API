<?php 
include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
ini_set('max_execution_time', 300);


$abcd = 0;
$a1 = array();
$a2 = array();
$a3 = array();
$a4 = array();
$a5 = array();

$sorgu = array();
$gelenLink = array();
	$sql = "select LINK from tb_news";
	$output = mysqli_query($baglanti, $sql);
	$cb;
	while($satir = mysqli_fetch_assoc($output)){
		$gelenLink[] = $satir['LINK'];
	}
$diziUzunluk = count($gelenLink);


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


DON:
if($abcd <$diziUzunluk){
	$site = $gelenLink[$abcd];

$icerik = file_get_contents($site);
$baslik = ara('<h1 class="news-detail__title">', "</h1>", $icerik);
$tarih = ara('</span>', "</time>", $icerik);




$pattern = '@<div class="content-text">(.*?)</div>@si';
preg_match_all($pattern,$icerik,$yazilar);


$Resimler = serialize($yazilar[1]);
$Resimler = ara('src="','"',$Resimler);
$rIndex = count($Resimler);

$ReSim = array();
$h = 0;
for ($i=0; $i <$rIndex; $i++) {
	$dresim = $Resimler[$i];

	if (!(strstr($dresim, "https://platform.twitter.com"))) {
		if (!(strstr($dresim, "video-embed"))) { 
			
			$ReSim[$h] = $dresim;
			$h++;
		}
		
	}

}

////////////////
$X = count($yazilar[1]);
$Y;
for ($i=0; $i <$X; $i++) { 
	$Y = $yazilar[1][$i];
	
}

$tYazilar = $Y;
$tYazilar = str_replace('<h2 class="news-detail__description">', "", $tYazilar);

$uzunluk = strlen($tYazilar) + 17;
$tYazilar = str_replace('a:1:{i:0;s:'.$uzunluk.':"',"", $tYazilar);
$tYazilar = str_replace('";}',"", $tYazilar);
$tYazilar = str_replace('<strong>', "", $tYazilar);
$tYazilar = str_replace('</strong>', "", $tYazilar);
$tYazilar = str_replace('</p>', "", $tYazilar);
$tYazilar = str_replace('<p>', "", $tYazilar);
$tYazilar = str_replace('</h2>', "", $tYazilar);
$tYazilar = str_replace('<h2>', "", $tYazilar);
$tYazilar = str_replace('<br>', "", $tYazilar);
$tYazilar = str_replace('<br>', "", $tYazilar);
$tYazilar = str_replace('</a>', "", $tYazilar);
$tYazilar = str_replace('"', " ", $tYazilar);
$tYazilar = str_replace("'", " ", $tYazilar);


$pattern = '@<img src="(.*?)>@si';
preg_match_all($pattern,$tYazilar,$yazilar);
$rIndex2 = count($yazilar[1]);

for ($i=0; $i <$rIndex2; $i++) { 
	$string = $yazilar[0][$i];
	$tYazilar = str_replace($string, "", $tYazilar);
}


$pattern = '@<figure class="embed-responsive(.*?)figure>@si';
preg_match_all($pattern,$tYazilar,$yazilar);
$vIndex = count($yazilar[1]);

for ($i=0; $i <$vIndex; $i++) { 
	$string = $yazilar[0][$i];
	$tYazilar = str_replace($string, "", $tYazilar);
}



$pattern = '@<blockquote class="(.*?)</script>@si';
preg_match_all($pattern,$tYazilar,$yazilar);
$vIndex = count($yazilar[1]);

for ($i=0; $i <$vIndex; $i++) { 
	$string = $yazilar[0][$i];
	$tYazilar = str_replace($string, "", $tYazilar);
}

$pattern = '@<a href="(.*?)">@si';
preg_match_all($pattern,$tYazilar,$yazilar);
$vIndex = count($yazilar[1]);

for ($i=0; $i <$vIndex; $i++) { 
	$string = $yazilar[0][$i];
	$tYazilar = str_replace($string, "", $tYazilar);
}

$a1[$abcd] = $baslik[0];
$a2[$abcd] = $tarih[0];
$a3[$abcd] = $tYazilar;

$img1;
$img2;

$rUzunluk = count($ReSim);
if($rUzunluk > 0){
	$a4[$abcd] = $ReSim[0];
	$a5[$abcd] = $ReSim[1];
}


$abcd++;
GOTO DON;
}

$sql2 = "truncate tb_news_content";
if(mysqli_query($baglanti, $sql2)){
for ($i=0; $i <=$abcd ; $i++) {

		$c1 = $a1[$i];
		$c2 = $a2[$i];
		$c3 = $a3[$i];
		$c4 = $a4[$i];
		$c5 = $a5[$i];
    
		if ($c1 !=null && $c2 != null && $c3 != null) {
			$sql = "insert into tb_news_content(TITLE,DATE,CONTENT,IMG1,IMG2) values('$c1','$c2','$c3','$c4','$c5')";
			if(mysqli_query($baglanti, $sql)){

			} else{
    			echo "hata" . mysqli_error($baglanti);
			}
		}

	
  }
  
}


 ?>
