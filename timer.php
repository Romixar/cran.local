<?php $timer = $_GET['timer']; ?>

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
    
    $(document).ready(function(){
        
        setTimeout(myTimerDown, 1000, <?= $timer ?>);
        
        ifvisible.on('statusChanged', function(e){

            //d("result").innerHTML += (e.status+"<br>");
            //d("serfframe").innerHTML += (e.status+"<br>");

            var stopTxt = '<b style="color:red">Нарушен просмотр! Деньги не зачислены!</b>';

            if(e.status == 'hidden'){

                d("serfframe").innerHTML = stopTxt;

                clearTimeout(timer);
            }
        });
        
        
                   

    });

</script>
  
</head>
   
<body>
    <div id="serfframe">

        <p>Здесь будет таймер обратного отсчета <span id="timer"><?= $timer ?></span> сек.</p>
    </div>    
  
</body>
</html>
   

