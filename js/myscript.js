    
    var act = 'controller/controller';
    
    var timer; /// ID таймера
    var timer2; /// ID таймера второго

    var flagView = true; // флаг просмотра (если true то следить за просмотром)

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
            
            // остановить отслеживание статусов вкладки
            flagView = false;
            
            replFrameContent();
            
            // запускаю второй счетчик
            setTimeout(timerCount, 1000, count = 5);//  отсчёт таймера и скрытие фрейма
        }
         
    }

    function actualityView(e){
            
        var statuses = '';

        var stopTxt = '<b style="color:red">Нарушен просмотр! Деньги не зачислены!</b>';

        if(e.status == 'hidden') statuses += e.status;
                
        if(statuses == 'hidden'){// сработает только после первого hidden
                    
            d("serfframe").innerHTML = stopTxt;
                    
            failedView();
                    
            clearTimeout(timer);
        }
            
            
    }

    function failedView(){
        
        if(serf_id != undefined){
            
            var str = serf_id;
        
            post_query('addserfview', '&serf_id='+str+'&view=0');
            
        }
        
        
    }
      
     
     function replFrameContent(){

         
         d('serfframe').innerHTML = '';
         
         price = Number(price).toFixed(4);
         
         var content = '<p>Получите за просмотр:</p><button id="getserfpay">'+price+' руб.</button>';
         
         d('serfframe').innerHTML = content;
         
     }

    $(document).on('click', 'button#getserfpay', function(e){// запрос серфинг платы за просмотр
        
        e.preventDefault();
        
        if(serf_id != undefined){
            
            var str = serf_id;
        
            post_query('addserfview', '&serf_id='+str+'&view=1');
            
            
            
        }
        
        
        
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
                    
                    obj = JSON.parse(res);
                    
                    if(obj.alert) alert(obj.alert);
                    
                },
                error: function(xhr, ajaxOptions, thrownError){
                    
		            console.log(xhr.status); // пoкaжeм oтвeт сeрвeрa
		            console.log(thrownError); // и тeкст oшибки
		        },
		        complete: function(data){ // сoбытиe пoслe любoгo исхoдa
		        },
            });
    };


    
    





	function timerCount(count){
                
	   count--;

	   if(count >= 0){
           
           console.log(count);
           
           timer2 = setTimeout(timerCount,1000,count);
       }else{
            
           clearTimeout(timer2);
           removeFrame();
           //failedView();

       }
	}
      
      
    function removeFrame(){
          
        //var frtimer = document.getElementsByName('TIMER');
        
        var frtimer = $('#resultFrame');
        
        frtimer.remove();
                    
        //var prntEl = frtimer[0].parentNode;
        //var prntEl = frtimer.parent;
        var prntEl = $('#mainframe');
        
        console.log(frtimer);
                    
        //prntEl.remove(frtimer);
                    
        //prntEl.setAttribute('rows','*');
        prntEl.attr('rows','*');
          
    }

      
    $(window).on("beforeunload", function(event) {// запрет закрытия

        if($(event.target.activeElement).is("a")) return;

        return "Деньги за просмотр будут утеряны!";

    });
      
    $(window).keydown(function(event){// запрет перезагрузки по F5 or Ctrl+R

        if(event.keyCode == 116 || (event.ctrlKey && event.keyCode == 82)) refresh = true;
    });
    
    
    
    
