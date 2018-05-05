<?php

namespace models;


use app\Core;
use app\Model;

class Blocked
{

    static public $agentsBots = [
        'bingbot',
        'YandexBot',
        'Go 1.1 package',
        'Googlebot',
        'MJ12bot',
        'AhrefsBot',
        'Baid',
        'baidu',
        'Baiduspider',
        'DotBot',
        'Disqus',
    ];

    static public function isBlockedAgent(){
        return self::agentsBotsFilter(Core::Request()->getUserAgent());
    }


    static public function agentsBotsFilter($userAgent){
        $count = count(self::$agentsBots);
        $block = false;
        for($i=0; $i < $count; $i++){
            if(stripos($userAgent, self::$agentsBots[$i]) !== false){
                $block = true;
                continue;
            }
        }
        return $block;
    }

}