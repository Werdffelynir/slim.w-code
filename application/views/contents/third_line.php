<?php
/**
 * @var $columnOne
 * @var $columnTwo
 * @var $columnThree
 */

isset($columnOne) OR $columnOne = '';
isset($columnTwo) OR $columnTwo = '';
isset($columnThree) OR $columnThree = '';
?>


<div class="content_third clear">

    <div class="column grid_4 first">
        <?= $columnOne?>
    </div>

    <div class="column grid_4">
        <?= $columnTwo?>
    </div>

    <div class="column grid_4">
        <?= $columnThree?>
    </div>

</div>

