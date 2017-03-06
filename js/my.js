(function(){
    
    activeMenu();// определение активного пункта меню
    
    
    var act = 'controller/controller';
    
    $('div.form a.login').click(function(e){
        
        e.preventDefault;
        
        var str = '&login='+$('div.form input#login').val()+'&password='+$('div.form input#password').val();
        var name = 'do_login';
        
        post_query(name, str);

    });
    
    $('div.form a.registration').click(function(e){
        
        e.preventDefault;
        
        var str = '&login='+$('div.form input#login').val()+'&password='+$('div.form input#password').val()+'&wallet='+$('div.form input#wallet').val()+'&ip='+$('div.form input#ip').val()+'&g-recaptcha-response='+grecaptcha.getResponse();
        var name = 'do_regist';
                
        post_query(name, str);

    });
    
    
    
    

    
    
    
    
    function post_query(name, str){

        console.log(str);

        $.ajax({// отправляем её

                url: act,
                type: 'POST',
                data: name + '_f=' + str,
                cache: false,
                success: function(res){

                    //alert(res);
                    console.log(res);
                    if(res){
                        obj = JSON.parse(res);
                        
                        if(obj.redirect) location.href = obj.redirect;
                        if(obj.alert) alert(obj.alert);
                        if(obj.sysmes) viewMessage(obj.sysmes);
                        if(obj.btn) viewButtons();
                    };
                    


                },
            });
    };
    
    
    function viewMessage(mes){
        var sysmes = $('div#sysmes');
        if(sysmes) sysmes.remove();
        $('div.main div.col-md-12 h4').after(mes);    
    }
    
    function viewButtons(){
        var sysmes = $('div#sysmes');
        if(sysmes) $('div.alert').append('<a href="#" onclick="rem()" class="btn btn-sm btn-default" style="margin-left: 25px">Да</a><a href="registration" class="btn btn-sm btn-default">Нет</a>');
    }
    
    function activeMenu(){
        var loc = location.href;
        var items = $('ul.navbar-nav li');// колллекция LI где ссылки меню
        var arr = loc.split('/');// 3 элемент это активная страница

        $.each(items, function(){

            var href = this.children[0].getAttribute('href');// uri ссылки меню

            if(arr[3] !== ''){

                // адрес ссылки включ в себя часть URL
                if(href.indexOf(arr[3], 1) !== -1) $(this).addClass('active');
            }
            if(arr[3] === '' && href === '/') $(this).addClass('active');// главная
         });
    }
    

    

    
    
    
    
    
})();