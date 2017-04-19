    

    
    var timer; /// ID таймера

    function d(el){
        return document.getElementById(el);
    }

//    ifvisible.on('statusChanged', function(e){
//
//        //d("result").innerHTML += (e.status+"<br>");
//        //d("serfframe").innerHTML += (e.status+"<br>");
//        
//        var stopTxt = '<b style="color:red">Нарушен просмотр! Деньги не зачислены!</b>';
//        
//        if(e.status == 'hidden'){
//            
//            d("serfframe").innerHTML = stopTxt;
//            
//            clearTimeout(timer);
//        }
//    });

    

    function myTimerDown(cnt){

        cnt--;

        if(cnt >= 0){

            d('timer').innerHTML = cnt;
            
            timer = setTimeout(myTimerDown,1000, cnt);
            
        }else{
            
            replFrameContent();
        }
         
     }
      
     
     function replFrameContent(){
         
        d('serfframe').innerHTML = '';

        var content = '<p>Получите за просмотр:</p><button>0,0234 руб.</button>';

        d('serfframe').innerHTML = content;
         
     }


    
    





	function timerCount(count){
                
	   count--;

	   if(count >= 0){
           
           timer = setTimeout(timerCount,1000,count);
       }else{
                    
           removeFrame();

       }
	}
      
      
    function removeFrame(){
          
        var frtimer = document.getElementsByName('TIMER');
                    
        var prntEl = frtimer[0].parentNode;
                    
        prntEl.removeChild(frtimer[0]);
                    
        prntEl.setAttribute('rows','*');
          
    }

      
    $(window).on("beforeunload", function(event) {// запрет закрытия

        if($(event.target.activeElement).is("a")) return;

        return "Деньги за просмотр будут утеряны!";

    });
      
    $(window).keydown(function(event){// запрет перезагрузки по F5 or Ctrl+R

        if(event.keyCode == 116 || (event.ctrlKey && event.keyCode == 82)) refresh = true;
    });
    
    
    
    
