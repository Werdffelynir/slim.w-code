<?php

function _autoloader($className) {
    if(strpos($className,'Slim') === false) {
        $filePath = getClassPath($className);
        if (is_file($filePath))
            require_once ($filePath);
        else
            throw new \RuntimeException("Class: $className PATH: $filePath not exists");
    }
}

function getClassPath($className) {
    $root = dirname(__DIR__);
    $filePath = $root.'/'.str_replace('\\', '/', $className).'.php';
    return $filePath;
}

spl_autoload_register('_autoloader');