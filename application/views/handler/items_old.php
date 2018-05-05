<?php
/**
 * @var $data
 *
 */

use \components\HString;
use \components\Parsedown;

isset($desc) OR $desc = '';
//var_dump($data[1]);
?>

    <div class="items_main_description">
        <?= Parsedown::instance()->text($desc);?>
    </div>

<?php if (is_array($data) && !empty($data)): ?>
<?php foreach ($data as $item): ?>

<div class="items">

    <div class="is_title clear full">
        <div class="is_links">
            <a class="is_title_main" href="/<?=$item['c_link']?>/<?=$item['sc_link']?>/<?=$item['s_link']?>"><?=$item['s_title']?></a>
        </div>
    </div>

    <?php if (!empty($item['s_description'])): ?>
        <div class="is_desc">

            <div class="is_info">
            <?php
            if($vote = HString::voteDisplay($item['s_vote'])):
                echo 'Оценка: <span>'.$vote.'</span> ';
            endif;

            echo 'Обновленно: <span>'.$item['s_created'].'</span> ';
            echo 'Автор: <span>'.$item['u_name'].'</span> ';

            ?>
            </div>
            <hr />

            <?php
            echo Parsedown::instance()
                ->text(htmlspecialchars_decode($item['s_description']));
            ?>
        </div>
    <?php endif;?>

</div>

<?php endforeach;?>
<?php endif;?>