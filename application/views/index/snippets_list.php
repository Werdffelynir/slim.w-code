<?php
/**
 * @var $dataSnippet
 */

is_array($data) OR $data = false;
?>

<div class="snippets_list full clear">

    <h2>Последние добавленные на сайте статьи и сниппеты:</h2>

    <p class="index_text">Все самые последние, добавления или обновления сниппетов и свежих блоговых записей.</p>

<? if($data):?>

    <ul class="snippet_item">
    <?php foreach($data as $item) : ?>

        <li>
            <div class="si_title"><a href="/<?= "{$item['clink']}/{$item['sclink']}/{$item['link']}";?>"><span class="si_cat"><?= $item['ctitle']?></span> <?= $item['title']?></a></div>
            <div class="si_desc"><?= $item['description']?></div>
        </li>

    <?php endforeach;?>
    </ul>

<? else:?>

<? endif;?>

</div>



