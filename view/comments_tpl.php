
    <li id="comm_<?= $comment['id'] ?>">

    
<div class="panel panel-info">
  <div class="panel-heading">
  
  <img src="/images/1b.jpg" class="user" alt="">
  <span><?= $commment['name'] ?></spanp><br/>
  <span>Написано: <?= $comment['date_add'] ?></span>
  
  </div>
  <div class="panel-body">
    <?= $comment['text'] ?>
  </div>
  <p class="ftr"><a href="#comm_<?= $comment['id'] ?>">Ответить</a></p>
</div>        

    <?php if($comment['childs']){ ?>

        <ul class="childs">
            <li id="comm_<?= $comment['id'] ?>">
                <div class="panel panel-info">
                  <div class="panel-heading">
                      <img src="/images/1b.jpg" class="user" alt="">
                      <span><?= $comment['name'] ?></spanp><br/>
                      <span>Написано: <?= $comment['date_add'] ?></span>
                  </div>
                  <div class="panel-body">
                    <?= $comment['text'] ?>
                  </div>
                  <p class="ftr"><a href="#comm_<?= $comment['id'] ?>">Ответить</a></p>
                </div>
            </li>

        </ul>
    
    <?php } ?>
    
    </li>
    

