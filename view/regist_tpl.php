<h4>Страница регистрации</h4>
<div class="form registration">
            
    <p>Логин:</p>
    <p>
       <span></span>
        <input type="text" id="login" name="login" style="width:90%" placeholder="Ваш логин" autofocus /><span class="glyphicon"></span>
<!--        <span class="glyphicon"><i class="glyphicon glyphicon-refresh gly-spin"></i></span>-->
        
    </p>
    <p>Пароль:</p>
    <p>
       <span></span>
        <input type="password" id="password" name="password" placeholder="Ваш пароль" />
    </p>
    <p>Номер кошелька PAYER:</p>
    <p>
       <span></span>
        <input type="text" id="wallet" name="password" placeholder="P123456789" />
        <input type="hidden" id="ip" value="<?= $ip ?>">
    </p>
    <p>
       <span></span>
        <div style="transform:scale(0.80);transform-origin:0 0" class="g-recaptcha" data-sitekey="6LfvuRcUAAAAAOosHGNNbcWvnLcYQ70Jew5MWzYf"></div>
    </p>
    <p>
        <a href="###" class="btn btn-success registration">Зарегистрироваться</a>
    </p>
            
            
            
</div>
