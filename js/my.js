(function(){
    
    var submit;// разрешение на отправку формы
    
    activeMenu();// определение активного пункта меню
    inpFocus();// проверка фокуса полей

    var act = 'controller/controller';
    var patLogPas = /^[a-z0-9-\._]+$/i; // проверка логина/пароля
                    ///^[a-z0-9-\._]+$/i
    var patEmail = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i;
    
    var elem; // объект кнопка
    var textButton = ''; // текст нажатой кнопки

    var uri = location.href;
    if(uri.split('/')[3] == '' || uri.split('/')[3] == '###'){
        
        if($.cookie('user') !== ''){
            
            //var u = JSON.parse($.cookie('user'));
            
            console.log('существует user');
            console.log($.cookie('user'));
            
            console.log('существует users');
            console.log($.cookie('users'));
            
            checkUserLim();
            
        }
        
        if($.cookie('user_out') !== ''){
            
            console.log('вышедший');
            console.log($.cookie('user_out'));
            
        }
        
    }

    
    
    
    
    
    
    
    

        
    $("div.registration input#login").on("change", function(){// набран текст и убран фокус
        
        $('a#submit').removeClass('disabled');
        
        $('a#submit').text('Зарегистрироваться');
        
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
        
        $('a#submit').removeClass('disabled');
        
        $('a#submit').text('Войти');

    });
    $('div.profile input#email').on('focus', function(){ // снять блокироку кнопки
        
        $('a#submit').removeClass('disabled');
        
        $('a#submit').text('Сохранить настройки');

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
    
    
    
    $('div.login a#submit').click(function(e){// авторизация
        
        e.preventDefault;
        
        var lg = $('div.form input#login');
        var pwd = $('div.form input#password');
        elem = $(this);
        
        if(validLogAndPass(lg, pwd)) submitLogPass(lg,pwd);
    });
    $('div.login').keyup(function(e){// авторизация
        
        var lg = $('div.form input#login');
        var pwd = $('div.form input#password');
        elem = $('div.login a#submit');
        
        if(e.keyCode == 13) if(validLogAndPass(lg, pwd)) submitLogPass(lg,pwd);
    });
    
    
    
    $('div.profile a#submit').click(function(e){
        
        e.preventDefault;
        
        elem = $(this);
        
        if(validEmailAndFile()) submitJson($('#my_form'));// отправить все из my_form
        
    });

    $('a#get_ref_list').click(function(e){
        
        e.preventDefault;
        
        elem = $(this);
        textButton = $(this).text();
        
        viewIcon3($(this), 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('get_ref_list', '');
        
    });
    
    $('a#get_bonus').click(function(e){
        
        e.preventDefault;
        
        elem = $(this);
        textButton = $(this).text();
        
        viewIcon3($(this), 'refresh gly-spin');// запуск крутилки в кнопке
        
        if($.cookie('user')){
            
            if(checkUserLim()) return;
            
        }
        
        post_query('get_bonus', '');
        
    });
    
    function prepareToBonus(){
        
        $('span#wait_bonus').remove();
        $('a#get_bonus').text('').removeClass('disabled').text('Получить');
        return;
    }
    
    function checkUserLim(){
        var user = JSON.parse($.cookie('user'));// превр в объект
            
        if(user.time_lim){
                
            var now = new Date();
            var ts = Math.ceil(now.getTime() / 1000);// TS в сек.
                
            if(ts < user.time_lim){
                    
                $('div.bonus').before('<span id="wait_bonus">До получения бонуса осталось <span id="bon_day"></span> <span id="bon_hour"></span> <span id="bon_min"></span> <span id="bon_sec"></span> секунд.</span>');
                    
                timerToBonus();// счётчик сколько осталось до получения

                return true;
            }
            return false;
        }
    }
    
    
    
    function validEmailAndFile(){// отправка на странице PROFILE
        
        submit = true;
        
        var em = $('div.profile input#email');
        var fl = $('div.profile input#file');
        
        if(em.val() == '' && fl.val() == '') submit = false;
        
        if(em.val() !== '') if(!patEmail.test(em.val())) validMessage(em, 'ERR_EML');
        
        if(fl.val() !== ''){
            
            var name = fl.val().substr(fl.val().lastIndexOf('\\') + 1);
            
            if(!patLogPas.test(name)) validMessage(fl, 'ERR_NME');
            
            submit = false;
            var ext = Array('jpg','jpeg','png','gif');
            var pos = fl.val().lastIndexOf('.');
            
            var str = fl.val().substr(pos+1);
            
            if(pos !== -1) for(i=0; i<(ext.length + 1); i++) if(str == ext[i]) submit = true;
            
            if(!submit) validMessage(fl, 'ERR_EXT');
        }
        
        if(submit) return true;
        return false;
    };

    function submitJson(frm){

        formData = new FormData(frm.get(0)); // в экземпляр объекта передаем форму
        textButton = elem.text();
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
        send_json(formData);
    }
    
    
    function validLogAndPass(lg,pwd){
        
        submit = true;// запрет второй отправки (по ENTER например)

        if(lg.val() === '') validMessage(lg, 'ERR_EMP');
        if(lg.val().indexOf(' ') !== -1) validMessage(lg, 'ERR_NBS');
        if((lg.val()).length > 100) validMessage(lg, 'ERR_LEN');
        
        if(pwd.val() === '') validMessage(pwd, 'ERR_EMP');
        if(pwd.val().indexOf(' ') !== -1) validMessage(pwd, 'ERR_NBS');
        if(pwd.val().length > 100) validMessage(pwd, 'ERR_LEN');
        
        if(submit) return true;
        return false;
    }
    
    function submitLogPass(lg,pwd){
            
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке

        var str = '&login='+lg.val()+'&password='+pwd.val();
        
        post_query('do_login', str);
    }
    

    function validRegAndSubmit(){
        
        submit = true;
        
        var lg = $('div.form input#login');
        var pwd = $('div.form input#password');
        var wt = $('div.form input#wallet');
        var ip = $('div.form input#ip');
        var ref_id = $('div.form input#ref_id');
        
        
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
            
            var str = '&login='+lg.val()+'&password='+pwd.val()+'&wallet='+wt.val()+'&ip='+ip.val()+'&ref_id='+ref_id.val()+'&g-recaptcha-response='+response;
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
                success: function(res){

                    console.log(res);
                    if(res){
                        obj = JSON.parse(res);
                        
                        if(obj.redirect) location.href = obj.redirect;
                        
                        if(obj.alert) alert(obj.alert);
                        
                        if(obj.sysmes){
                            
                            viewMessage(obj.sysmes);
                            setTextSubmit();
                        }
                        
                        if(obj.btn) viewButtons();
                        
                        if(obj.icon) viewIcon(obj.icon, obj.click);
                        
                        if(obj.err) validMessage($('div.registration input#login'), obj.err);
                        if(obj.dataRefList){
                            
                            getRefList(obj.dataRefList);
                            setTextSubmit();
                        }
                        if(obj.mycookie) saveMyCookie(obj.mycookie);

                            
                        
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

              console.log(json);

              if(json.sysmes){

                  viewMessage(json.sysmes);
                  setTextSubmit();
                  clearAndRepl(json.flname, json.changeEm);
              }


    //        if(json){
    //          $('#my_form').replaceWith(json);
    //        }
          }
    });
        
        
    }
    
    
    
    function validMessage(inp, k){
        var err = {
            'ERR_EMP': 'Заполните пожалуйста это поле!',
            'ERR_LEN': 'Превышена длина текстового поля!',
            'ERR_WAL': 'Формат кошелька указан неверно!',
            'ERR_NBS': 'Пробелы недопустимы в этом поле!',
            'ERR_EML': 'E-mail введён некорректно!',
            'ERR_DBL': 'Ваш логин уже используется на сайте!',
            'ERR_CHR': 'Только латинский алфавит и цифры!',
            'ERR_EXT': 'Допустимые расширения jpg, jpeg, png, gif!',
            'ERR_NME': 'В названии файла должны быть латинские символы и цифры!',
            
        };            
        inp.css('border','1px solid red').prev().text('').append(err[k]);
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
    
    
    function viewMessage(mes){
        
        console.log(mes);
        
        var al = $('div.alert');

        if(al) al.remove();
        
        $('div.main div.col-md-12 h4:first').after(mes);// вывод сист сообщения    
    }
    
    function clearAndRepl(img, change){// подстановка в DOM e-mail и img
        
        var el = $('div.profile input#email');
        var fl = $('div.profile input#file');
        
        if(el.val() != '' && change != false){ // по умолчанию заменять
            $('span#email').text('').append(el.val());
            el.val('');// очистка
        }
        if(fl.val() != '' && img != false){
            $('div.image img').attr('src','/images/'+img);
            fl.val('');// очистка
        }
    }
    
    function setTextSubmit(){
        elem.text('').append(textButton);
    }
    
    function getTplMes(mes, type){
        return '<div class="alert alert-' +type+ ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +mes+ '</div>';
    }
    
    function viewButtons(){
        var sysmes = $('div#sysmes');
        if(sysmes) $('div.alert').append('<a href="#" onclick="rem()" class="btn btn-sm btn-default" style="margin-left: 25px">Да</a><a href="registration" class="btn btn-sm btn-default">Нет</a>');
    }
    
    function getRefList(data){
        
        var str = '';
        for(var i=0; i<(data.length); i++){

            str += '<tr><td>'+(i+1)+'.</td><td>'+data[i].login+'</td><td>'+data[i].balance+'</td><td>'+data[i].date_reg+'</td></tr>';   
        }
        
        var strRes = '<tr class="success"><th colspan="3">Всего:</th><th><span id="cnt">'+i+' чел.</span></th></tr>';
        
        $('table.ref_list thead').append(strRes);
        
        
        $('table.ref_list tbody').text('').append(str);
        
        
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
    
    
    $('a#u_out').click(function(e){// при выходе переименовываю
        
        var u_out = $.cookie('user');
        
        
        $.cookie('user_out', u_out, {
                expires: 5,// продолжит-ть 5 дней
                path: '/',
            });
        
        $.cookie('user','');
        
    });
    
        
        
      

    function saveMyCookie(mycookie){
        
        if(mycookie.time_lim){// если впервые получает бонусы

            if($.cookie('user') !== ''){// дописываю time_lim в JS cookie

                var user = JSON.parse($.cookie('user'));

                user.time_lim = mycookie.time_lim;

                user = JSON.stringify(user);

                $.cookie('user', user, {
                    expires: 5,// продолжит-ть 5 дней
                    path: '/',
                });
            }
            return;

        }
        
        if($.cookie('user_out') !== ''){
            
            var user_out = JSON.parse($.cookie('user_out'));
            
            if(user_out.login == mycookie.login && user_out.ip == mycookie.ip){// вошел тот же
                
                var user_in = $.cookie('user_out');// старую куку пересохраню в новую
                
                console.log(user_in);
        
                $.cookie('user', user_in,{
                        expires: 5,
                        path: '/',
                    });
                $.cookie('user_out','');   
            }else{// значит вошел другой польз-ль, запишу его в объект users
                
                var users = {
                    user_1_out:user_out
                };
                
                // если в истории уже есть тот кто входил раньше
                if(users.user_1_out.login == mycookie.login){
                    
                    mycookie.time_lim = users.user_1_out.time_lim;
                    console.log(mycookie);
                }
                
                
                
                users = JSON.stringify(users);
                $.cookie('users', users,{
                        expires: 5,
                        path: '/',
                    });
                
                
                var user_in = JSON.stringify(mycookie);
                
                $.cookie('user', user_in,{
                        expires: 5,
                        path: '/',
                    });
                $.cookie('user_out','');
                
            }
        }

        


        
    }


    function timerToBonus(){
        
        var user = JSON.parse($.cookie('user'));
        var lim = user.time_lim;// лимит на не получение бонуса в сек.
        
		var now = new Date();
		var ts = Math.ceil(now.getTime() / 1000); //TS в сек.
        
        
        var fullSec = lim - ts;
        
        var dayOst = parseInt((fullSec/(60*60*24)));// сколько дней до бонуса
        
        var hourOst = parseInt((lim/(60*60)) - (ts/(60*60)));// Кол-во часов от сегодня до бонуса
        
        var hour = parseInt(hourOst/24);//Получаем (целое число) сколько чаов до бонуса по 60
        
        var hourXX = hourOst - (hour*24);//Сколько часов осталось в формате ХХ
    
        var minOst = parseInt((lim/60) - (ts/60)); // Кол-во минут от сегодня до бонуса
        
        var min = parseInt(minOst/60);//Получаем (целое число) сколько мин до бонуса по 60
        
        var minXX = minOst - (min*60);//Сколько мин осталось в формате ХХ
        
        var secOst = parseInt(lim - ts); // Кол-во секунд от сегодня до бонуса
        
        var sec = parseInt(secOst/60);// целое число сек до бонуса по 60
        
        var secXX = secOst - (sec * 60);//Сколько сек осталось в формате ХХ
        
        if(dayOst == 0) $('span#bon_day').remove();
        if(hourXX == 0 && fullSec < 60*60 && dayOst == 0) $('span#bon_hour').remove();
        if(minXX == 0 && fullSec == 60) $('span#bon_min').remove();
        
        $('span#bon_day').text('').append(dayOst + " дней");
        $('span#bon_hour').text('').append(hourXX + " часов");
        $('span#bon_min').text('').append(minXX + " минут");
        $('span#bon_sec').text('').append(secXX);
        
        if(fullSec == 0){
            prepareToBonus();
            return;
        }
        setTimeout(function(){timerToBonus()},1000);//Рекурсия каждую секунду
        
        
    }
    
    
    
    
    
})();