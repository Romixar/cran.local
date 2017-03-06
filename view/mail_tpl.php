<!DOCTYPE html>
    <html>
        <head>
            <title><?= $title ?></title>
        </head>
        <body style="margin:0px;">
            <div style="padding:0px;font-size:18px;font-family:Arial sans-serif;font-weight:bold;text-align:left;background:#FCFCDF;">
               <div style="background:#464E78;margin:0;padding:25px;color:#fff;">
                   Тема письма: <?= $title ?><br>
                   Имя отправителя: <?= $name ?><br>
                   Email отправителя: <?= $uemail ?><br>
               </div>
               <div style="padding:30px;">
                   <div style="background:#FFF;border-radius:10px;padding:25px;border:1px solid #EEEFF2">    <?= $text ?>
                   </div>
               </div>
            </div>
        </body>
    </html>