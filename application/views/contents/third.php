<?php
/**
 * @var $columnOne
 * @var $columnTwo
 * @var $contentMain
 */

isset($columnOne) OR $columnOne = '';
isset($columnTwo) OR $columnTwo = '';
isset($content) OR $content = '';
?>


<div class="content_third clear">

    <div class="column grid_2 first">
        <?= $columnOne?>
    </div>

    <div class="column grid_2">
        <?= $columnTwo?>
    </div>

    <div class="content_main grid_8">
        <?= $content?>
    </div>

</div>

