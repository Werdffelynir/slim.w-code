<?php
/**
 * @var $content
 */
$title = (isset($title)) ? "<h2>$title</h2>" : '';
$content = (isset($content)) ? $content : '';

?>

<div class="content_base">
    <?= $title ?>
    <?= $content ?>
</div>
