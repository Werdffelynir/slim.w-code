<?php

/**
 * @var $allCat
 * @var $listSubcat
 */

isset($allCat) OR $allCat = [];
isset($listSubcat) OR $listSubcat = [];
isset($listSnippets) OR $listSnippets = [];

isset($idCat) OR $idCat = false;
isset($idSubcat) OR $idSubcat = false;
isset($idSnipp) OR $idSnipp = false;

?>


<?php if($allCat == false): ?>

    No Information

<?php else: ?>

    <div class="edit_cat_menu">

        <div class="ecb full clear">
            <span class="grid_6 first">
                <a href="/settings/new" class="button">Category</a>
            </span>
            <span class="grid_6">
                <?php if($idCat): ?>
                    <a href="/settings/<?=$idCat?>/new" class="button">New Subcategory</a>
                <?php endif; ?>
            </span>
            <span class="full clear">
                <?php if($idSubcat): ?>
                    <a href="/settings/<?=$idCat?>/<?=$idSubcat?>/new" class="button">New Snippet</a>
                <?php endif; ?>
            </span>
        </div>



        <h3>Edit category</h3>
        <ul>
            <?php foreach ($allCat as $c): ?>
                <li><a href="/settings/<?=$c['id']?>"><?=$c['title']?></a></li>
            <?php endforeach; ?>
        </ul>

        <?php if(!empty($listSubcat)): ?>

            <h3>Edit Subcategory</h3>
            <ul>
                <?php foreach ($listSubcat as $sc): ?>
                    <li><a href="/settings/<?=$idCat?>/<?=$sc['id']?>"><?=$sc['title']?></a></li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>



        <?php if(!empty($listSnippets)): ?>

            <h3>Edit Snippet</h3>
            <ul>
                <?php foreach ($listSnippets as $sn):

                    $addClass = $addTitle = '';

                    if($sn['permission'] == 0) {
                        $addTitle .= 'Для всех. ';
                    }
                    else if($sn['permission'] == 1) {
                        $addTitle .= 'Только для зарегестрированых. ';
                    }
                    else if($sn['permission'] == 2) {
                        $addClass .= 'list_moder ';
                        $addTitle .= 'Только для модераторов. ';
                    }
                    else if($sn['permission'] == 3) {
                        $addClass .= 'list_admin ';
                        $addTitle .= 'Только для администраторов. ';
                    }

                    if($sn['enabled'] == 0) {
                        $addClass .= 'list_disabled ';
                        $addTitle .= 'Запись НЕ отображается. ';
                    }

                ?>
                    <li><a href="/settings/<?=$idCat?>/<?=$sn['idsubcategory']?>/<?=$sn['id']?>"
                           title="<?=$addTitle?>"
                           class="<?=$addClass?>" ><?=$sn['title']?></a></li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>

    </div>

<?php endif; ?>

