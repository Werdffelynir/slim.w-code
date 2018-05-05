<?php
/**
 * @var $data
 */
isset($data) OR $data = [];

foreach ($data as $cat)
    echo "<a href=\"/{$cat['link']}\">{$cat['title']}</a>";
