<h4>Страница пользователя <?= $login ?></h4>

<p>Ваш баланс: <?= $balance ?> руб.</p>
<p>Дата последней активности: <?= $date_act ?></p>
<p>Дата регистрации: <?= $date_reg ?></p>
<p>Регистрационный IP: <?= $ip ?></p>
<div class="form profile">
            
    <p>E-mail для восстановления пароля:</p>
    <p>
       <span></span>
        <input type="email" id="email" name="email" placeholder="Ваш email" autofocus />    
    </p>
    
    <p>
        <a href="###" tabindex="-1" class="btn btn-success profile">Сохранить настройки</a>
    </p>
            
</div>