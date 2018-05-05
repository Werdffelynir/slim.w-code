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
    <div class="items_wrap">
        <?php foreach ($data as $item): ?>

        <div class="items tbl">
            <div class="tbl_cell"><?= $item['s_vote']?></div>
            <div class="tbl_cell"><a class="items_title" href="/<?=$item['c_link']?>/<?=$item['sc_link']?>/<?=$item['s_link']?>"><?=$item['s_title']?></a></div>
            <div class="tbl_cell"><?= $item['s_created']?></div>
        </div>

        <?php endforeach;?>
    </div>
<?php endif;?>