<?php

use components\HString;

/**
 * @var $data
 * @var $allSubcat
 */

/* -- rows
id
iduser
idsubcategory
metakey
metadesc
link
tags
vote
description
title
content
permission
ordering
enabled
created
*/


isset($data) OR $data = [];

isset($idCat) OR $idCat = false;
isset($idSubcat) OR $idSubcat = false;
isset($idSnipp) OR $idSnipp = false;

isset($allCat) OR $allCat = [];
isset($allSubcat) OR $allSubcat = [];

isset($linkView) OR $linkView = false;
isset($listSubcat) OR $listSubcat = [];


//var_dump($allSubcat, $listSubcat);
//var_dump($idSnipp, $listSubcat);
$currentSubcat = [];
array_walk($allSubcat, function($_scat) use ($idSubcat, &$currentSubcat){
    if($_scat['id'] === $idSubcat) $currentSubcat = $_scat;
});

?>


<div class="info_edit_panel tbl">
        <?php if($linkView):?>
            <div class="tbl_cell" style="width:180px">
                <a href="<?= $linkView?>" target="_blank" class="button">Просмотр страницы</a>
            </div>
        <?php endif; ?>
    <div class="tbl_cell">
        <?= ($idSnipp) ? 'Edit snippet ID: <span>'.$idSnipp.'</span>' : 'Create new snippet. Into SubCategory: <u>' . $currentSubcat['title'] . '</u> : ' .\components\HString::limitWords($currentSubcat['description'])?>
    </div>
</div>

<form name="snippForm" action="<?= $this->value('currentUrl')?>" method="post">

    <div class="full clear edit_form" style="padding-right: 10px">

        <p> <input name="title" type="text" value="<?=$data['title']?>" required placeholder="Заголовок сниппета" /></p>

        <div class="grid_5 first ">

            <input hidden="hidden" name="form_type" type="text" value="snippet" />
            <input hidden="hidden" name="id" type="text" value="<?=$data['id']?>" />
            <input hidden="hidden" name="vote" type="text" value="<?=$data['vote']?>" />


            <p> <input name="link" type="text" value="<?=$data['link']?>" required /> <span class="form_label">Link</span></p>


            <p> <input name="created" type="date" value="<?=date('Y-m-d', strtotime($data['created']))?>" /> <span class="form_label">Created</span></p>


            <p> <select name="ordering" id="" required >
                    <?php for($iterOrd=1; $iterOrd<100; $iterOrd++):?>
                        <option value="<?=$iterOrd?>" <?=($data['ordering']==$iterOrd)?"selected":""?> > Step <?=$iterOrd?> </option>
                    <?php endfor; ?>
                </select> <span class="form_label">Ordering</span></p>


            <p> <select name="permission" required >
                    <option value="0" <?= ($data['permission']==0)?"selected":""?> > Для всех </option>
                    <option value="1" <?= ($data['permission']==1)?"selected":""?> > Пользователей </option>
                    <option value="2" <?= ($data['permission']==2)?"selected":""?> > Модераторов </option>
                    <option value="3" <?= ($data['permission']==3)?"selected":""?> > Администраторов </option>
                </select> <span class="form_label">Permission</span></p>


            <p> <select name="enabled" required >
                    <option value="0" <?= ($data['enabled']==0)?"selected":""?> > Скрыто </option>
                    <option value="1" <?= ($data['enabled']==1)?"selected":""?> > Отображаеться </option>
                </select> <span class="form_label">Enabled</span></p>


            <p> <select name="category" required >
                    <?php foreach($allCat as $cat):?>
                        <option value="<?= $cat['id']?>" <?=($idCat==$cat['id'])?"selected":""?> > <?= $cat['title']?> </option>
                    <?php endforeach; ?>
                </select> <span class="form_label">Category</span></p>


            <p> <select name="idsubcategory" required >

                    <?php if($idSnipp && $listSubcat): ?>
                        <?php foreach($listSubcat as $scat):?>
                            <option value="<?=$scat['id']?>" <?=($data['idsubcategory']==$scat['id'])?"selected":""?> > <?=$scat['title']?> </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" selected >&nbsp;</option>
                        <?php foreach($listSubcat as $scat):?>
                            <option value="<?=$scat['id']?>" <?=($scat['id']===$idSubcat)?'selected':''?> > <?=$scat['title']?> </option>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </select> <span class="form_label">ID SubCategory</span></p>


        </div>

        <div class="grid_7">

            <p> <span class="form_label">Search Tags</span><br/>
                <input name="tags" type="text" value="<?=$data['tags']?>" placeholder=""/></p>

            <p> <span class="form_label">Meta Keywords</span><br/>
                <input name="metakey" type="text" value="<?=$data['metakey']?>" placeholder=""/></p>

            <p> <span class="form_label">Meta Description</span><br/>
                <textarea name="metadesc"><?=$data['metadesc']?></textarea></p>

        </div>

        <!-- TEXT AREA -->

        <div class="clear_line"></div>

        <p> <span class="form_label">Description</span><br/>
            <textarea name="description"><?=$data['description']?></textarea></p>

        <p> <span class="form_label">Content</span><br/>
            <textarea name="content"><?=$data['content']?></textarea></p>

        <input name="save" class="button" type="submit" value="Save snippet"/>
        <input name="delete" class="button" type="submit" value="Delete" onclick="return linkConfirm();"/>

    </div>

</form>

<script type="application/javascript">

    var subCat = {};
    // селект елементы категории и субкатегории
    var elemSelCat = document.querySelector('select[name=category]');
    var elemSubSelCat = document.querySelector('select[name=idsubcategory]');

    <?php
    // формирование необходимых данных списка всех субкатегорий
    $scSort = [];
    foreach($allSubcat as $sc) {
        $scSort[$sc['idcategory']][] = [
            'id'=>$sc['id'],
            'idcategory'=>$sc['idcategory'],
            'title'=>$sc['title'],
        ];
    }
    ?>
    // переобразование PHP массива а JS объект
    subCat = <?= json_encode($scSort) ?>;

    // по изминении категории вывод ее субкатегорий в селект елемент
    elemSelCat.addEventListener('change', function(e){
        var catID = this.value;
        var listSC = subCat[catID];
        var elemSelCatChange = document.createElement('select');
        elemSelCatChange.setAttribute('name','idsubcategory');
        elemSelCatChange.setAttribute('required','required');

        // если есть в категории субкатегории, то
        if(Array.isArray(listSC)){
            listSC.forEach(function (item) {
                var opt = document.createElement('option');
                opt.value = item['id'];
                opt.textContent = item['title'];
                elemSelCatChange.appendChild(opt);
            });
            // елемент по умолчанию
            var optEnd = document.createElement('option');
            optEnd.value = '';
            optEnd.textContent = '-';
            optEnd.setAttribute('selected','selected');
            elemSelCatChange.appendChild(optEnd);

            elemSubSelCat.innerHTML = elemSelCatChange.innerHTML;
        }
    },false);


    // Проверка заполнения полей
    var snippForm = document.querySelector('form[name=snippForm]');
    var fields = ['title','link','permission','idsubcategory'];

    snippForm.addEventListener('submit',function(e){
        for(var i=0; i<this.length; i++){
            var field = this[i];
            var fName = field.name;
            fields.forEach(function(item){
                if(fName.indexOf(item) > -1){
                    if(field.value === '' || field.value === undefined){
                        e.preventDefault();
                        field.style.border = '1px solid #FF0000';
                    }
                }
            });
        }
    },false);

</script>