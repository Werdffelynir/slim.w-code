<?php

/**
 * @var $data
 */

isset($data) OR $data = false;
?>


<?php if($data == false): ?>

    No Information

<?php else: ?>
    <ul class="sub_menu">

        <?php foreach ($data as $list): ?>
            <li><a href="/<?=$list['c_link']."/".$list['sc_link'];?>"><?=$list['sc_title']?></a></li>
        <?php endforeach; ?>

    </ul>
<?php endif; ?>

