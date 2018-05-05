<?php

/**
 * @var $data
 * @var $info
 * @var Slim\View|\app\Layout $this
 */

isset($data) OR $data = false;
isset($info) OR $info = false;

//$sep = ' &#10095; ';
$sep = ' &gt; ';

$title = ' ';

if($info['category'])
{
    $c_link = $info['category']['link'];
    $c_title = $info['category']['title'];
    $title .= "<a href=\"/{$c_link}\">{$c_title}</a>";

    if($info['subcategory']){
        $sc_link = $info['subcategory']['link'];
        $sc_title = $info['subcategory']['title'];
        $title .= $sep."<a href=\"/{$c_link}/{$sc_link}\">{$sc_title}</a>";

        if($info['snippets']){
            $s_link = $info['snippets']['link'];
            $s_title = $info['snippets']['title'];
            $title .= $sep."<a href=\"/{$c_link}/{$sc_link}/{$s_link}\">{$s_title}</a>";
        }
    }
}
?>


<div class="cat_title">
    <?=$title?>
</div>

<?php if($data == false): ?>

    No Information

<?php else: ?>

    <div class="cat_content">
        <?=$data?>
    </div>

<?php endif; ?>

