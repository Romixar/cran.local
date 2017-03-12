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
       <input type="file" name="avatar" id="file" accept="image/*,image/jpeg" />
   </p>
   
   
   
   <div class="panel panel-info">
 <div class="panel-heading">
 <a data-toggle="collapse" data-parent="#collapse-group" href="#el3"><?= $text ?> E-mail</a>
 </div>
 <div id="el3" class="panel-collapse collapse">
 <div class="panel-body">
     
     <p>
       <span></span>
        <input type="email" id="email" name="email" placeholder="Ваш email" />    
    </p>
     
     
 </div>
 </div>
 </div>
   
   <div class="panel panel-info">
 <div class="panel-heading">
 <a data-toggle="collapse" data-parent="#collapse-group" href="#el2">Загрузить аватар</a>
 </div>
 <div id="el2" class="panel-collapse collapse">
 <div class="panel-body">
     
     <p>Аватар:</p>
   <p>
      <span></span>
       <input type="file" name="avatar" id="file" accept="image/*,image/jpeg" />
   </p>
     
     
 </div>
 </div>
 </div>

   
   <div class="panel panel-info">
 <div class="panel-heading">
 <a data-toggle="collapse" data-parent="#collapse-group" href="#el1">Изменить пароль</a>
 </div>
 <div id="el1" class="panel-collapse collapse in">
 <div class="panel-body">
     
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
     
     
 </div>
 </div>
 </div>
   


    <p>
        <a href="###" id="submit" tabindex="-1" class="btn btn-success profile">Сохранить настройки</a>
    </p>

</form>
</div>
    
    

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

    
    
