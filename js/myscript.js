    
    var act = 'controller/controller';
    
    var timer; /// ID таймера

    function d(el){
        return document.getElementById(el);
    }


    function myTimerDown(cnt){

        cnt--;

        if(cnt >= 0){
            
            if(d('timer')) d('timer').innerHTML = cnt;
            
            timer = setTimeout(myTimerDown,1000, cnt);
            
        }else{
            
            clearTimeout(timer);
            
            replFrameContent();
        }
         
     }
      
     
     function replFrameContent(){

         
         d('serfframe').innerHTML = '';
         
         var content = '<p>Получите за просмотр:</p><button id="getserfpay">0,0234 руб.</button>';
         
         d('serfframe').innerHTML = content;
         
     }

    $(document).on('click', 'button#getserfpay', function(e){// запрос серфинг платы за просмотр
        
        e.preventDefault();
        
        var str = 25;
        
        post_query('addserfview', '&serf_id='+str);
        
    });

    function post_query(name, str){

        console.log(str);

        $.ajax({// отправляем её

                url: act,
                type: 'POST',
                data: name + '_f=' + str,
                cache: false,
                beforeSend: function(data){ // сoбытиe дo oтпрaвки
		        },
                success: function(res){

                    console.log(res);
                    
                },
                error: function(xhr, ajaxOptions, thrownError){
                    
		            console.log(xhr.status); // пoкaжeм oтвeт сeрвeрa
		            console.log(thrownError); // и тeкст oшибки
		        },
		        complete: function(data){ // сoбытиe пoслe любoгo исхoдa
		        },
            });
    };


    
    





//	function timerCount(count){
//                
//	   count--;
//
//	   if(count >= 0){
//           
//           timer2 = setTimeout(timerCount,1000,count);
//       }else{
//                    
//           removeFrame();
//
//       }
//	}
      
      
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
    
    
    
    