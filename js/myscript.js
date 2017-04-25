    
    var act = 'controller/controller';
    
    var timer; /// ID таймера

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
            
            flagView = false; // остановить отслеживание статусов вкладки
            
            replFrameContent();
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
      


    function replFrameContent(mes=''){
        
        var content = '';
        
        d('serfframe').innerHTML = '';
        
        if(mes) content = '<b style="color:blue">'+mes+'</b>';
        else{
            
            price = (price != undefined) ? Number(price).toFixed(4) : 0;
            
            if(rand != undefined){
                
                var btns = getHtmlButtons(rand, price);
            
                //content = '<p>Получите за просмотр:</p><button id="getserfpay">'+price+' руб.</button>';
                content = '<p>Получите за просмотр:</p>'+btns;
                
            }
        }
        
        d('serfframe').innerHTML = content;
        
    }

    function getHtmlButtons(rand, price){
        
        var str = '<p id="btns">';
        var p;
        
        for(var i=1; i<5; i++){
            
            p = (i == rand) ? price : 0;
            
            str += '<button id="'+i+'">'+p+' руб.</button>';
        }
        return str += '</p>';
    }


    $(document).on('click', '#btns button', function(e){// запрос серфинг платы за просмотр
        
        e.preventDefault();
        
//        console.log(e.target.id);
//        
//        console.log(rand);
        
        if(e.target.id == rand && serf_id != undefined){
            
            var str = serf_id;
        
            post_query('addserfview', '&serf_id='+str+'&view=1');
            
        }else{
            
            post_query('addserfview', '&serf_id='+serf_id+'&view=0');
            
        }
        
//        if(serf_id != undefined){
//            
//            var str = serf_id;
//        
//            post_query('addserfview', '&serf_id='+str+'&view=1');
//            
//            
//            
//        }
        
        
        
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
                    
                    if(obj.replFrCont) replFrameContent(obj.replFrCont);
                    
                },
                error: function(xhr, ajaxOptions, thrownError){
                    
		            console.log(xhr.status); // пoкaжeм oтвeт сeрвeрa
		            console.log(thrownError); // и тeкст oшибки
		        },
		        complete: function(data){ // сoбытиe пoслe любoгo исхoдa
		        },
            });
    };
      
      
    function removeFrame(frtimer, prntEl){// скрытие фрейма после счетчика
        
        //failedView(); // засчитываю провальный просмотр, если еще не было
        
        
        frtimer.remove();
        
        prntEl.attr('rows','*');
          
    }

      
    $(window).on("beforeunload", function(event) {// запрет закрытия

        if($(event.target.activeElement).is("a")) return;

        return "Деньги за просмотр будут утеряны!";

    });
      
    $(window).keydown(function(event){// запрет перезагрузки по F5 or Ctrl+R

        if(event.keyCode == 116 || (event.ctrlKey && event.keyCode == 82)) refresh = true;
    });
    
    
    
    
