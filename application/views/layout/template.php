<?php

use app\Layout;
/**
 * @var $url
 * @var $title
 */

!isset($isAdmin) AND $isAdmin = false;
!isset($auth) AND $auth = false;
isset($title) OR $title = '';
isset($description) OR $description = '';
isset($keywords) OR $keywords = '';

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=$title?></title>
    <meta name="description" content="<?=$description?>" />
    <meta name="keywords" content="<?=$keywords?>" />
    <meta name="copyright" content="&copy; 2015 w-code.ru">
    <meta name="author" content="OL Werdffelynir" />
    <link rel="shortcut icon" href="/favicon.ico" />

    <link rel="stylesheet" href="/style/grid.css"/>
    <link rel="stylesheet" href="/style/main.css"/>

<!--   solarized_dark.css color-brewer.css idea.css   -->
    <link rel="stylesheet" href="/js/styles/idea.css">
    <script src="/js/highlight.pack.js"></script>

    <script>hljs.initHighlightingOnLoad();</script>

    <script type="text/javascript" src="/js/main.js"></script>

	<!-- to test 
	<script src="http://62.149.13.59:81/widget.js?v=1&u=10032E3X3TZ6BZVXJ"></script>-->
	
</head>
<body>

    <div class="page">
<!--        grad_dark-->
        <div class="header full clear">

            <div class="tbl">
                <div class="tbl_cell">
                    <a class="logo" href="/"><span>{Snippets}</span> Web-Code</a>
                </div>
                <div class="tbl_cell">
                    <img src="/images/search-icon.png" alt=""/>
                </div>
                <div class="tbl_cell search">
                    <form action="/search" method="get">
                        <input name="search" type="text" placeholder="Поиск..."/> <input type="submit" value="Search"/>
                    </form>
                </div>
                <div class="tbl_cell top_menu">
                    <a href="http://grid.w-code.ru/" target="_blank" class="btn-service" >Grid CSS Generator</a>
                    <a href="http://runjs.w-code.ru/" target="_blank" class="btn-service" >Run JS</a>

                    <?php if($auth):?>
                        <a href="/profile">Profile</a>
                        <a href="/logout">Out</a>
                    <?php else:?>
                        <a href="/register">Register</a>
                        <a href="/login">Auth</a>
                    <?php endif;?>
                </div>
            </div>

        </div>

            <div class="navigate_top">
                <?php if($auth && $isAdmin):?>
                <a href="/visits">Visits</a>
                <a href="/settings/1/23/new">New snippet</a>
                <a href="/settings">Records</a>
                <?php endif;?>
            </div>

        <div class="navigate">
            <?php Layout::output('navigate'); ?>
        </div>

        <div class="content full clear">
            <?php Layout::output(); ?>
        </div>

        <div class="footer clear">
            Copyright © - 2015 SunLight system, OL Werdffelynir. All rights reserved. <br>
            Was compiled per: <?php echo round(microtime(true) - START_TIMER, 4); ?> sec. Visits <?=\controllers\Base::$visit?>.
        </div>

    </div>

</body>
</html>
