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