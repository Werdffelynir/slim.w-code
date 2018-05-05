<?php

/**
 * @var $data
 */

isset($data) OR $data = '';
isset($info) OR $info = '';
?>



<ul class="sub_menu">

    <?php foreach ($data as $list): ?>

        <li><a href="/<?=$list['cat_link']."/".$list['subcat_link'];?>"><?=$list['subcat_title']?></a></li>

    <?php endforeach; ?>

</ul>