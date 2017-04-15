<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">


  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!--
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  
  <script   src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
-->

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
</head>
   
<body>
    <div id="serfframe">
       
       <?php $timer = $_GET['timer']; ?>
       
        <p>Здесь будет таймер обратного отсчета <span id="timer"><?= $timer ?></span> сек.</p>
    </div>    

<!--
<script src="/js/jquery.cookie.js"></script>
<script src="/js/my.js"></script>
-->
 
 <script type='text/javascript'>

		count=<?= $timer ?>;
		setTimeout('countdown()', 1000);
			function countdown(){
                
			    count--;

				if(count>=0){
					document.getElementById('timer').innerHTML=count;
					timer=setTimeout('countdown()',1000);
				}else{
                    alert('таймер закончился');
					//location.href='frame_footer.php?splin=c81e728d9d4c2f636f067f89cc14862c';
				}
			}

		</script>
  
  
</body>
</html>
   

