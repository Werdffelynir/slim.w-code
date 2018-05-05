<?php
/**
 * @var $category
 * @var $subcategory
 */

is_array($category) OR $category = false;
is_array($subcategory) OR $subcategory = false;

?>
<div class="snippets_map full clear">

    <h2>Навигация по категориям</h2>

<? if($category && $subcategory):?>

    <ul>
    <?php foreach($category as $cat):?>

        <li><a href="/<?= $cat['link']?>"><?= $cat['title']?></a>
            <ul>
                <?php foreach($subcategory as $sub):?>
                    <?php if($cat['id'] == $sub['idcategory']): ?>
                        <li><a href="/<?= $cat['link']?>/<?= $sub['link']?>"><?= $sub['title']?></a></li>
                    <?php endif; ?>
                <?php endforeach;?>
            </ul>
        </li>

    <?php endforeach;?>
    </ul>

<? else:?>

<? endif;?>

</div>