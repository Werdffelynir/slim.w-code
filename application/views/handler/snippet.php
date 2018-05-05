<?php
/**
 * @var $dataCat
 * @var $dataSubCat
 * @var $dataSnippet
 * @var $links
 */

use \widgets\Vote;
use \widgets\EditPanel;
use \components\HString;
use \components\Parsedown;

isset($dataCat) OR $dataSnip = false;
isset($dataSubCat) OR $dataSnip = false;
isset($dataSnippet) OR $dataSnip = false;
isset($dataUser) OR $dataUser = false;

?>

<div class="one_in_center">

<? if(empty($dataCat)):?>



<? else:?>

    <div class="breadcrumbs">
        <?= $links['category']; ?> &gt;<!--&#10095;-->
        <?= $links['subcategory']; ?> &gt;<!--&#10095;-->
        <?= $links['snippet']; ?>
    </div>

    <div class="item clear">

        <div class="item_title full clear">

            <div class="item_vote grid_2 first">
                <?php Vote::widget(['id'=>$dataSnippet['id'],'vote'=>$dataSnippet['vote']]); ?>
            </div>

            <div class="item_info grid_10">
                Update: <?= date('m.d.Y',strtotime($dataSnippet['created']))?><br/>
                Record by: <?= $dataUser['name']?><br/>
                Snippet Tags: <?= HString::explodeTags($dataSnippet['tags']); ?>
            </div>

        </div>

        <div class="item_edit">
            <?php EditPanel::widget([
                'cat'=>$dataCat['id'],
                'subcat'=>$dataSubCat['id'],
                'snippet'=>$dataSnippet['id'],
                'user'=>$dataUser['id']
            ]); ?>
        </div>

        <div class="item_desc">

            <h1><?= $dataSnippet['title']?></h1>

            <?= Parsedown::instance()
                ->text($dataSnippet['description']);
            ?>
        </div>

        <div class="item_content">
            <?= Parsedown::instance()
                //->setMarkupEscaped(true)
                ->setBreaksEnabled(true)
                ->text(htmlspecialchars_decode($dataSnippet['content']));
            ?>
        </div>


    </div>


<? endif;?>




</div>