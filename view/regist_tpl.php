<h4>Страница регистрации</h4>

<?= $refers ?>


<!--
<div class="ref-page">
    
    <p>Выберите одного реферера и на ваш баланс поступит бонус 2,00 руб.!</p>
    
    <div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="/images/no-user-image.gif" alt="проверка">
      <div class="caption">
        <h3>Thumbnail</h3>
        <p class="desc">Проверка пррка про п проверка!</p>
        <p><a href="#" class="btn btn-primary btn-xs" role="button">Выбрать</a></p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="/images/no-user-image.gif" alt="проверка">
      <div class="caption">
        <h3>Thumbnail</h3>
        <p class="desc">Проверка проверка ркапроверка проверка!</p>
        <p><a href="#" class="btn btn-primary btn-xs" role="button">Выбрать</a></p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="/images/no-user-image.gif" alt="проверка">
      <div class="caption">
        <h3>Thumbnail</h3>
        <p class="desc">Проверка проверка проверка проверка проверка!</p>
        <p><a href="#" class="btn btn-primary btn-xs" role="button">Выбрать</a></p>
      </div>
    </div>
  </div>
  
</div>
    

</div>
-->



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
        <input type="text" id="wallet" name="password" placeholder="P123456789" /><span class="icon" ></span>
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
