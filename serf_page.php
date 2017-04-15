<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Просмотр страницы</title>
  
<link rel='icon' type='image/png' href='http://profitruble.com/img/inter_profit.png'>
<link rel='icon' href='/favicon.ico'>

  <link rel="stylesheet" href="/css/style.css">
 </head>
 <frameset rows="100,*" frameborder='0'>
 
  <frame src="timer.html" name="TIMER" noresize noborder>
  
  <?php $url = (isset($_GET['url'])) ? $_GET['url'] : $_SERVER['HTTP_HOST']; ?>
  
  <frame src="http://<?= $url ?>" name="CONTENT">
 </frameset>
</html>