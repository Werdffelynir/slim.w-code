<?php
/**
 * @var $column
 * @var $content
 */

isset($column) OR $column = '';
isset($content) OR $content = '';
?>


<div class="content_half">

    <div class="column grid_3 first">
        <?= $column?>
    </div>

    <div class="content_main grid_9">
        <?= $content?>
    </div>

</div>
