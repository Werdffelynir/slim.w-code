<?php
/**
 * @var $data
 * @var $one
 * @var $two
 */

isset($data) OR $data = false;
isset($info) OR $info = false;
isset($one) OR $one = false;
isset($two) OR $two = false;
isset($snippets) OR $snippets = [];
?>

<div class="half_content clear">

    <?php if($snippets && $info):?>

        <div class="hc_tree_info clear">
            <?php
                echo "<a href=\"/{$info['category_link']}\">{$info['category']}</a>";
            if ($one)
                echo " &#10149; <a href=\"/{$info['category_link']}/{$info['subcat_link']}\">{$info['subcat']}</a>";
            if ($two)
                echo " &#10149;  <a href=\"/{$info['category_link']}/{$info['subcat_link']}/{$info['snippet_link']}\">{$info['snippet']}</a>";
            ?>
        </div>

        <?php foreach ($snippets as $snippet) :?>
            <div class="hc_item">

                <div class="hc_title">
                    <a href="/<?=$snippet['cat_link']?>/<?=$snippet['subcat_link']?>/<?=$snippet['snip_link']?>"><?=$snippet['snip_title']?></a>
                </div>
                <div class="hc_tags">
                    <?=$snippet['snip_tags']?>
                </div>
                <div class="hc_content">
                    <?=$snippet['snip_content']?>
                </div>


                <div class="hc_footer full clear">
                    <div class="hc_trees grid_6 first">
                        <a href="/<?=$snippet['cat_link']?>"><?=$snippet['cat_title']?></a>
                        &#10149;
                        <a href="/<?=$snippet['cat_link']?>/<?=$snippet['subcat_link']?>"><?=$snippet['subcat_title']?></a>
                        &#10149;
                        <a href="/<?=$snippet['cat_link']?>/<?=$snippet['subcat_link']?>/<?=$snippet['snip_link']?>"><?=$snippet['snip_title']?></a>
                    </div>
                    <div class="hc_info grid_6 ">
                        <?=$snippet['users_name']?> | <?=$snippet['snip_created']?>
                    </div>
                </div>

            </div>
        <?php endforeach;?>

    <?php else :?>

        <div class="hc_title">hc_title</div>
        <div class="hc_content">hc_content</div>
        <div class="hc_footer">hc_footer</div>

    <?php endif;?>

</div>