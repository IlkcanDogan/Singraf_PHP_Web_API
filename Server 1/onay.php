<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Singraf">
  <meta name="author" content="İlkcan Doğan">

  <title>Singraf Hesap Onayı</title>
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
        <div class="col-lg-7 my-auto">
          <div class="header-content mx-auto">
<?php 
    include "fonksiyon.php";
    include "veritabani.php";
    error_reporting(0);

    $_metot = $_SERVER['REQUEST_METHOD'];

    if($_metot == "GET"){
      $token = Temizle(htmlspecialchars(trim($_GET["TOKEN"])));

      if($token != null){
        $TOKENDizisi = SifreliTokenCoz($token);
        $kullanici_adi = $TOKENDizisi[0];
        $onayKodu = $TOKENDizisi[1]; 

        $sql = $baglanti->prepare("CALL sp_CODE_GET(?,@out_onayKodu)");
        $sql->bind_param('s',$kullanici_adi);
        $sql->execute();

        $output = $baglanti->query("select @out_onayKodu");
        $hata = $output->FETCH_ASSOC();
        $hata =  $hata['@out_onayKodu'];

        if($hata == 2){
          header("Location:web/index.html");
        }
        elseif($hata == 1){
          header("Location:web/index.html");
        }
        elseif($hata == null || $hata != 0 || $hata != 1){
          if($hata == $onayKodu){

            $sql = $baglanti->prepare("CALL sp_CHECK(?,@out_error)");
            $sql->bind_param('s',$kullanici_adi);
            $sql->execute();

            $output = $baglanti->query("select @out_error");
            $sp_Error = $output->FETCH_ASSOC();
            $sp_Error = $sp_Error['@out_error'];

            if($sp_Error == 0){
              echo '<h1 class="mb-5">Hesabınız Etkinleştirildi!</h1>';
            }
            elseif($sp_Error == 1){
              echo '<h1 class="mb-5">Lütfen Daha Sonra Tekrar Deneyin!</h1>';
            }
          
          }
          else{
           echo '<h1 class="mb-5">Hesap Onayı Yapılamadı!</h1>';
          }
        }

      }
      else{
          header("Location:web/index.html");
      }
      

    }

?>
                       
            <a href="web/index.html" class="btn btn-outline btn-xl js-scroll-trigger">Anasayfa</a>
          </div>
        </div>
        <div class="col-lg-5 my-auto">
          <div class="device-container">
            <div class="device-mockup iphone6_plus portrait white">
              <div class="device">
                <div class="screen">
                  <img src="web/img/demo-screen-1.jpg" class="img-fluid" alt="">
                </div>
                <div class="button">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>


  

 

  

  <footer>
    <div class="container">
      <p>Singapp 2019</p>
    </div>
  </footer>

  <script src="web/vendor/jquery/jquery.min.js"></script>
  <script src="web/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script src="web/vendor/jquery-easing/jquery.easing.min.js"></script>

  <script src="web/js/new-age.min.js"></script>

</body>

</html>
