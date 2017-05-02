(function(){
    
    var submit;// разрешение на отправку формы
    
    activeMenu();// определение активного пункта меню
    inpFocus();// проверка фокуса полей
    
    clearBorder();// аналог inpFocus() НО с делегированием

    var act = 'controller/controller';
    var patLogPas = /^[a-z0-9-\._]+$/i; // проверка логина/пароля
                    ///^[a-z0-9-\._]+$/i
    var patEmail = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i;
    var patErrLogin = /(admin|moderator)/i;
    
    var patUrl = /https?:\/\/([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
    
    var elem; // объект кнопка
    var textButton = ''; // текст нажатой кнопки
    
    var txtarea; // поле ввода комментария
    var unicID = 1000;
    var p_id; // ID комментария родителя
    var childs = false;// есть ли дети у коммента
    
    var regP = /^\d{1,4}(\.){0,1}\d{0,4}$/; // регулярка цены
    var tableRefs; // модальное окно с выбором рефералалов
    var trRefs; // строка выбранного реферала
    var buyID; // ID покупаемого реферала
    var refLg; // Логин покупаемого реферала
    
    
    var uri = location.href;
    if(uri.split('/')[3] == '' || uri.split('/')[3] == '###'){
        
        
        if($.cookie('user') !== ''){
                        
            console.log('существует user');
            console.log($.cookie('user'));
            
            
            checkUserLim();
            
        }
        
        if($.cookie('user_out') !== ''){
            
            console.log('вышедший');
            console.log($.cookie('user_out'));
            
        }
        
    }
    
    $(document).on('click','a.linkserf',function(e){// нажатие на серф ссылку
        
        if($(this).hasClass('disabled')) e.preventDefault();
        else{
            
            $(this).addClass('disabled');
            
            var sp = $(this).parent().parent().find('span.ost'); // осталось просмотров
            
            var ost = Number(sp.text()) - 1;
            
            sp.text('').text(ost);
            
        }
            
        
        
    });
    
    $(document).on('click','a#addstaticlink',function(e){// создание статич ссылки
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        if(!validReklBalance()){
            viewMessage(getTplMes('Недостаточно средств на рекламном счёте!','danger'));
            return;
        }
        
        var url = $('#url');
        var h;
        var desc = $('#desc');
        var qntday = $('#qntday');
                
        var opt = $('#linkselect option:selected').val();// выбранный селект индекс опшина
        
        if(validStaticLink(url,desc,qntday)){
            
            viewIcon3($(this), 'refresh gly-spin');// запуск крутилки в кнопке
            
            qntday = qntday.val();
            qntday = qntday.replace(".","").replace(" ","");
            
            h = (url.val()[4] == 's') ? 1 : 0; // уточню протокол
            
            url = url.val().substr((url.val().indexOf('//'))+2);// обрезка протокола
            
            var str = '&url='+$.trim(url)+'&h='+h+'&desc='+$.trim(desc.val())+
                      '&qntday='+qntday+'&opt='+opt;
            
            post_query('add_statlink', str);
            return;
        }
    });
    
    function validStaticLink(url,desc,qntday){
        
        submit = true;// запрет второй отправки (по ENTER например)
        
        var days;
        
        if(!patUrl.test($.trim(url.val()))) validMessage(url, 'ERR_URL');
        if($.trim(desc.val()).length > 60) validMessage(desc, 'ERR_LEN');
        
        days = qntday.val();
        
        if(days === '') validMessage(qntday, 'ERR_EMP');
        
        days = days.replace(".","").replace(" ","");
        
        if(!regP.test(days)) validMessage(qntday, 'ERR_DIG');
        
        if(submit) return true;
        return false;
    }
    
    $(document).on("focusout", 'input#qntday', function(){ // потеря фокуса дни статич ссылки
        
        calcReklForm();
    });
    
    $(document).on("change", 'select#linkselect', function(){// выбор селект статич ссылки
        
        calcReklForm();
    });
    
    function calcReklForm(){
        
        var qntday = $('#qntday').val();
        
        qntday = Number(qntday.replace(".","").replace(" ",""));

        if(isNaN(qntday)) return; // если введено не число то выход
        
        var total;
        
        var sum;
        
        var opt = $('#linkselect option:selected').val();// выбранный селект индекс опшина
        
        if(Number(opt)) sum = 0;
        else sum = 5 * qntday; // прибавим по 5 руб?день за выделение
            
        total = (qntday * 20) + sum;
        
        $('span#sum').text('').text(total);
    }
    
    function validReklBalance(){
        
        var acnt2 = $('span#acnt2').text();
        
        acnt2 = Number(acnt2.replace(",",".").replace(" ",""));
        
        var totalsum = Number($('span#sum').text());
        
        if((acnt2 - totalsum) >= 0) return true;
        else return false;
    }
    
    
    
    
    
    
    

        // РЕГИСТРАЦИЯ //
    $("div.registration input#login").on("change", function(){// набран текст и убран фокус
        
        $('a#submit').removeClass('disabled');
        
        $('a#submit').text('Зарегистрироваться');
        
        submit = true;
        
        if(patErrLogin.test($(this).val())) validMessage($(this), 'ERR_LOG');
        if($(this).val().indexOf(' ') !== -1) validMessage($(this), 'ERR_NBS');
        if(!patLogPas.test($(this).val())) validMessage($(this), 'ERR_CHR');
        if(($(this).val()).length > 100) validMessage($(this), 'ERR_LEN');
                
        if(submit){
            
            viewIcon2($(this), 'refresh gly-spin');// запуск крутилки
            
            var str = '&login='+$(this).val();
            
            post_query('reg_login', str);
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
    $('div.form a.registration').click(function(e){
        
        e.preventDefault();
        validRegAndSubmit();
    });
    $("div.registration").keyup(function(e){// запустить валидацию и отправку
        
        if(e.keyCode == 13) validRegAndSubmit();
    });
    
    // РЕГИСТРАЦИЯ КОНЕЦ //
    
    
    
    $('div.login input#login').on('change', function(){
        
        $('a#submit').removeClass('disabled');
        
        $('a#submit').text('Войти');

    });
    $('div.profile input#email').on('focus', function(){ // снять блокироку кнопки
        
        $('a#submit').removeClass('disabled');
        
        $('a#submit').text('Сохранить настройки');

    });


                


    
    
    
    
    
    
    
    
    $('div.form a.mes').click(function(e){
        
        e.preventDefault();
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
    
    
    
    
    
    
    
    $('div.login a#submit').click(function(e){// авторизация
        
        e.preventDefault();
        
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
    $('div.login a#recoverypass').click(function(e){// восстановление пароля
       
        e.preventDefault();
        
        $('div.main div h4').remove();
        if($('div.alert')) $('div.alert').remove();
        $('title').text('').text('Страница восстановления пароля');
        $('div.login').replaceWith(getRecoveryTpl());
        return;
        
    });
    $(document).on('click', 'a#recovery', function(e){// восстановление пароля
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        var em = $('input#email');
        var lg = $('input#login');
        var wt = $('input#wallet');
        
        if(validRecovery(em,lg,wt)){
            
            viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
            var str = '&email='+em.val()+'&login='+lg.val()+'&wallet='+wt.val();
            post_query('do_recov', str);
        }else return false;
    });
    $(document).on('click', 'a#comment', function(e){// добавление коммента
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        var mes = $('textarea#text');
        p_id = (p_id) ? p_id : 0;
        
        if(validComment(mes)){
            
            viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
            var str = '&parent_id='+p_id+'&text='+mes.val();
            post_query('do_comment', str);
            
            var name = ($.cookie('user')) ? JSON.parse($.cookie('user')).login : 'Аноним';
            
            // установить на место textarea если это ответ
            if(p_id != 0){
                
                $('ul#comments').before(txtarea);
                
                var divcomm = $('div#comm_'+p_id).clone();

                divcomm.attr('id','comm_'+unicID);
                
                // подставить блок с комментарием
                divcomm.find('span#name').text('').text(name);
                divcomm.find('span#date').text('').text('только что');
                divcomm.find('div.panel-body').text('').text(mes.val());
                
                if(!childs){// добавление ребенка или корневого
                    $('div#comm_'+p_id).after('<ul class="childs chldcomm_'+p_id+'"><li></li></ul>');
                    $('ul.chldcomm_'+p_id+' li:first').after(divcomm);
                    
                }else $('div#comm_'+p_id).after(divcomm);
            }else{
                
                var divlastcomm = $('ul#comments li:first div.panel-info').clone();// посл коммент
                
                divlastcomm.attr('id','comm_'+unicID);
                
                // подставить блок с комментарием
                divlastcomm.find('span#name').text('').text(name);
                divlastcomm.find('span#date').text('').text('только что');
                divlastcomm.find('div.panel-body').text('').text(mes.val());
                
                $('ul#comments').prepend('<li></li>');
                $('ul#comments li:first').append(divlastcomm);

                
            }
            
            unicID += unicID;// увеличю случайный ID
            
            mes.val('');
            
            
            
            
        }else return false;
    });
    $(document).on('click','a[href^="#comm"]', function(e){// добавление ответа на комментарий
        
        e.preventDefault();
        
        txtarea = $('div.comment').remove();// врем-е удаление textarea
        
        var ul = $(this).parent().parent().parent().parent();
        
        if(ul.hasClass('childs')) childs = true;

        $(this).parent().parent().after(txtarea);// подстановка textarea после коммента
        
        var pos = e.target.href.indexOf('_') + 1;
        p_id = e.target.href.substr(pos);
    });
    
    $(document).on('click', 'a#buy_ref_page', function(e){// стена рефереров
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        // сделать проверку баланса перед отправкой запроса
        
        
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('buy_ref_page', '');
        return false;

    });
    $(document).on('click', 'div.ref-preview a.btn', function(e){// выбор реферера при регистр-и
        
        e.preventDefault();
        
        var ref_id = $(e.target).parent().parent().parent().parent().attr('id');
        
        $('div.ref-preview div.row div').removeClass('selected');
        
        $(e.target).parent().parent().parent().addClass('selected');
        
        ref_id = ref_id.substr(4);
        
        $('input#ref_id').val(ref_id);

        
    });
    
    
    $(document).on('click', 'a#uprating', function(e){// получение баллов раз в сутки
        
        e.preventDefault();
        
        //console.log('есть клик!');
        
        elem = $(this);
        textButton = $(this).text();
        
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('get_rating', '');
        
        var r = Number($('span#rating').text());
        
        $('span#rating').text('').text((0.20 + r).toFixed(2));
        
    });
    
    $(document).on('click', 'a#addref', function(e){// хочу стать его рефералом
        
        e.preventDefault();
        
        //console.log('есть клик!');
        
        elem = $(this);
        
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
        
        var arr = (location.href).split('/');
        
        if(validGET(arr[4])){
            
            post_query('do_addref', '&ref_id='+arr[4]);
            
        }
    });
    
    function validGET(str){
        
        if(str.length > 20) return false;
        if(!patLogPas.test(str)) return false;
        
        return true;
    }
    
    $(document).on('click', 'a#set_ref_back', function(e){// установка рефбэка
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
//        var boxes = $("input:checkbox");
//        
//        var theArray = new Array();
//        
//        for(var i=0; i<boxes.length; i++){
//          
//        var box = boxes[i]; 
//
//            if($(box).prop('checked')){
//                
//                theArray[theArray.length] = $(box).attr('id');
//            }
//        }
//        
//        var str = JSON.stringify(theArray);
        
        var opt = $('#percent_rb option:selected').val();// выбранный процент

        console.log(opt);
        
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('do_ref_b', '&user_ids='+str+'&percent_rb='+opt);
        
        
    });
    
    $(document).on('click', 'a#buy_ref_stock', function(e){// выбор реферала на бирже д/покупки
        
        e.preventDefault();
        
        trRefs = e.target.closest("tr");// строка выбранного реферала

        var tds = $(trRefs).find('td'); // коллекция td-эшек
        
        var str1 = $(tds[0]).html(); // HTML код перв ячейки
        
        buyID = str1.substr(0,str1.indexOf('<br>'));// ID покупаемого реферала
        
        var str = str1.substr(str1.indexOf('<br>') + 4);
        
        refLg = str.substr(0,str.indexOf('<br>'));
        
        var price = $(tds[5]).html(); // HTML код ячейки с ценой
        
        // запускать модальное окно с покупкой
        
        tableRefs = $('div.modal-content').clone();
        
        $('div.modal-header h4').text('Покупка реферала на бирже');
        
        $('div.modal-body').text('').append('<p>Вы покупаете реферала <span style="color:red">ID '+buyID+' | '+refLg+'</span> на бирже</p><p>С вашего баланса будет списано '+price+' руб.</p>');
        
        var but = $('div.modal-footer button');
        
        but.text('Купить').attr('id','buy_ref').removeClass('btn-primary').addClass('btn-success');
        
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: true 
        });
        
    });
    
    $(document).on('click', 'button#buy_ref', function(e){ // покупка реферала
        
        e.preventDefault();
        
        if(buyID !== undefined){
            
            console.log('щас покупка реферала '+buyID+' логин'+refLg);
            
            post_query('buy_refstock', '&user_id='+buyID+'&login='+refLg);
            
            
        }else sysMes('danger','Не выбран реферал для покупки!');
        
        
        
    });
    
    $(document).on('click', 'a#refstock', function(e){ // кнопка Выставить на биржу
        
        e.preventDefault();
        
        // открытие модального окна с таблицей реыералов
        if(tableRefs !== undefined) $('div#myModal div').text('').append(tableRefs);
        
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: true 
        });
        
    });
    
    $(document).on('click', 'button#addrefstock', function(e){// добавление реферала на биржу
        
        e.preventDefault();
        
        elem = $('a#refstock');
        textButton = $(this).text();
        
        
        var boxes = $("input:checkbox");// коллекция выделенных чекбоксов
        
        var theArray = new Array();
        
        var trs = [];
        
        for(var i=0; i<boxes.length; i++){
          
        var box = boxes[i];
        
            if($(box).prop('checked')){
                var id = $(box).attr("id");
                
                var price = $('input#price_'+id).val();
                
                price = validPrice(price);
                
                if(price === false) return;
                
                theArray[theArray.length] = [
                    id,
                    price
                ];
                
                trs[trs.length] = $(box).parent().parent();// остальные ячейки
            }
        }
        
        if(theArray.length == 0){
            sysMes('danger','Не выбрано ни одного элемента!'); 
            return;
        }
        
        var str = JSON.stringify(theArray);
        
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('addrefstock', '&referals='+str);
        
        htmlStockTable(theArray, trs);
        
    });
    

    
    function validPrice(price){
        
        price = price.trim();
        
        price = formSum(price); // заменить запятую на точку и округлить до 2-х
                
        if(isNaN(price) || !regP.test(price)){
            
            // вывести сообщение об ошибке формата
            sysMes('danger','Неверно заполнено поле цена!'); 
            return false;
        }
                
        if(price.indexOf('.') !== -1) price = Number(price).toFixed(2);
        return price;
    }
    
    function htmlStockTable(arr, trs){
        
        var table = $('table#listrefstock tbody');
        
        var obj = JSON.parse($.cookie('user'));
        
        for(var i=0; i<arr.length; i++){
            
            var tds = $(trs[i][0]).find('td');// коллекция td в строке выбранного реферала
            
            var tr = '<tr><td></td><td>'+tds[1].innerHTML+'<br>'+obj.login+'</td><td>'+tds[2].innerHTML+'</td><td>'+tds[3].innerHTML+'</td><td>'+tds[4].innerHTML+'</td><td>10%</td><td>'+arr[i][1]+'</td></tr>';
        
            table.prepend(tr);
        }
    }
    
    function sysMes(type,mes){
        
        var al = $('div.alert');
        if(al) al.remove();
        $(document).find('div.main div.col-md-12 h4:first').after(getTplMes(mes,type));
    }
    
    function formSum(sum){// заменить запятую на точку и округлить до 2-х
        sum = sum.replace(",",".");
        sum = sum.replace(" ","");
        if(!isNaN(sum) && regP.test(sum)){
            if(sum.indexOf('.') !== -1) sum = Number(sum).toFixed(2);
            return sum;
        }
        return false;
    }
 
    
    
    
    //    сделать ассинхронно
//    $(document).on('click', 'a#refpage', function(e){// открытие стена рефереров
//        
//        e.preventDefault;
//        
//        elem = $(this);
//        textButton = $(this).text();
//        
//        
//        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке
//        
//        post_query('get_refpage', '');
//        return false;
//
//    });

    $(document).on('click', 'p#sunduki', function(e){
        
        e.preventDefault();
        
        var sund = e.target.id;
        
        var sum = $('input#lottery_sum').val();
        
        if(sum === '') validMessage($('input#lottery_sum'), 'ERR_EMP');
        
        sum = formSum(sum);// заменить запятую на точку и округлить до 2-х
        
        if(sum){
            
            console.log('цифры - '+sum);
            
            post_query('do_lottery', '&sund_id='+sund+'&sum='+sum);
            
            
            
            return;
        }else validMessage($('input#lottery_sum'), 'ERR_ERR');

        console.log('Ошибка в sum');
        
    });

    
    $('div.profile a#submit').click(function(e){// отправка img аватарки и e-mail
        
        e.preventDefault();
        
        elem = $(this);
        
        if(validEmailAndFile()) submitJson($('#my_form'));// отправить все из my_form
        
    });
    
    $('div.profile a#change').click(function(e){// смена пароля
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        var pwd1 = $('input#pass1');
        var pwd2 = $('input#pass2');
        
        if(validPassws(pwd1,pwd2)){

            viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке

            var str = '&pass1='+pwd1.val()+'&pass2='+pwd2.val();

            post_query('do_pass', str);
            
        }
        return;
    });
    

    $('a#get_ref_list').click(function(e){
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        viewIcon3($(this), 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('get_ref_list', '');
        
    });
    $('a#get_b_list').click(function(e){
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        viewIcon3($(this), 'refresh gly-spin');// запуск крутилки в кнопке
        
        post_query('get_b_list', '');
        
    });
    
    $('a#get_bonus').click(function(e){
        
        e.preventDefault();
        
        elem = $(this);
        textButton = $(this).text();
        
        if($.cookie('user')){
            
            if(checkUserLim()) return;
            
        }else{
            
            var mes = 'Зарегистрируйтесь или авторизуйтесь, чтобы ежедневно получать бонусы!_000';
            
            $('div.main div.col-md-12 h4:first').after(getTplMes(mes, 'danger'));// вывод сист сообщения
            return;
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
                
                viewIcon3($('a#get_bonus'), 'refresh gly-spin');// запуск крутилки в кнопке
                
                if($('span#wait_bonus')) $('span#wait_bonus').remove();
                    
                $('div.bonus').before('<span id="wait_bonus">До получения бонуса осталось <span id="bon_day"></span> <span id="bon_hour"></span> <span id="bon_min"></span> <span id="bon_sec"></span> секунд.</span>');
                    
                timerToBonus();// счётчик сколько осталось до получения

                return true;
            }
            return false;// пришло время для бонуса
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
        if(lg.val().length > 30) validMessage(lg, 'ERR_LEN');
        
        if(pwd.val() === '') validMessage(pwd, 'ERR_EMP');
        if(pwd.val().indexOf(' ') !== -1) validMessage(pwd, 'ERR_NBS');
        if(pwd.val().length > 15 || pwd.val().length < 4) validMessage(pwd, 'ERR_PSW');
        
        if(submit) return true;
        return false;
    }
    
    function submitLogPass(lg,pwd){
            
        viewIcon3(elem, 'refresh gly-spin');// запуск крутилки в кнопке

        var str = '&login='+lg.val()+'&password='+pwd.val();
        
        post_query('do_login', str);
    }
    
    function validPassws(pwd1,pwd2){
        
        submit = true;// запрет второй отправки (по ENTER например)
        
        if(pwd1.val() === '') validMessage(pwd1, 'ERR_EMP');
        
        if(pwd1.val().indexOf(' ') !== -1) validMessage(pwd1, 'ERR_NBS');
        
        if(pwd1.val() !== '' && pwd1.val().indexOf(' ') === -1){
            
            //if(!patLogPas.test(pwd1.val())) validMessage(pwd1, 'ERR_CHR');
            if(pwd1.val().length > 15 || pwd1.val().length < 5) validMessage(pwd1, 'ERR_PSW');
        }
        
        if(pwd2.val() === '') validMessage(pwd2, 'ERR_EMP');
        
        if(pwd2.val().indexOf(' ') !== -1) validMessage(pwd2, 'ERR_NBS');
        
        if(pwd2.val() !== '' && pwd2.val().indexOf(' ') === -1){
            
            if(!patLogPas.test(pwd2.val())) validMessage(pwd2, 'ERR_CHR');
            if(pwd2.val().length > 15 || pwd2.val().length < 5) validMessage(pwd2, 'ERR_PSW');
        }
         
        if(submit) return true;
        return false;
    }
    
    function validRecovery(em,lg,wt){
        
        submit = true;// запрет второй отправки (по ENTER например)
        
        if(em.val() !== '') if(!patEmail.test(em.val())) validMessage(em, 'ERR_EML');
        
        if(lg.val() !== ''){
            if(lg.val().indexOf(' ') !== -1) validMessage(lg, 'ERR_NBS');
            if(lg.val().length > 30) validMessage(lg, 'ERR_LEN');
            if(!patLogPas.test(lg.val())) validMessage(lg, 'ERR_CHR');
        }
        
        if(wt.val() !== ''){
            if(wt.val().indexOf(' ') !== -1) validMessage(wt, 'ERR_NBS');
            if((wt.val()).length > 30) validMessage(wt, 'ERR_LEN');

            // перв символ или пусто после первого или только цифры с перв символа
            if((wt.val())[0] !== 'P' || (wt.val()).substring(1) === '' || isNaN(+(wt.val()).substring(1))) validMessage(wt, 'ERR_WAL');
        }

        if(submit) return true;
        return false;        
    }
    

    function validRegAndSubmit(){// валидация регистрации
        
        submit = true;
        
        var lg = $('div.form input#login');
        var pwd = $('div.form input#password');
        var wt = $('div.form input#wallet');
        var ip = $('div.form input#ip');
        var ref_id = $('div.form input#ref_id');
        
        if(lg.val() === '') validMessage(lg, 'ERR_EMP');
        if(lg.val().indexOf(' ') !== -1) validMessage(lg, 'ERR_NBS');
        if(patErrLogin.test(lg.val())) validMessage(lg, 'ERR_LOG');
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
    
    function validComment(mes){
        
        submit = true;
        
        if((mes.val()).length > 300) validMessage(mes, 'ERR_LEN');
        
        if(submit) return true;
        return false;
    }
    
    
    
    function post_query(name, str){

        console.log(str);

        $.ajax({// отправляем её

                url: act,
                type: 'POST',
                data: name + '_f=' + str,
                cache: false,
                beforeSend: function(data){ // сoбытиe дo oтпрaвки
                    //form.find('input[type="submit"]').attr('disabled', 'disabled');
		        },
                success: function(res){

                    console.log(res);
                    if(res){
                        obj = JSON.parse(res);
                        
                        if(obj.redirect) location.href = obj.redirect;
                        
                        if(obj.alert) alert(obj.alert);
                        
                        if(obj.sysmes){
                            
                            viewMessage(obj.sysmes);
                            setTextSubmit();
                            if(obj.auto) clearAndReplRecovery();
                        }
                        
                        if(obj.icon) viewIcon(obj.icon, obj.click);
                        
                        if(obj.err) validMessage($('div.registration input#login'), obj.err);
                        if(obj.dataRefList){
                            
                            getRefList(obj.dataRefList);
                            setTextSubmit();
                        }
                        if(obj.dataBList){
                            
                            getBList(obj.dataBList);
                            setTextSubmit();
                        }
                        if(obj.dataRefPage) buildDataRefPage(obj.dataRefPage);
                        
                        if(obj.mycookie){
                            
                            if(obj.mycookie.img != undefined) buildRefPage(obj.mycookie);
                            if(obj.mycookie && obj.mycookie.img == undefined) saveMyCookie(obj.mycookie);
                        }
                        if(obj.flname === 'ok') $(trRefs).remove();// удаление купленного реферала
                        
                        if($('a#addref')) $('a#addref').remove();// кнопка Хочу стать рефералом
                        

                            
                        
                    }else removeDisabled();// разблокир и снять крутилку
                },
                error: function(xhr, ajaxOptions, thrownError){
                    
		            console.log(xhr.status); // пoкaжeм oтвeт сeрвeрa
		            console.log(thrownError); // и тeкст oшибки
		        },
		       complete: function(data) { // сoбытиe пoслe любoгo исхoдa
                   
                   if($('a#uprating')) $('a#uprating').remove();
                   
                   removeDisabled();// разболир и снять крутилку
                   
                   
		            //form.find('input[type="submit"]').prop('disabled', false);
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
            'ERR_PSW': 'Пароль должен быть от 5 до 15 символов!',
            'ERR_LOG': 'Недопустимое слово в Вашем логине!',
            'ERR_ERR': 'Поле заполнено не верно!',
            'ERR_URL': 'Ссылка указана не верно!',
            'ERR_DIG': 'В поле должны быть только цифры!',
            
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
        
        var al = $('div.alert');

        if(al) al.remove();
        
        $(document).find('div.main div.col-md-12 h4:first').after(mes);// вывод сист сообщения
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
    
    function clearAndReplRecovery(){
        
        $(document).find('div.form').remove();
        post_query('auto_recov','');// повторный запрос для отправки письма пользов-лю
        return;
    }
    
    function setTextSubmit(){
        if(elem !== undefined) elem.text('').append(textButton);
    }
    
    function getTplMes(mes, type){
        return '<div class="alert alert-' +type+ ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +mes+ '</div>';
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
    
    function getBList(data){
        
        var str = '';
        var res = 0;
        for(var i=0; i<(data.length); i++){

            str += '<tr><td>'+(i+1)+'.</td><td>'+data[i].date_add+'</td><td>'+data[i].sum+'</td></tr>'; res += Number(data[i].sum);
        }
        
        var strRes = '<tr class="success"><th>Всего:</th><th><span>'+i+' бон. на сумму</span></th><th><span>'+res.toFixed(2)+'</span></th></tr>';
        
        $('table.b_list thead').append(strRes);
        $('table.b_list tbody').text('').append(str);
        
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
        
        //var inp = $('input');
        var inp = $('input:not([name=price]');
        var txt = $('textarea');
        
        inp.focusin(function(){
            $(this).css('border','none');
            $(this).prev().text('');// очистка текста перед полем
            $(this).next().text('');// очистка глификонки
            removeDisabled();
        });
        txt.focusin(function(){
            $(this).css('border','none');
            $(this).prev().text('');
        });   
    }
    
    function clearBorder(){
    
        $(document).on('focus', 'div.recovery input', function(e){
        
            $(this).css('border','none');
            $(this).prev().text('');

        });
        
        
    }
    
    
    
    function removeDisabled(){
        if(elem && elem.hasClass("disabled")) elem.text('').removeClass('disabled').text(textButton);
        return;
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
            
            checkUserLim();
            
            return;

        }
        
        if(mycookie.login && mycookie.ip){// только что вошел
        
//            if($.cookie('user_out') !== '' || $.cookie('user_out') !== undefined){
//
//                var user_out = JSON.parse($.cookie('user_out'));
//
//                if(user_out.login == mycookie.login && user_out.ip == mycookie.ip){// вошел тот же
//
//                    var user_in1 = $.cookie('user_out');// старую куку пересохраню в новую
//
//                    console.log(user_in1);
//
//                    $.cookie('user', user_in1,{
//                            expires: 5,
//                            path: '/',
//                        });
//                    $.cookie('user_out','');   
//                }else{
//                    
//                    var user_in2 = JSON.stringify(mycookie);
//
//                    $.cookie('user', user_in2,{
//                                    expires: 5,
//                                    path: '/',
//                                });
//                    $.cookie('user_out','');
//                    
//                    
//                }
//
//            }else{
                
                var user_in3 = JSON.stringify(mycookie);

                $.cookie('user', user_in3,{
                                expires: 5,
                                path: '/',
                            });
                $.cookie('user_out','');
                
            }
        
        
        //}
console.log('попал');
        location.href = '/profile';

        
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
    
    function buildRefPage(mycookie){// вставлю в начало списка реферера
                
        var clRef = $(document).find('div#ref_1').clone();
        
        clRef.attr('id','ref_16');
        
        clRef.find('img').attr('src','/images/'+mycookie.img);
          
        clRef.find('h3').text('').text(JSON.parse($.cookie('user')).login);

        $('div.ref-page div.row').prepend(clRef); /// вставляю первого
        
        
        var n = $(document).find('div.ref-page div.row').children().length;// номер посл реферера в DOM
        
        //console.log(n);
        
        if(n > 15) $(document).find('div.ref-page div.row').children()[n-1].remove();// удалю его
        
        removeDisabled();

        return;
        
    }
    
    function buildDataRefPage(data){
        
        $('div.ref-page div.row').replaceWith(data);
        removeDisabled();
        return;
    }
    
    
    function getRecoveryTpl(){
        return "<h4>Восстановление логина / пароля</h4><h5>Заполните любое из полей</h5><div class=\"form recovery\"><p>E-mail, указанный в профиле:</p><p><span></span><input type=\"email\" id=\"email\" name=\"email\" placeholder=\"Ваш e-mail\" autofocus /></p><p>Логин, указанный при регистрации:</p><p><span></span><input type=\"text\" id=\"login\" name=\"login\" placeholder=\"Ваш логин\" /></p><p>Номер вашего кошелька, указанный при регистрации:</p><p><span></span><input type=\"text\" id=\"wallet\" name=\"wallet\" placeholder=\"P0123456789\" /></p><p><a href=\"###\" id=\"recovery\" tabindex=\"-1\" class=\"btn btn-success\">Восстановить</a></p></div>";
    }
    
    

    
    
})();