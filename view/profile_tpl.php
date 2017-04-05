<h4>Страница пользователя <?= $login ?></h4>

<div class="image">
    <img src="/images/<?= $img ?>" class="img-responsive" alt="<?= $login ?>">
</div>
<p>
   <?= $toberef ?>
</p>

<p>Ваш рейтинг: <?= $rating ?> баллов</p>
<p>Ваш баланс: <?= $balance ?> руб.</p>
<p>Ваш реферер / рефбек / дата присоед.: <?= $referer ?> / <?= $ref_b ?> / <?= $date_ref ?></p>
<p>Количество полученных бонусов: <span id="b"><?= $b ?></span></p>
<p>Дата последней активности: <?= $date_act ?></p>
<p>Дата регистрации: <?= $date_reg ?></p>



<?= $usersettings ?>
    
<?= $userstats ?>

    
