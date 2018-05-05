<?php

if(isset($data['geo']) && isset($data['prv'])){
/**/
    $country = isset($data['geo']['country']) ? $data['geo']['country'] : ' ? ';
    $region = isset($data['geo']['region']) ? $data['geo']['region'] : ' ? ';
    $city = isset($data['geo']['city']) ? $data['geo']['city'] : ' ? ';
    $name_ripe = isset($data['prv']['name_ripe']) ?$data['prv']['name_ripe'] : ' ? ';
    $site = isset($data['prv']['site']) ?$data['prv']['site'] : ' ? ';
    $route = isset($data['prv']['route']) ?$data['prv']['route'] : ' ? ';

    echo "<p> Страны : ".addslashes($country)."</p>
   <p>Регион : ".addslashes($region)."</p>
   <p>Город : ".addslashes($city)."</p>
   <p>Провайдер : ".addslashes($name_ripe)."</p>
   <p>Сайт провайдера : ".addslashes($site)."</p>
   <p>Сеть провайдера : ".addslashes($route)."</p>";
}

