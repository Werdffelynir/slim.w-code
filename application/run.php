<?php

# incl slim framework
require 'framework/Slim/Slim.php';
require 'app/autoload.php';
require 'config/main.php';

\Slim\Slim::registerAutoloader();
\app\Core::register(new \Slim\Slim());
$slim = \app\Core::Slim();


# main page
$slim->get('/', [new controllers\Index(), 'index']);


# main page with error 404
$slim->notFound([new controllers\Index(), 'notFound']);


# Pages - login, logout, profile
$slim->map('/:action', ['controllers\Profile','call'])
    ->via('GET','POST')
    ->conditions([
        'action'=>'login|logout|register|profile'
    ]);


# Pages - services,
$slim->map('/service/:link', [new controllers\Service(), 'index'])
    ->via('GET','POST')
    ->conditions([
        'link'=> '[\w-]+'
    ]);


# Pages - dynamic [category / sub / snippet], settings, snippet, edit, create
$slim->map('/:action(/:one(/:two)(/:three))', [new controllers\Handler(), 'index'])
    ->via('GET','POST')
    ->conditions([
        'action'=> \controllers\Handler::registeredRegExp().'|snippet|blog|settings|create|search|selectvote|visits|visitsDetect',
        'one'=>'[\w-]+',
        'two'=>'[\w-]+',
        'three'=>'[\w-]+'
    ]);


$slim->run();