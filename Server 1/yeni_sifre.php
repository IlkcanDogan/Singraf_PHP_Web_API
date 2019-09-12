<!DOCTYPE html>
<html lang="tr">

<head>

   
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Singraf">
  <meta name="author" content="İlkcan Doğan">

  <title>Singraf Yeni Şifre</title>
  <link rel="icon" type="image/png" href="web/img/slogo2.png"/>
  <link href="web/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="web/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="web/vendor/simple-line-icons/css/simple-line-icons.css">
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

  <link rel="stylesheet" href="web/device-mockups/device-mockups.min.css">
  <link href="web/css/new-age.css" rel="stylesheet">

</head>

<body id="page-top">

  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="#page-top">Singraf</a>
      
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          
        </ul>
      </div>
    </div>
  </nav>

<header class="masthead">
    <div class="container h-100">
      <div class="row h-100">
        <div class="col-lg-12 my-auto">
          <div class="header-content mx-auto">
               <form>
                <div class="form-group">                 
                </div>
              </form>

       <img src="web/img/slogo.png" class="rounded mx-auto d-block" alt="singraf" style="width: 90%; margin-top: -30%; margin-bottom: 5%;">
<?php 
    include "fonksiyon.php";
    include "veritabani.php";
    error_reporting(0);
    
    
    $_metot = $_SERVER['REQUEST_METHOD'];
    if($_metot == "GET"){
      $token = Temizle(htmlspecialchars(trim($_GET["TOKEN"])));

      if($token != null){
        $TOKENDizisi = SifreliTokenCoz($token);
        $mail = $TOKENDizisi[0];
        $onayKodu = $TOKENDizisi[1];
        
        $sql = "CALL sp_FORGET_PASS_CODE_GET('$mail')";
        $output = mysqli_query($baglanti,$sql);
        if(mysqli_num_rows($output)> 0){
            while($satir = mysqli_fetch_assoc($output)){
            extract($satir);
            $hata = $satir['@p_ERROR'];

          }
        }

        if($hata == 1){
            header("Location:web/index.html");
        }
        elseif($hata == 2){
          header("Location:web/index.html");
        }
        elseif($hata == $onayKodu){

          echo "<div class='form-group'>";
            echo  "<label>Yeni Şifre</label>";
            echo  "<input type='password' class='form-control' id='Sifre1'>";
            echo '<br>';
            echo  "<label>Yeni Şifre Tekrar</label>";
            echo "<input type='password' class='form-control' id='Sifre2'>";
            echo "<h6 id='durum' style='margin-top:10px;'></h6>";
             echo "<h6 id='durum2' style='margin-top:10px; color: white;'></h6>";
          echo "</div>";
          echo "<button  type='submit' id='btnKaydet' onclick='Gonder()' class='btn btn-outline btn-xl js-scroll-trigger'>Şifreyi Değiştir</button>"; 


         }
        else{
          header("Location:web/index.html");
        }
          
          
      }
      else{
         header("Location:web/index.html");
      }


    }
 

?> 
          </div>
        </div>        
       </div>
    </div>
  </header>

  <footer>
    <div class="container">
      <p>Singraf 2019</p>
      <ul class="list-inline">
        <li class="list-inline-item">
          
        </li>
        <li class="list-inline-item">
          
        </li>
        <li class="list-inline-item">
          
        </li>
      </ul>
    </div>
  </footer>

  <script src="web/vendor/jquery/jquery.min.js"></script>
  <script src="web/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="web/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="web/js/new-age.min.js"></script>

  <script>

function parametreGetir(veri) {

   var url = window.location.href;
   veri = veri.replace(/[\[\]]/g, "\\$&");
   var regex = new RegExp("[?&]" + veri + "(=([^&#]*)|&|#|$)");
   sonuc = regex.exec(url);
   if (!sonuc) return null;
   if (!sonuc[2]) return '';
   return decodeURIComponent(sonuc[2].replace(/\+/g, " "));
}

    function Gonder(){
      var sifre1 = document.getElementById('Sifre1').value;
      var sifre2 = document.getElementById('Sifre2').value;
      var token = parametreGetir('TOKEN');

      if(sifre1.length >= 8 && sifre1.length >= 8){

          if(sifre1 === sifre2){
            if(token.length == 160 && token != null){
        		$.ajax({
					type: "POST",
					url: "sifrepost.php",
					data: {'sifre': sifre1, 'token': token},
					dataType: 'json',
					timeout: 3000,

					beforeSend: function(){
						document.getElementById('durum').innerHTML = "";
        				document.getElementById('durum2').innerHTML = "Şifreniz değiştiriliyor...";
					},

					success: function(gelenVeri){
						var res = gelenVeri['durum'];
						
						if(res == "ok"){
							document.getElementById('durum').innerHTML = "";
        					document.getElementById('durum2').innerHTML = "Şifreniz değiştirildi";
        					setTimeout(function(){   
        						window.location.assign("web/index.html");
        					}, 2000); 
						}
						else{
							document.getElementById('durum').innerHTML = "";
        					document.getElementById('durum2').innerHTML = "Şifreniz değiştirilemedi. Lütfen daha sonra tekrar deneyiniz.";
						}
						
					},

					error: function(hata){
						document.getElementById('durum').innerHTML = "";
        				document.getElementById('durum2').innerHTML = "Şifreniz değiştirilemedi. Lütfen daha sonra tekrar deneyiniz.";
					},

					complate: function(){
						
					}
			
				}) 

        		


        	}
        	else{
        		alert('Token Hatası!');
        	}

          }
          else{
             
              document.getElementById('durum').innerHTML = "Şifreniz eşleşmiyor.";
          }

      }
      else{
         document.getElementById('durum').innerHTML = "Şifreniz en az 8 karakter olmalıdır.";

      }

    }




  </script>

</body>
</html>