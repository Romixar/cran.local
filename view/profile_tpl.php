<h4>Страница пользователя <?= $login ?></h4>

<div class="image">
    <img src="/images/<?= $img ?>" class="img-responsive" alt="<?= $login ?>">
</div>


<p>Ваш рейтинг: <?= $rating ?> баллов</p>
<p>Ваш баланс: <?= $balance ?> руб.</p>
<p>Ваш реферер: <?= $referer ?> </p>
<p>Количество полученных бонусов: <span id="b"><?= $b ?></span></p>
<p>Дата последней активности: <?= $date_act ?></p>
<p>Дата регистрации: <?= $date_reg ?></p>
<p>Регистрационный IP: <?= $ip ?></p>
<p>Кошелёк: <?= $wal ?></p>
<p>Реферальная ссылка: <?= $ref_url ?></p>
<p>E-mail: <span id="email"><?= $email ?></span></p>


<h4>Настройки</h4>

<div class="profile">
  <form id="my_form" enctype="multipart/form-data">          
    <p><?= $text ?> E-mail для восстановления пароля:</p>
    <p>
       <span></span>
        <input type="email" id="email" name="email" placeholder="Ваш email" />    
    </p>
    

    <p>Аватар:</p>
   <p>
      <span></span>
       <input type="file" name="avatar" id="file" class="form-control" accept="image/*,image/jpeg" />
   </p>
    <p>
        <a href="###" id="submit" tabindex="-1" class="btn btn-success profile">Сохранить настройки</a>
    </p>

</form>



<h4>Смена пароля</h4>

    <p>Ваш старый пароль:</p>
            <p>
               <span></span>
                <input type="password" id="pass1" name="pass1" placeholder="Старый пароль" />    
            </p>
    <p>Ваш новый пароль:</p>
            <p>
               <span></span>
                <input type="password" id="pass2" name="pass2" placeholder="Новый пароль" />    
            </p>
            <p>
        <a href="###" id="change" class="btn btn-success">Изменить</a>
    </p>
<h4></h4>

</div>
    
    
    
    
    
    <a href="###" id="get_b_list" class="btn btn-success">Список полученных бонусов</a>

    <table class="table table-hover b_list">
       <thead>
            <tr>
                <th>№</th>
                <th>Дата получения</th>
                <th>Сумма, руб.</th>
            </tr>
       </thead>

        <tbody>
        </tbody>
    </table>
    
    
    

    <a href="###" id="get_ref_list" class="btn btn-success">Показать список рефералов</a>

    <table class="table table-hover ref_list">
       <thead>
            <tr>
                <th>№</th>
                <th>Логин</th>
                <th>Баланс, руб.</th>
                <th>Дата регистр.</th>
            </tr>
       </thead>
        <tbody>
        </tbody>
    </table>

    
