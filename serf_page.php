<?php $timer = $_GET['timer']; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Просмотр страницы</title>
  
<link rel='icon' type='image/png' href='http://<?= $_SERVER['HTTP_HOST'] ?>/images/ruble.png'>
<link rel='icon' href='/favicon.ico'>

  <link rel="stylesheet" href="/css/style.css">
  
  <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  
  <script src="/js/myscript.js"></script>
  
  <script type="text/javascript">
      
      //if(!flagView) 
    
  </script>
 </head>
 <frameset id="mainframe" rows="100,*" frameborder='0'>
 
     <?php $timer = $_GET['timer']; ?>
     <?php $serf_id = $_GET['serf_id']; ?>
     <?php $price = $_GET['price']; ?>

     <frame id="resultFrame" src="timer.php?timer=<?= $timer ?>&serf_id=<?= $serf_id ?>&price=<?= $price ?>" name="TIMER" noresize noborder>

     <?php $url = (isset($_GET['url'])) ? $_GET['url'] : $_SERVER['HTTP_HOST']; ?>

     <frame src="http://<?= $url ?>" name="CONTENT">
     
 </frameset>
 
</html>