<?php
/**
 *
 */

use \app\Core;
use \components\HString;

function ifUndefinedEmpty($para){
    if($para == 'undefined')
        return '';
    return $para;
}
?>

<table class="tbl_visits tbl_reset">
    <thead>
        <tr>
            <th>Time</th>
            <th>IP</th>
            <th>Entered</th>
            <th>Referrer</th>
            <th>Agent</th>
            <th>Lang</th>
            <th>Detection</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($data as $vis):?>

        <tr data-id="<?= $vis['id']?>" data-ip="<?= $vis['ip']?>" >
            <td>
                <?= date("m.d.y H:i",(int)$vis['lastvisit'])?>
            </td>
            <td>
                <?= ifUndefinedEmpty($vis['ip'])?>
            </td>
            <td data-tip="<?= $vis['url']?>" class="tipser">
                <?php if (!$vis['url'] || $vis['url'] != 'undefined' ): ?>
                    <a href="<?= Core::urlBase().$vis['url']?>">
                        <?= HString::limitChars($vis['url'], 18)?>
                    </a>
                <?php endif; ?>
            </td>
            <td data-tip="<?= $vis['referrer']?>" class="tipser">
                <?php if (!$vis['referrer'] || $vis['referrer'] != 'undefined' ): ?>
                    <a href="<?= $vis['referrer']?>">
                        <?= HString::limitChars($vis['referrer'], 18)?>
                    </a>
                <?php endif; ?>
            </td>
            <td data-tip="<?= $vis['agent']?>" class="tipser">
                <?= HString::limitChars(ifUndefinedEmpty($vis['agent']), 18)?>
            </td>
            <td data-tip="<?= $vis['lang']?>" class="tipser">
                <?= HString::limitChars(ifUndefinedEmpty($vis['lang']), 8)?>
            </td>

            <?php
            $detected = ['tip'=>'','country'=>'','region'=>'','city'=>''];
            if(!empty($vis['detected'])):
                $_detected = \widgets\Detected::widget(['data'=>$vis['detected']], true);
                if(is_array($_detected)) $detected = $_detected;

            endif;
            ?>
            <td data-tip="<?= htmlspecialchars($detected['tip'])?>" class="detected tipser">
                <?= !empty($detected['tip'])
                    ? ( $detected['country'] .'. '. $detected['region'] .', '. $detected['city'] )
                    : ''?>
            </td>

            <td>
                <div class="button" onclick="onDetect(this,'<?= $vis['ip']?>','<?= $vis['id']?>')">Detect</div>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>