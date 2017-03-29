<h4>Страница регистрации</h4>

<div class="ref-preview">
    <?= $refers ?> 
</div>

<div class="form registration">
            
    <p>Логин:</p>
    <p>
       <span></span>
        <input type="text" id="login" name="login" placeholder="Ваш логин" autofocus /><span class="icon" ></span>
        
    </p>
    <p>Пароль:</p>
    <p>
       <span></span>
        <input type="password" id="password" name="password" placeholder="Ваш пароль" /><span class="icon" ></span>
    </p>
    <p>Номер кошелька PAYER:</p>
    <p>
       <span></span>
        <input type="text" id="wallet" name="wallet" placeholder="P123456789" /><span class="icon" ></span>
        <input type="hidden" id="ip" value="<?= $ip ?>">
        <input type="hidden" id="ref_id" value="<?= $ref_id ?>">
    </p>
    <p>
       <span></span>
        <div style="transform:scale(0.80);transform-origin:0 0" class="g-recaptcha" data-sitekey="6LfvuRcUAAAAAOosHGNNbcWvnLcYQ70Jew5MWzYf"></div>
    </p>
    <p>
        <a href="###" id="submit" class="btn btn-success registration">Зарегистрироваться</a>
    </p>
    
</div>
