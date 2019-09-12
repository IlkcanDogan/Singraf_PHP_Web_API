<?php  
error_reporting(0);
$_metot = $_SERVER['REQUEST_METHOD'];
$_POST = json_decode(file_get_contents('php://input'), true);

if($_metot == "POST"){
  $no = htmlspecialchars($_POST["no"]);

  if (unlink('videos/'.$no.'.mp4')) {
    if(unlink('thumb/'.$no.'.jpg')){
      $jsonDizi = array('durum' => 'ok');
    }
    else{
      $jsonDizi = array('durum' => 'error');
    }
  }
  else{
    $jsonDizi = array('durum' => 'error');
  }
  echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
}

?>