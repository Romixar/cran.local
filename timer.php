<?php $timer = $_GET['timer']; ?>
<?php $serf_id = $_GET['serf_id']; ?>
<?php $price = $_GET['price']; ?>
<?php $rand = $_GET['rand']; ?>
<?php $url = $_GET['url']; ?>
<?php $title = $_GET['title']; ?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <link rel="stylesheet" href="/css/style.css">
  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  
  <script   src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  
  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <script src="/js/ifvisible.js"></script>
  <script src="/js/myscript.js"></script>
  
<script type="text/javascript">
    
    var serf_id = <?= $serf_id ?>;
    var price = <?= $price ?>;
    var rand = <?= $rand ?>;
    
    $(document).ready(function(){
        
        setTimeout(myTimerDown, 1000, <?= $timer ?>);
        
        ifvisible.on('statusChanged', function(e){
            
            if(flagView) actualityView(e);

        });
        
    });

</script>
  
</head>
   
<body>
    <div id="serfframe">

        <p><span id="sp1">Ждите... </span><span id="timer"><?= $timer ?></span><span id="sp3"> сек.</span></p>
        <div id="dopinfo">
           <div id="inserf">
              
              <p>Описание: <span id="serfdesc"><?= $title ?></span><br/>

              URL: <span id="serfurl"><a href="http://<?= $url ?>">http://<?= $url ?></a></span></p>
               
           </div>
            
            <div id="banner">
                
                Здесь будет ваша реклама!<br/>
                Место 500х70px сдается в аренду<br/>
                Серфссылка - описание 100 символов, URL 30 символов, тело не > 30
                
            </div>
        </div>
    </div>    
  
</body>
</html>
   

