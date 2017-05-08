<?php $timer = $_GET['timer']; ?>
<?php $serf_id = $_GET['serf_id']; ?>
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
         
         $(document).ready(function(){
             
             var msek = (<?= $timer ?> * 1000) + 55000;// ч/з 35 сек. просмотр будет провален
             
             serf_id = <?= $serf_id ?>;
             
             frtimer = $('#resultFrame');
             
             prntEl = $('#mainframe');
             
             setTimeout(removeFrame, msek, frtimer, prntEl);//  отсчёт и скрытие фрейма
             
         });

  </script>

 </head>
 <frameset id="mainframe" rows="100,*" frameborder='0'>
 
     <?php $price = $_GET['price']; ?>
     <?php $rand = $_GET['rand']; ?>
     <?php $title = $_GET['title']; ?>
     <?php $url = (isset($_GET['url'])) ? $_GET['url'] : $_SERVER['HTTP_HOST']; ?>

     <frame id="resultFrame" src="timer.php?timer=<?= $timer ?>&serf_id=<?= $serf_id ?>&price=<?= $price ?>&rand=<?= $rand ?>&url=<?= $url ?>&title=<?= $title ?>" name="TIMER" noresize noborder>

     

     <frame src="<?= $url ?>" name="CONTENT">
     
 </frameset>
 

   
 
</html>