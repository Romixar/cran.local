<div id="comm_<?= $comment['id'] ?>" class="panel panel-info">
  <div class="panel-heading">
  
  <img src="/images/1b.jpg" class="user" alt="">
  <span><?= $comment['name'] ?></span><br/>
  <span>Написано: <?= $comment['date_add'] ?></span>
  
  </div>
  <div class="panel-body">
    <?= $comment['text'] ?>
  </div>
  <p class="ftr"><a href="#comm_<?= $comment['id'] ?>">Ответить</a></p>
</div>