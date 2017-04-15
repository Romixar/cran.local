<?php $timer = $_GET['timer']; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Просмотр страницы</title>
  
<link rel='icon' type='image/png' href='http://<?= $_SERVER['HTTP_HOST'] ?>/images/ruble.png'>
<link rel='icon' href='/favicon.ico'>

  <link rel="stylesheet" href="/css/style.css">
  
  <script   src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  
  <script type="text/javascript">
      
      function test(){
          
          console.log('провыерка');
          
      }
      
          
          count=<?= $timer ?> + 7;
		setTimeout('timerCount()', 1000);
			function timerCount(){
                
			    count--;

				if(count>=0){
					timer=setTimeout('timerCount()',1000);
				}else{
                    
                    var frtimer = document.getElementsByName('TIMER');
                    
                    var prntEl = frtimer[0].parentNode;
                    
                    prntEl.removeChild(frtimer[0]);
                    
                    prntEl.setAttribute('rows','*');

				}
			}
          
          
      
//      window.onblur = function(){
//   console.log("Ушли мы с этой вкладки. Либо вообще сернули все");
//}
      
//      window.onblur = function(){
//          
//          console.log('serf_page.php неактивен');
//          
//          document.title='документ неактивен';
          
//          var div = document.createElement('div');
//          
//          var txt = document.createTextNode('Незасчитано!');
//          
//          div = div.appendChild(txt);
//          
//          var fr = document.getElementsByName('TIMER');
//          
//          var myDiv = fr.getElementById('serfframe');
//          
//          myDiv.appendChild(div);
         
        //document.title='документ неактивен';
    //}
//    window.onfocus = function(){
//        document.title='документ снова активен';
//    }
      

      

      
//      window.onbeforeunload = function(){
//         
//         alert('Вы уверены?');
//         
////         document.getElementById('serfframe').innerHTML='';
////                    
////         var content = 'Нарушен просмотр! Деньги не зачислены!';
////
////         document.getElementById('serfframe').innerHTML=content;
//                        
//         //return "Что-нибудь сообщить пользователю";
//    }
    
    
    
    
    
//    var interval_id;
//      
//$(window).focus(function() {
//    
//    console.log('фокус');
//    
//    if (!interval_id)
//        interval_id = setInterval(hard_work, 1000);
//});
//
//$(window).blur(function() {
//    
//    console.log('фокус потеря');
//    
//    clearInterval(interval_id);
//    interval_id = 0;
//});
//var isActive;
//
//window.onfocus = function () {
//  isActive = true;
//};
//
//window.onblur = function () {
//  isActive = false;
//};
//
//setInterval(function () {
//  console.log(window.isActive ? 'active' : 'inactive');
//}, 1000);
    
    
          
          

    
</script>
 </head>
 <frameset rows="100,*" frameborder='0'>
 
     <?php $timer = $_GET['timer']; ?>

     <frame src="timer.php?timer=<?= $timer ?>" name="TIMER" noresize noborder>

     <?php $url = (isset($_GET['url'])) ? $_GET['url'] : $_SERVER['HTTP_HOST']; ?>

     <frame src="http://<?= $url ?>" name="CONTENT">
     
 </frameset>

</html>