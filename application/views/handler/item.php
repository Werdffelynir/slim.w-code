<?php
/**
 * @var $data
 */

use \widgets\Vote;
use \widgets\EditPanel;
use \components\HString;
use \components\Parsedown;
?>

<? if(empty($data)):?>

    Not information!

<? else:?>

    <div class="item clear">

        <div class="item_title full clear">

            <div class="item_vote grid_2 first">
                <?php Vote::widget(['id'=>$data['s_id'],'vote'=>$data['s_vote']]); ?>
            </div>

            <div class="item_info grid_10">
                Update: <?= date('m.d.Y',strtotime($data['s_created']))?><br/>
                Record by: <?=$data['u_name']?><br/>
                Snippet Tags: <?= HString::explodeTags($data['s_tags']); ?>
            </div>

        </div>

        <div class="item_edit">
            <?php EditPanel::widget(['data'=>$data]); ?>
        </div>

        <div class="item_desc">

            <h1><?= $data['s_title']?></h1>

            <?= Parsedown::instance()->text(htmlspecialchars_decode($data['s_description']));?>
        </div>

        <div class="item_content">
            <?= Parsedown::instance()
                //->setMarkupEscaped(true)
                ->setBreaksEnabled(true)
                ->text(htmlspecialchars_decode($data['s_content']));
            ?>
        </div>

        <div class="comments">

            <div id="disqus_thread"></div>

        </div>

    </div>


<? endif;?>




