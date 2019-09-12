<?php 

include "fonksiyon.php";
include "veritabani.php";

error_reporting(0);
headerEkle();
$_metot = $_SERVER['REQUEST_METHOD'];

if($_metot == "POST"){
	$token = TokenOku();
	
	$_N_NAME = Temizle(trim($_POST["n_name"]));
	$_F_NAME = Temizle(trim($_POST["f_name"]));
	$_L_NAME = Temizle(trim($_POST["l_name"]));
	$_CITY = Temizle(trim($_POST["city"]));
	$_BIO = htmlspecialchars($_POST["bio"]);
	$_PIC_NO = Temizle(trim($_POST["image"]));
	$_B_DATE = Temizle(trim($_POST["b_date"]));
		/**/
	$_S_INS = trim($_POST["ins"]);
	$_S_FACE = trim($_POST["face"]);
	$_S_TWIT = trim($_POST["twit"]);
	/**/
	
	$_REG_FRAME_ID = Temizle(trim($_POST["frame_id"]));
	$_NEW_PASS = TemizleMail(trim($_POST["new_pass"]));

	if($token != null){
		$tokenDizisi = SifreliTokenCoz($token);
		$_MAIL = $tokenDizisi[0];
		$_PASS = $tokenDizisi[1];

		if($_N_NAME == null) $_N_NAME = 333;
		if($_F_NAME == null) $_F_NAME = 333;
		if($_L_NAME == null) $_L_NAME = 333;
		if($_CITY == null) $_CITY = 333; elseif($_CITY == "X") $_CITY = null;
		if($_BIO == null) $_BIO = 333; elseif($_BIO == "X") $_BIO = null;
		/**/
		if($_S_INS == null) $_S_INS = 333; elseif($_S_INS == "X") $_S_INS = null;
		if($_S_FACE == null) $_S_FACE = 333; elseif($_S_FACE == "X") $_S_FACE = null;
		if($_S_TWIT == null) $_S_TWIT = 333; elseif($_S_TWIT == "X") $_S_TWIT = null;
		/**/
		
		if($_PIC_NO == null){
			$_PIC_NO = ProfilResmi(); 
		}
		elseif($_PIC_NO == "X"){
			$_PIC_NO = 0;
		}
		else{								
			$_PIC_NO = 333;
		}
		if($_B_DATE == null) $_B_DATE = 333; elseif($_B_DATE == "X") $_B_DATE = '0000-00-00';
		if($_REG_FRAME_ID == null) $_REG_FRAME_ID = 333; elseif($_REG_FRAME_ID == "X") $_REG_FRAME_ID = 0; 
		if($_NEW_PASS == null || strlen($_NEW_PASS) < 8){
			$_NEW_PASS = 333;
		}
		else{
			$_NEW_PASS = Sifrele($_NEW_PASS);
			$SifreliToken = SifreliTokenUret($_MAIL,$_NEW_PASS);			
		}


			$_kod = 200;
            $pcn;
		    $sql = "CALL sp_PROFILE_EDIT('$_MAIL','$_PASS','$_N_NAME','$_F_NAME','$_L_NAME','$_CITY','$_BIO','$_PIC_NO','$_B_DATE','$_REG_FRAME_ID','$_S_INS','$_S_FACE','$_S_TWIT','$_NEW_PASS')";
			$output = mysqli_query($baglanti,$sql);
			if(mysqli_num_rows($output)> 0){
				while($satir = mysqli_fetch_assoc($output)){
					extract($satir);
					$hata = $satir['@p_ERROR'];
					$coin = $satir['COIN'];
					$pcn = $satir['PIC_NO'];
					
				}
			}
			

			if($coin != null){
				$jsonDizi['COIN'] = $coin;
				$jsonDizi['hata'] = 0;
			}
			elseif($_PIC_NO != 333 && $_PIC_NO != 0 && $_PIC_NO != 1 && $_PIC_NO != 2 && $_PIC_NO != 3){
				$jsonDizi['IMAGE'] = $DOMAIN."profil/".$_PIC_NO.".jpg";
				$jsonDizi['hata'] = 0;
			}
			elseif($_PIC_NO == 0 && $hata == null){
			    
			    //unlink('profil/'.$pcn.'.jpg');
			    if($pcn != "0"){
			        unlink('profil/'.$pcn.'.jpg');
			    }
				$jsonDizi['IMAGE'] = $DOMAIN."profil/0.png";
				$jsonDizi['hata'] = 0;
			}
			elseif($_NEW_PASS != null && $_NEW_PASS != 333 && $hata == null){
				$jsonDizi['TOKEN'] = $SifreliToken;
				$jsonDizi['hata'] = 0;
			}

			if($hata == 1){
				$jsonDizi['hata'] = 1; //Mail hatası
			}
			elseif($hata == 2){
				$jsonDizi['hata'] = 2; //Şifre hatası
			}
			elseif($hata == 3){
				$jsonDizi['hata'] = 3; //Ban Hatası.
			}
			elseif($hata == 4){
				$jsonDizi['hata'] = 4; //Token Hatası.
			}
			elseif($hata == 5){
				$jsonDizi['hata'] = 5; //NickName Hatası aynısından var.
			}
			elseif($hata == 6){
				$jsonDizi['hata'] = 6; //Coin Hatası.
			}
			else{
				$jsonDizi['hata'] = 0;
			}

			BaslikAyarla($_kod);
			echo json_encode($jsonDizi,JSON_UNESCAPED_UNICODE);
	}
	else{
		$_kod = 400;
		BaslikAyarla($_kod);
	}

}



?>