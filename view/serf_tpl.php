<div class="panel panel-default">
    <div class="panel-heading">

    <a href="http://cran.local/serf_page.php?url=<?= $url ?>&serf_id=<?= $id ?>&price=<?= $price ?>&timer=<?= $timer ?>&rand=<?= $rand ?>&title=<?= $title ?>" class="linkserf<?= $cl ?>" target="_blank">
        <?= $title ?>
    </a>

    <div style="float:right">
        <a data-toggle="collapse" class="open" data-parent="#collapse-group" href="#el<?= ($i+1) ?>">
            <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
        </a>
        <span class="price"><?= $price ?></span>
    </div>

    </div>
    <div id="el<?= ($i+1) ?>" class="panel-collapse collapse">
         <div class="panel-body">
             <p>Адрес: http://<span class="url"><?= $url ?></span></p>
             <p>Описание: <span class="desc"><?= $desc ?></span></p>
             <p>Количество просмотров (всего): <?= $n ?></p>
             <p>Осталось просмотров: 65</p>
         </div>
    </div>
</div>