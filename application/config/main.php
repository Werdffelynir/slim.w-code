<?php

\app\Core::param(
    [
        # Базовые Параметры

        # Язык по умолчанию
        'language' => 'ru',

        # Включает мультиязычность параметры: false | ['en','ru','ua']
        'languages' => ['en','ru','ua'],

        # Параметры фреймворка
        'slim'=>[
            'debug' => true,
            'mode' => 'development',
            'templates.path' => \app\Core::root().'/views',
        ],

        # Параметры шаблона и видов
        'view'=>[
            # Текст елемента title на страницу по умолчанию
            'title' => 'App Title',
            # Файд шаблона (без расширения, физически template.php если парам. expansion=php)
            'layout' => 'layout/template',
            # Расширение фалов вида, шаблона
            'expansion' => 'php',
            # Кодировка по умолчанию
            'charset' => 'UTF-8',
        ],

        # Параметры подключения баз данных
        'dbConnection' =>
            [
                'db' =>
                    [
                        'dsn' => 'sqlite:'.\app\Core::root().'/database/db.sqlite',
                    ],
                'mysql' =>
                    [
                        'dsn' => 'mysql:host=localhost;dbname=db_dream',
                        'username' => 'root',
                        'passwd' => '',
                        'options' => [],
                    ],
            ],


    ]
);