<?php  
include "fonksiyon.php";
include "veritabani.php";
error_reporting(0);

$pass = htmlspecialchars($_POST["sifre"]);
$token = htmlspecialchars($_POST["token"]);

if(($pass != null && $pass >= 8) && $token != null){
      $pass = Sifrele($pass);
      
      $TOKENDizisi = SifreliTokenCoz($token);
      $mail = $TOKENDizisi[0];
      

     		 $sql = "CALL sp_FORGET_PASS_CHECK('$mail','$pass')";
      		 $output = mysqli_query($baglanti,$sql);
      		 if(mysqli_num_rows($output)> 0){
       			while($satir = mysqli_fetch_assoc($output)){
          			extract($satir);
          			$hata = $satir['@p_ERROR'];
        		}
      		}
      
      		if($hata == 111){
        		//echo "<label for='exampleInputEmail1'><h2>Şifreniz Değiştirildi.</h2></label>";
            $jsonDizi['durum'] = 'ok';
            echo json_encode($jsonDizi);
      		}
      		elseif($hata == 1){
          		//echo "<label for='exampleInputEmail1'>Şifreniz Değiştirilemedi.</label>";
            $jsonDizi['durum'] = 'er';
            echo json_encode($jsonDizi);
      		}
    } 



?>