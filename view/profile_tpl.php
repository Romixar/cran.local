<h4>Страница пользователя <?= $login ?></h4>

<div class="image">
    <img src="/images/<?= $img ?>" class="img-responsive" alt="<?= $login ?>">
</div>


<p>Ваш баланс: <?= $balance ?> руб.</p>
<p>Дата последней активности: <?= $date_act ?></p>
<p>Дата регистрации: <?= $date_reg ?></p>
<p>Регистрационный IP: <?= $ip ?></p>
<p>Кошелёк: <?= $wal ?></p>
<p>Реферальная ссылка: <?= $ref_url ?></p>
<p>E-mail: <span id="email"><?= $email ?></span></p>
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
       <input type="file" name="avatar" id="file" accept="image/*,image/jpeg" />
   </p>

    <p>
        <a href="###" id="submit" tabindex="-1" class="btn btn-success profile">Сохранить настройки</a>
    </p>

</form>
</div>
<a href="###" id="get_ref_list" class="btn btn-success">Показать список рефералов (28)</a>
<table class="table table-hover">
    <tr>
        <th>№</th>
        <th>Логин</th>
        <th>Баланс</th>
    </tr>
    
    <tr>
       
       <?= $ref_list ?>
        <td>1.</td>
        <td>Admin</td>
        <td>100.00 руб.</td>
        
        
    </tr>
    
    
    
    <tr>
        <td>1.</td>
        <td>Admin</td>
        <td>100.00 руб.</td>
    </tr>
    <tr>
        <td>2.</td>
        <td>Admin_2</td>
        <td>100.00 руб.</td>
    </tr>
    <tr>
        <td>3.</td>
        <td>Admin_3</td>
        <td>100.00 руб.</td>
    </tr>
</table>