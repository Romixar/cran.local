(function(){
    
    var submit;// разрешение на отправку формы
    
    activeMenu();// определение активного пункта меню
    inpFocus();// проверка фокуса полей

    var act = 'controller/controller';
    var patLogPas = /^[a-z0-9]+$/i; // проверка логина/пароля
    var patEmail = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i;
    
    
    
    
    
    
    
    

        
    $("div.registration input#login").on("change", function(){// набран текст и убран фокус
        
        $('a.login').removeClass('disabled');
        
        $('a.login').text('Зарегистрироваться');
        
        submit = true;
        
        if($(this).val().indexOf(' ') !== -1) validMessage($(this), 'ERR_NBS');
        if(!patLogPas.test($(this).val())) validMessage($(this), 'ERR_CHR');
        if(($(this).val()).length > 100) validMessage($(this), 'ERR_LEN');
                
        if(submit){
            
            viewIcon2($(this), 'refresh gly-spin');// запуск крутилки
                      
                      //'<i class="glyphicon glyphicon-refresh gly-spin"></i>'
            
            var str = '&login='+$(this).val();
            var name = 'reg_login';
        
            post_query(name, str);
        }
    });
    $("div.registration input#password").on("change", function(){// набран текст и убран фокус
        
        submit = true;

        if(!patLogPas.test($(this).val())) validMessage($(this), 'ERR_CHR');
        
        if($(this).val().indexOf(' ') !== -1) validMessage($(this), 'ERR_NBS');
        
        if($(this).val().length > 15) validMessage($(this), 'ERR_LEN');
        
        if(submit) viewIcon2($(this), 'ok');
        else viewIcon2($(this), 'remove', 'onclick="rem4()"');
                
        
    });
    $("div.registration input#wallet").on("change", function(){// набран текст и убран фокус
        
        submit = true;

        // перв символ или пусто после первого или только цифры с перв символа
        if(($(this).val())[0] !== 'P' || ($(this).val()).substring(1) === '' || isNaN(+($(this).val()).substring(1))) validMessage($(this), 'ERR_WAL');
        
        if($(this).val().indexOf(' ') !== -1) validMessage($(this), 'ERR_NBS');
        
        if($(this).val().length > 20) validMessage($(this), 'ERR_LEN');
        
        if(submit) viewIcon2($(this), 'ok');
        else viewIcon2($(this), 'remove', 'onclick="rem3()"');
        
    });
    
    $('div.login input#login').on('change', function(){
        
        $('a.login').removeClass('disabled');
        
        $('a.login').text('Войти');

    });
    $('div.profile input#email').on('focus', function(){ // снять блокироку кнопки
        
        $('a.profile').removeClass('disabled');
        
        $('a.profile').text('Сохранить настройки');

    });


                


    
    
    
    
    
    
    
    
    $('div.form a.mes').click(function(e){
        
        e.preventDefault;
        submit = true;

        var nm = $('div.form input#name');
        var em = $('div.form input#email');
        var mes = $('div.form textarea#message');
        //var pattern = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i;
        
        if(nm.val() === '') validMessage(nm, 'ERR_EMP');
        if(nm.val().length > 100) validMessage(nm, 'ERR_LEN');
        
        if(em.val() === '') validMessage(em, 'ERR_EMP');
        else if(!patEmail.test(em.val())) validMessage(em, 'ERR_EML');
        
        if(mes.val() === '') validMessage(mes, 'ERR_EMP');
        if(mes.val().length > 3000) validMessage(mes, 'ERR_LEN');

        if(submit){
            var str = '&name='+$.trim(nm.val())+'&email='+$.trim(em.val())+'&message='+$.trim(mes.val());
            var name = 'do_message';

            post_query(name, str);
            
            viewMessage(getTplMes('Спасибо!<br/>Ваше обращение отправлено администратору.','success'));
            
            $('div.form').remove();// удаляю форму
        }

    });
    
    
    
    $('div.form a.registration').click(function(e){
        
        e.preventDefault;
        validRegAndSubmit();
    });
    $("div.registration").keyup(function(e){// запустить валидацию и отправку
        
        if(e.keyCode == 13) validRegAndSubmit();
    });
    $('div.login a.login').click(function(e){
        
        e.preventDefault;
        validLogAndSubmit();
    });
    $('div.login').keyup(function(e){
        
        if(e.keyCode == 13) validLogAndSubmit();
    });
    $('div.profile a.profile').click(function(e){
        
        e.preventDefault;
        validEmailAndSubmit();
    });
//    $('div.profile').keyup(function(e){  ПОКА НЕ ДЕЛАТЬ
//        
//        if(e.keyCode == 13) validEmailAndSubmit();
//    });
    
    
    function validEmailAndSubmit(){
        
        formData = new FormData($('#my_form').get(0)); // в экземпляр объекта передаем форму
        
        submit = true;
        
        var em = $('div.profile input#email');
        
        if(em.val() !== '') if(!patEmail.test(em.val())) validMessage(em, 'ERR_EML');
        
        
        if(submit){
            viewIcon3($('a.profile'), 'refresh gly-spin');// запуск крутилки в кнопке
            send_json(formData);
        }
        
        
        
        
    };
    
    
    
//    function validEmailAndSubmit(){
//        
//        submit = true;
//        
//        var em = $('div.profile input#email');
//        
//        if(em.val() === '') validMessage(em, 'ERR_EMP');
//        else if(!patEmail.test(em.val())) validMessage(em, 'ERR_EML');
//        
//        if(submit){
//            
//            viewIcon3($('a.profile'), 'refresh gly-spin');// запуск крутилки в кнопке
//
//            var str = '&email='+em.val();
//            var name = 'do_profile';
//
//            post_query(name, str);
//        }
//        
//        
//    }
    
    
    function validLogAndSubmit(){
        
        submit = true;// запрет второй отправки (по ENTER например)

        var lg = $('div.form input#login');
        var pwd = $('div.form input#password');
        
        if(lg.val() === '') validMessage(lg, 'ERR_EMP');
        if(lg.val().indexOf(' ') !== -1) validMessage(lg, 'ERR_NBS');
        if((lg.val()).length > 100) validMessage(lg, 'ERR_LEN');
        
        if(pwd.val() === '') validMessage(pwd, 'ERR_EMP');
        if(pwd.val().indexOf(' ') !== -1) validMessage(pwd, 'ERR_NBS');
        if(pwd.val().length > 100) validMessage(pwd, 'ERR_LEN');
        
        if(submit){
            
            viewIcon3($('div.login a.login'), 'refresh gly-spin');// запуск крутилки в кнопке

            var str = '&login='+lg.val()+'&password='+pwd.val();
            var name = 'do_login';

            post_query(name, str);
        }
        
        
    }
    

    function validRegAndSubmit(){
        
        submit = true;
        
        var lg = $('div.form input#login');
        var pwd = $('div.form input#password');
        var wt = $('div.form input#wallet');
        
        if(lg.val() === '') validMessage(lg, 'ERR_EMP');
        if(lg.val().indexOf(' ') !== -1) validMessage(lg, 'ERR_NBS');
        if((lg.val()).length > 100) validMessage(lg, 'ERR_LEN');
        
        if(pwd.val() === '') validMessage(pwd, 'ERR_EMP');
        if(pwd.val().indexOf(' ') !== -1) validMessage(pwd, 'ERR_NBS');
        if((pwd.val()).length > 100) validMessage(pwd, 'ERR_LEN');
        
        if(wt.val() === '') validMessage(wt, 'ERR_EMP');
        if(wt.val().indexOf(' ') !== -1) validMessage(wt, 'ERR_NBS');
        if((wt.val()).length > 20) validMessage(wt, 'ERR_LEN');
        
        // перв символ или пусто после первого или только цифры с перв символа
        if((wt.val())[0] !== 'P' || (wt.val()).substring(1) === '' || isNaN(+(wt.val()).substring(1))) validMessage(wt, 'ERR_WAL');
        
        // проверка капчи перед отправкой
        var response = grecaptcha.getResponse();

        if(response == ""){
            
            var rc = $('div.g-recaptcha');
            rc.prev().find('span').text('').append("Подтвердите, что Вы не являетесь роботом!");
            submit = false;
        }
        
        if(submit){
            
            viewIcon3($('div.registration a.registration'), 'refresh gly-spin');// запуск крутилки
            
            var str = '&login='+lg.val()+'&password='+pwd.val()+'&wallet='+wt.val()+'&ip='+$('div.form input#ip').val()+'&g-recaptcha-response='+response;
            var name = 'do_regist';

            post_query(name, str);
        }
        
        
    }
    
    
    
    function post_query(name, str){

        console.log(str);

        $.ajax({// отправляем её

                url: act,
                type: 'POST',
                data: name + '_f=' + str,
                cache: false,
//                contentType: false,
//                processData: false,
                success: function(res){

                    console.log(res);
                    if(res){
                        obj = JSON.parse(res);
                        
                        if(obj.redirect) location.href = obj.redirect;
                        if(obj.alert) alert(obj.alert);
                        if(obj.sysmes) viewMessage(obj.sysmes, obj.submit);
                        if(obj.btn) viewButtons();
                        if(obj.icon) viewIcon(obj.icon, obj.click);
                        if(obj.err) validMessage($('div.registration input#login'), obj.err);
                    };
                },
            });
    };
    
    function send_json(formData){

        $.ajax({
      url: act,
      type: 'POST',
      contentType: false, // важно - убираем форматирование данных по умолчанию
      processData: false, // важно - убираем преобразование строк по умолчанию
      data: formData,
      dataType: 'json',
      success: function(json){
          
          //console.log(json);
          
          //obj = JSON.parse(json);
          
          console.log(json);
          
          if(json.sysmes) viewMessage(json.sysmes, json.submit);
          
          
//        if(json){
//          $('#my_form').replaceWith(json);
//        }
      }
    });
        
        
    }
    
    
    
    function validMessage(el, k){
        var err = {
            'ERR_EMP': 'Заполните пожалуйста это поле!',
            'ERR_LEN': 'Превышена длина текстового поля!',
            'ERR_WAL': 'Формат кошелька указан неверно!',
            'ERR_NBS': 'Пробелы недопустимы в этом поле!',
            'ERR_EML': 'E-mail введён некорректно!',
            'ERR_DBL': 'Ваш логин уже используется на сайте!',
            'ERR_CHR': 'Только латинский алфавит и цифры!',
            
        };            
        el.css('border','1px solid red').prev().text('').append(err[k]);
        submit = false;
        
        
    }
    
    function viewIcon(type, click=''){
        
        var span = $('div.registration input#login').next();
        
        span.text('').append('<span class="glyphicon" '+click+'><i class="glyphicon glyphicon-' +type+ '"></i></span>');
    }
    
    function viewIcon2(el, type, click=''){
        var span = el.next();

        span.text('').append('<span class="glyphicon" '+click+'><i class="glyphicon glyphicon-' +type+ '"></i></span>');
    }
    
    function viewIcon3(el, type){
        
        el.addClass('disabled');
        el.text('').append('<i class="glyphicon glyphicon-' +type+ '"></i>');
    }
    
    
    function viewMessage(mes, sub=false){
        
        console.log(mes);
        
        //var sysmes = $('div#sysmes');
        var al = $('div.alert');
        //if(sysmes) sysmes.remove();
        if(al) al.remove();
        $('div.main div.col-md-12 h4').after(mes);
        
        console.log(sub);
        
        if(sub !== false) $('div.form a').text(sub);
            
    }
    
    function getTplMes(mes, type){
        return '<div class="alert alert-' +type+ ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +mes+ '</div>';
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
    
    function inpFocus(){
        
        var inp = $('input');
        var txt = $('textarea');
        
        inp.focusin(function(){
            $(this).css('border','none');
            $(this).prev().text('');// очистка текста перед полем
            $(this).next().text('');// очистка глификонки
        });
        txt.focusin(function(){
            $(this).css('border','none');
            $(this).prev().text('');
        });
    }



    
    
    
    
    
})();