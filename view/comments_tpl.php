
    <li id="<?= $comment->id ?>">
<div class="panel panel-info">
  <div class="panel-heading">
  
  <img src="/images/1.jpg" class="user" alt="">
  <span><?= $commment->name ?></spanp><br/>
  <span>Написано: <?= $comment->date_add ?></span>
  
  </div>
  <div class="panel-body">
    <?= $comment->text ?>
  </div>
  <p class="ftr"><a href="###">Ответить</a></p>
</div>        
 
    </li>
    
    <?php if($comment->childs){ ?>
    
    <li>
        <ul class="childs">
            <li id="<?= $comment->id ?>">
                <div class="panel panel-info">
                  <div class="panel-heading">
                      <img src="/images/1b.jpg" class="user" alt="">
                      <span><?= $comment->name ?></spanp><br/>
                      <span>Написано: <?= $comment->date_add ?></span>
                  </div>
                  <div class="panel-body">
                    <?= $comment->text ?>
                  </div>
                  <p class="ftr"><a href="###">Ответить</a></p>
                </div>
            </li>

        </ul>
    </li>
    
    <?php } ?>
    

