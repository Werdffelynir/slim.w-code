<?php

/**
 * @var $data
 * @var $idCat
 * @var $idSubcat
 */

/* -- rows
id
idcategory
metakey
metadesc
link
title
ordering
enabled
permission
created
*/

isset($data) OR $data = [];
isset($allCat) OR $allCat = [];
isset($idCat) OR $idCat = false;
isset($idSubcat) OR $idSubcat = false;

$currentCat = [];
array_walk($allCat, function($_cat) use ($idCat, &$currentCat){
    if($_cat['id'] === $idCat) $currentCat = $_cat;
});

?>

<div class="info_edit_panel">
    <?= ($idSubcat) ? 'Edit subCategory ID: '.$idSubcat : 'Create new subCategory. Into Category: <u>' . $currentCat['title'] . '</u> : ' . \components\HString::limitWords($currentCat['description'])?>
</div>

<form action="<?= $this->value('currentUrl')?>" method="post">

    <div class="full clear edit_form" style="padding-right: 10px">

        <p> <input name="title" type="text" value="<?=$data['title']?>" required placeholder="Заголовок суб-категории" /> </p>

        <div class="grid_5 first ">

            <input hidden="hidden" name="form_type" type="text" value="subcategory" />
            <input hidden="hidden" name="id" type="text" value="<?=$data['id']?>" />



            <p> <input name="link" type="text" value="<?=$data['link']?>" required /> <span class="form_label">Link</span></p>


            <p> <input name="created"type="date" value="<?=date('Y-m-d', strtotime($data['created']))?>" /> <span class="form_label">Created</span></p>

            <p> <select name="ordering" id="" required >
                    <?php for($iterOrd=1; $iterOrd<100; $iterOrd++):?>
                        <option value="<?=$iterOrd?>" <?=($data['ordering']==$iterOrd)?"selected":""?> > Step <?=$iterOrd?> </option>
                    <?php endfor; ?>
                </select> <span class="form_label">Ordering</span></p>

            <p> <select name="permission" id="" required >
                    <option value="0" <?= ($data['permission']==0)?"selected":""?> > Для всех </option>
                    <option value="1" <?= ($data['permission']==1)?"selected":""?> > Пользователей </option>
                    <option value="2" <?= ($data['permission']==2)?"selected":""?> > Модераторов </option>
                    <option value="3" <?= ($data['permission']==3)?"selected":""?> > Администраторов </option>
                </select> <span class="form_label">Permission</span></p>


            <p>
                <select name="enabled" id="" required >
                    <option value="0" <?= ($data['enabled']==0)?"selected":""?> > Скрыто </option>
                    <option value="1" <?= ($data['enabled']==1)?"selected":""?> > Отображаеться </option>
                </select> <span class="form_label">Enabled</span></p>

            <p>
            <select name="idcategory" id="" required >

                <?php if($idSubcat): ?>
                    <?php foreach($allCat as $cat):?>
                        <option value="<?=$cat['id']?>" <?= ($data['idcategory']==$cat['id'])?"selected":""?> > <?=$cat['title']?> </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" selected >&nbsp;</option>
                    <?php foreach($allCat as $cat):?>
                        <option value="<?=$cat['id']?>" <?=($cat['id']===$idCat)?'selected':''?> > <?=$cat['title']?> </option>
                    <?php endforeach; ?>
                <?php endif; ?>

            </select> <span class="form_label">ID SubCategory</span></p>


        </div>

        <div class="grid_7">

            <p> <span class="form_label">Meta Keywords</span><br/>
                <input name="metakey" type="text" value="<?=$data['metakey']?>" placeholder=""/></p>

            <p><span class="form_label">Meta Description</span><br/>
                <textarea name="metadesc"><?=$data['metadesc']?></textarea></p>

        </div>

        <!-- TEXT AREA -->

        <div class="clear_line"></div>

        <p> <span class="form_label">Description</span><br/>
            <textarea name="description"><?=$data['description']?></textarea></p>

        <input name="save" class="button" type="submit" value="Save sub-category"/>
        <input name="delete" class="button" type="submit" value="Delete" onclick="return linkConfirm();"/>

    </div>

</form>
