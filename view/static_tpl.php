<div class="panel panel-default">
    <div class="panel-heading">

    <a id="<?= $id ?>" href="http://<?= $url ?>" class="static" target="_blank">
        <?= $title ?>
    </a>

    <div style="float:right">
        <a data-toggle="collapse" class="open" data-parent="#collapse-group" href="#st<?= ($i+1) ?>">
            <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
        </a>
    </div>

    </div>
    <div id="st<?= ($i+1) ?>" class="panel-collapse collapse">
         <div class="panel-body">
             <p>Адрес: http://<span class="url"><?= $url ?></span></p>
             <p>Описание: <span class="desc"><?= $desc ?></span></p>
             <p>Количество просмотров (всего): <?= $n ?></p>
             <p>Осталось просмотров: <span class="ost"><?= $ost ?></span></p>
         </div>
    </div>
</div>