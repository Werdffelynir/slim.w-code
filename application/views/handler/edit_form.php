<?php
/**
 * @var $data
 * @var $dataSubcat
 * @var $dataCat
 */

?>

<div class="full clear">

    <div class="grid_2 first">
        <p>Category </p>
        <?php foreach ($dataCat as $cat): ?>

        <div onclick="getAllSubCat(this);" class="select_category btn_lite_dashed" data-cat="<?=$cat['id']?>"><?=$cat['title']?></div>

        <?php echo "\n"; endforeach; ?>
    </div>

    <div class="grid_3">
        <p>Subcategory </p>
        <div class="list_subcat_menu">
        <?php foreach ($dataSubcat as $scat): ?>

            <div onclick="getCurrentCat(this);" class="select_subcategory btn_lite_dashed" data-subcat="<?=$scat['id']?>" data-cat="<?=$scat['idcategory']?>"><?=$scat['title']?></div>

        <?php echo "\n"; endforeach; ?>
        </div>
    </div>

    <div class="grid_7 clear edit_form">

        <form action="/edit" method="post">

            <div class="info_edit_panel full clear">
                Hidden columns (
                vote: <strong><?=$data['vote']?></strong>
                iduser: <strong><?=$data['iduser']?></strong> )
                <?php
                if(!empty($errorMessage)): ?>
                    <div class="error_message">ERRORS:<br>

                    <?php foreach($errorMessage as $error):?>
                        <p><?=$error?></p>
                    <?php  endforeach; ?>

                    </div>
                <?php endif; ?>
            </div>

<!-- INPUTS -->

            <div class="full edit_inputs">

                <div class="grid_5 first">

                    <input hidden="hidden" name="id" type="text" value="<?=$data['id']?>" />
                    <input hidden="hidden" name="iduser" type="text" value="<?=$data['iduser']?>" />
                    <input hidden="hidden" name="ordering" type="text" value="<?=$data['ordering']?>" />
                    <input hidden="hidden" name="permission" type="text" value="<?=$data['permission']?>" />
                    <input hidden="hidden" name="idsubcategory" type="text" value="<?=$data['idsubcategory']?>" />


                    <p> <input name="title" type="text" value="<?=$data['title']?>" placeholder=""/> Title</p>
                    <p> <input name="link" type="text" value="<?=$data['link']?>" placeholder=""/> Link</p>
                    <p> <input name="tags" type="text" value="<?=$data['tags']?>" placeholder=""/> Tags</p>
                    <p> <input name="created" type="date" value="<?=date('Y-m-d', strtotime($data['created']))?>" placeholder=""/> Created</p>


                    <p> <input name="" type="range" min="0" max="99" step="1" value="<?=$data['ordering']?>"
                               onchange="setRange(this,'.vis_ordering','input[name=ordering]');" />
                        <span class="range_after_text">
                            Ordering <strong class="vis_ordering"><?=$data['ordering']?></strong> </span></p>


                    <p> <select name="permission" id="" required >
                            <option value="0" <?= ($data['permission']==0)?"selected":""?> > Для всех </option>
                            <option value="1" <?= ($data['permission']==1)?"selected":""?> > Пользователей </option>
                            <option value="2" <?= ($data['permission']==2)?"selected":""?> > Модераторов </option>
                            <option value="3" <?= ($data['permission']==3)?"selected":""?> > Администраторов </option>
                        </select> Permission</p>

                    <p>
                        <select name="enabled" id="" required >
                            <option value="0" <?= ($data['enabled']==0)?"selected":""?> > Скрыто </option>
                            <option value="1" <?= ($data['enabled']==1)?"selected":""?> > Отображаеться </option>
                        </select> Enabled</p>


                </div>

                <div class="grid_7 ">


                    <p> Category: <strong class="vis_idcategory"></strong> </p>
                    <p> Subcategory: <strong class="vis_idsubcategory"></strong></p>

                    <p> meta-keywords<br/>
                        <input name="meta_keywords" type="text" value="" style="width:100%"/></p>

                    <p>meta-description<br/>
                        <textarea name="meta_description"></textarea></p>

                </div>

            </div>

<!-- TEXT AREA -->

            <div class="clear_line"></div>

            <p>Snippet description<br/>
                <textarea name="description"><?=$data['description']?></textarea></p>
            <p>Snippet content<br/>
                <textarea name="content"><?=$data['content']?></textarea></p>
            <input class="button" type="submit" value="Save snippet"/>

        </form>
    </div>
</div>

<script>
    var catData = JSON.parse('<?=json_encode($dataCat)?>');
    var subCatData = JSON.parse('<?=json_encode($dataSubcat)?>');
    var currentCat = {
        id:'',
        title:''
    };
    var currentSubCat = {
        id:'',
        idcategory:'',
        title:''
    };

    var hasSubCat = '<?=$data['idsubcategory']?>';
    if(hasSubCat >= 1){
        currentSubCat = getOneObject(subCatData, parseInt(hasSubCat), 'id')[0];
        currentCat = getOneObject(catData, currentSubCat.idcategory, 'id')[0];
        var currentCatElem = document.querySelectorAll('.select_category');
        for(var key_elem in currentCatElem){
            if(key_elem >= 0){
                if(currentCatElem[key_elem].getAttribute('data-cat') == currentSubCat.idcategory) {
                    getAllSubCat(currentCatElem[key_elem]);
                    continue;
                }
            }
        }
        setChecked();
        setColorCat(currentSubCat.idcategory);
        setColorSubCat();
    }

    function getAllSubCat(elem) {
        var idCat = elem.getAttribute('data-cat');
        var subCat = getOneObject(subCatData, idCat, 'idcategory');

        var appendHtml = '';
        for(var i=0; i<subCat.length; i++){
            appendHtml += '<div onclick="getCurrentCat(this)" ';
            appendHtml += 'class="select_subcategory btn_lite_dashed" ';
            appendHtml += 'data-subcat="'+subCat[i]['id']+'" ';
            appendHtml += ' data-cat="'+subCat[i]['idcategory']+'"> ';
            appendHtml += subCat[i]['title']+'</div> ';
        }
        var list_subcat_menu = document.querySelector('.list_subcat_menu');
        list_subcat_menu.innerHTML = appendHtml;
        setColorCat(idCat);
        setColorSubCat();
    }
    function getCurrentCat(elem) {
        var idCat = elem.getAttribute('data-cat');
        var idSubCat = elem.getAttribute('data-subcat');
        currentCat = getOneObject(catData, idCat, 'id')[0];
        currentSubCat = getOneObject(subCatData, idSubCat, 'id')[0];
        setChecked();
        setColorSubCat();
    }

    function setChecked() {
        document.querySelector('input[name=idsubcategory]').value = currentSubCat.id;
        document.querySelector('.vis_idcategory').innerHTML = currentSubCat.title;
        document.querySelector('.vis_idsubcategory').innerHTML = currentCat.title;
    }
    function setColorCat(id) {
        var listCat = document.querySelectorAll('.select_category');
        for(var iCc=0; iCc<listCat.length; iCc++) {
            var curCat = listCat[iCc].getAttribute('data-cat');
            if(curCat == id) listCat[iCc].style.backgroundColor = '#989BA0';
            else listCat[iCc].style.backgroundColor = '#EAEEF6';
        }
    }
    function setColorSubCat() {
        var listSubCat = document.querySelectorAll('.select_subcategory');
        for(var iCsc=0; iCsc<listSubCat.length; iCsc++){
            var curSubCat = listSubCat[iCsc].getAttribute('data-subcat');
            if(curSubCat == currentSubCat.id) listSubCat[iCsc].style.backgroundColor = '#989BA0';
            else listSubCat[iCsc].style.backgroundColor = '#EAEEF6';
        }
    }
    function getOneObject(obj, id, key) {
        var info = [];
        for(var _itr in obj) {
            if(obj[_itr][key] == id)
                info.push(obj[_itr]);
        }
        return info;
    }
    function setRange(obj, selector, inp) {
        document.querySelector(selector).innerHTML = document.querySelector(inp).value = obj.value;
    }
</script>