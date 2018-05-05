<?php
/**
 * @var $vote
 * @var $id
 */

use \components\HString;


?>

<div class="vote_widget tbl">
    <div class="tbl_cell">
        <div class="vote_num"><?=HString::voteDisplay($vote)?></div>
    </div>
    <div class="tbl_cell">
        <div class="vote_btn vote_plu" data-id="<?= $id?>">&#9733;</div>
        <div class="vote_btn vote_min">&#9734;</div>
    </div>
</div>
