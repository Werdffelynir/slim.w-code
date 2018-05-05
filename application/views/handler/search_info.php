<?php

isset($data) OR $data = [];
?>
<div class="search_info">
    <?php if(empty($data)): ?>

        <p>Нет результата - нет информиции. </p>

    <?php else: ?>

        <p>Количество совпадений: <?= $data['length'] ?></p>

        <p>Категрии:</p>
        <ul>
        <?php foreach($data['category'] as $link=>$title):?>
            <li><a href="/<?= $link?>"><?= $title?></a></li>
        <?php endforeach;?>
        </ul>

        <p>Субкатегрии:</p>
        <ul>
            <?php foreach($data['subcategory'] as $link=>$title):?>
                <li><a href="/<?= $link?>"><?= $title?></a></li>
            <?php endforeach;?>
        </ul>

    <?php endif; ?>
</div>