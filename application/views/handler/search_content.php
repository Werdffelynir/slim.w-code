<?php

isset($word) OR $word = "";
isset($data) OR $data = [];
?>
<div class="search_content">

    <?php if(empty($data)): ?>


        <div class="grid clear" style="margin-top: 25px">

            <div class="grid_2 first">
                <img src="/images/grimace.png" alt="grimace" style="text-align: center"/>
            </div>
            <div class="grid_10">
                <?php if(empty($word)): ?>
                    <h2>Пустота и мрак. </h2>
                    <p>Поиск "ничего" невозможен. Попробуйте ввести в строку поиска что-нибудь, это должно помочь.</p>
                <?php else: ?>
                    <h2>Поиск по запросу "<?= $word?>" не дал результата. </h2>
                    <p>Попробуйте поискать по альтернативным словам или изменить комбинацию запроса. </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>

        <p class="search_title">Результат поиска по запросу <span><?= $word?></span></p>

        <?php foreach($data as $item):?>
            <div class="search_item">
                <p class="search_item_title"><a href="/<?= "{$item['clink']}/{$item['sclink']}/{$item['link']}"?>"><?= $item['title']?></a></p>

                <a href="/<?= "{$item['clink']}"?>"><?= $item['ctitle']?></a> /
                <a href="/<?= "{$item['clink']}/{$item['sclink']}"?>"><?= $item['sctitle']?></a>

                <div class="search_item_desc">
                    <?= $item['description']?>
                </div>
            </div>
        <?php endforeach;?>

    <?php endif; ?>

</div>