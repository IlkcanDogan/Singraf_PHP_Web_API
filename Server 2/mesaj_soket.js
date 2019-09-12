let app = require('express')();
let http = require('http').Server(app);
let io = require('socket.io')(http);
const request = require('request'); // HTTP istekleri

let soketler = {};

io.on('connection', (socket) => {
  let kullanici_adi;
  let id;
  let resim;

  socket.on('giris-yap', (TOKEN) => {

    //#region POST
    var veriJSON = {
      token: TOKEN
    };

    var header = {
      API_KEY: '15386b116a2a9e75fbd890841ed50aca'
    }

    request({
      url: "https://ilkcandogan.com/mesajtoken.php",
      method: "POST",
      json: true,
      headers: header,
      body: veriJSON
    }, function (error, response, body) {
      if (!error) {
        kullanici_adi = response.body.N_NAME;
        id = response.body.ID;
        resim = response.body.IMAGE;

        soketler[kullanici_adi] = socket.id;
        console.log(JSON.stringify(soketler));
        io.to(soketler[kullanici_adi]).emit('baglanti-callback',{
          durum: true
        });
        if (kullanici_adi != null) {

          //#region BM
          var tamponMesajlar = [];
          var veriJSON = {
            nickname: kullanici_adi
          };

          var header = {
            API_KEY: '15386b116a2a9e75fbd890841ed50aca'
          }

          request({
            url: "https://ilkcandogan.com/wmget.php",
            method: "POST",
            json: true,
            headers: header,
            body: veriJSON
          }, function (error, response, body) {
            if (!error) {
              tamponMesajlar.push(response.body.mesajlar);
              var abc = [];

              abc = tamponMesajlar[0];
              let uzunluk = abc.length;

              for (let index = 0; index < uzunluk; index++) {
                
                let s_MESSAGE = abc[index].MESSAGE;
                let s_DATE = abc[index].DATE;
                let s_SENDER = abc[index].SENDER;
                let s_IMAGE = abc[index].IMAGE;
                let s_SENDER_ID = abc[index].SENDER_ID;

                io.to(soketler[kullanici_adi]).emit('mesaj', {
                  MESSAGE: s_MESSAGE,
                  kimden: s_SENDER,
                  kimden_id: s_SENDER_ID,
                  profil: s_IMAGE,
                  tarih: s_DATE,
                  MY_MESSAGE: ""

                });
               
              }


            }
            else {
              console.log("istek hatası.");
            }

          });
          //#endregion

        }
        else {
          console.log("token hatası");
        }
      }
      else {
        console.log("istek hatasııı. " + socket.id);
      }

    });
    //#endregion

  });


  socket.on('mesaj-gonder', (veri) => {
    let cevap = {
      mesaj: veri.mesaj,
      kimden: kullanici_adi,
      kimden_id: id,
      profil: resim

    };

    if (typeof soketler[veri.alici] !== 'undefined') {
      io.to(soketler[veri.alici]).emit('mesaj', {
        MESSAGE: cevap.mesaj,
        kimden: cevap.kimden,
        kimden_id: cevap.kimden_id,
        profil: cevap.profil,
        tarih: new Date(),
        MY_MESSAGE: ""
      });

        console.log('gönderildi.');
          io.to(soketler[kullanici_adi]).emit('mesaj-callback',{
            durum: true
        });
        
        io.to(soketler[kullanici_adi]).emit('re-send',{
          durum: true
        });
        //console.log("bağlı olduğundaki veri: "+JSON.stringify(veri));
    }
    else {
      //#region BekleyenMesajlar
      var gelenMesajlar = { mesaj: cevap.mesaj, kimden: cevap.kimden, alici: veri.alici };

      var header = {
        API_KEY: '15386b116a2a9e75fbd890841ed50aca'
      }

      request({
        url: "https://ilkcandogan.com/wm.php",
        method: "POST",
        json: true,
        headers: header,
        body: gelenMesajlar
      }, function (error, response, body) {
        if (!error) {
          console.log("gönderildi");
          io.to(soketler[kullanici_adi]).emit('re-send',{
            durum: true
          });
          //console.log("bağlı olmadığında veri: "+JSON.stringify(veri));
        }
        else {
          //console.log("Bekleyen mesajlar istek hatası.");
        }

      });
      //#endregion

    }

    
   //#endregion
   

  }); //////

  socket.on('mesaj-dizi', (dizi) => {
    let array = [];
    array = dizi;
  });
  
  socket.on('disconnect', () => {
    delete soketler[kullanici_adi]; // kullanıcı çıkış yaptığında socket idsini kaldır
    //console.log("bağlantısı kopan kullanıcı: " + kullanici_adi);

  });

});

var port = 3001;

http.listen(port, function () {
  //console.log('http://localhost:' + port + " adresi dinleniyor...");
});