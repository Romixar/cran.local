<div class="panel panel-default">
    <div class="panel-heading">

    <a id="<?= $id ?>" href="<?= $url ?>" class="static<?= $cl ?>" target="_blank">
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
             <p>Адрес: <span class="url"><?= $url ?></span></p>
             <p>Название ссылки: <span class="desc"><?= $title ?></span></p>
             <p>Количество просмотров: <?= $v ?></p>
         </div>
    </div>
</div>