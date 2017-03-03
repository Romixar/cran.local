(function(){
    
    $('div.login a').click(function(e){
        //console.log(e);
        e.preventDefault;
        
        var str = '&login='+$('div.login input#login').val()+'&password='+$('div.login input#password').val();
        var name = 'do_login';
        var url = 'controller/controller';
        
        //console.log(str);
        
        post_query(url, name, str);
        
        
        
    });
    
    function post_query(url, name, str){

        console.log(str);

        $.ajax({// отправляем её

                url: url,
                type: 'POST',
                data: name + '_f=' + str,
                cache: false,
                success: function(res){

                    //alert(res);
                    if(res){
                        obj = JSON.parse(res);
                        if(obj.redirect) location.href = obj.redirect;
                    };
                    //console.log(res);
                    


                },
            });
    };
    
    
    
    
})();