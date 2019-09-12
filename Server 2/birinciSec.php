<?php  
error_reporting(0);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Custom-Header, API_KEY');
$_metot = $_SERVER['REQUEST_METHOD'];


$_POST = json_decode(file_get_contents('php://input'), true);
$Dizi = array();
$Dizi = apache_request_headers();
$API_KEY = $Dizi["API_KEY"];
$jsonDizi;


	if($_metot == "POST" && $API_KEY = "13dd183da96f031438d991f49f04f0b4") {
		$arrListe1 = array();
		$arrListe1 = dosyaListele("videos/");
		$arrListe2 = array();
		$arrListe2 = dosyaListele("thumb/");
		foreach ($_POST as $deger) {
			$arrListe1 = array_values(array_diff($arrListe1, array($deger.'.mp4')));
			$arrListe2 = array_values(array_diff($arrListe2, array($deger.'.jpg')));
		}
		foreach ($arrListe1 as $deger) {
			unlink('videos/'.$deger);
		}
		foreach ($arrListe2 as $deger) {
			unlink('thumb/'.$deger);
		}
		$jsonDizi = array('durum' => 'ok');
		echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		echo "hata";
	}


function dosyaListele($dizin){
    $pDizi = array();
    $dizin = opendir($dizin);
    while (($dosya = readdir($dizin)) !== false) {
        if(!is_dir($dosya)){
            array_push($pDizi, $dosya);
        }
    }
    closedir($dizin);
    return $pDizi;
}

?>