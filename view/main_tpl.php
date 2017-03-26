<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title><?= $title ?></title>
  <meta name="description" content="<?= $meta_desc ?>">
  <meta name="keywords" content="<?= $meta_key ?>">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  
  <script   src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <script type="text/javascript">
      function rem(){
          $('div.alert').remove();
          $('div.main a.col-md-3').remove();
          $('input#ip').val($('input#ip').val()+'_0');
          return false;
      };
      function rem2(){
          
          $('input#login').val('');
          $('input#login').next().html('');// очистить весь HTML внутри span.icon
      };
      function rem3(){
          
          $('input#wallet').val('');
          $('input#wallet').next().html('');// очистить весь HTML внутри span.icon
      };
      function rem4(){
          
          $('input#password').val('');
          $('input#password').next().html('');// очистить весь HTML внутри span.icon
      };
      


  </script>
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>

<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Cran.local</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="/"><i class="glyphicon glyphicon-home"></i></a></li>
              <li><a href="/rules">Правила</a></li>
              <li><a href="/news">Новости</a></li>
              <li><a href="/statistic">Статистика</a></li>
              <li><a href="/faq">FAQ</a></li>
              <li><a href="/works">Задания</a></li>
              <li><a href="/reklams">Рекламодателям</a></li>
              <li><a href="/contacts">Контакты</a></li>
              <?= $prof ?>

            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<?php// debug($_SERVER); ?>

<div class="container">
    <div class="row">
        
        <div class="col-md-2 col-lg-2 sidebar">
            
            <?= $left ?>
            
        </div>
        <div class="col-md-8 col-lg-8">
           
           <div class="main">
              <div class="col-md-12 col-lg-12">
                   <?= $sysmes ?>

                   <?= $content ?>
                   
              </div>
           </div>
           
            
        </div>
        <div class="col-md-2 col-lg-2 sidebar">
           
           <?= $right ?>
            
        </div>
        
        
    </div>
    
    <div class="row">
       <div class="col-md-12 footer">
           footer
       </div>
        
    </div>
</div>
 
  <script src="/js/jquery.cookie.js"></script>
  <script src="/js/my.js"></script>
  
  
</body>
</html>