<?php

namespace controllers;

use models\Category;
use models\Snippets;
use models\Subcategory;

class Index extends Base
{

    public function init()
    {
        parent::init();
    }

    /**
     * Главная Страница
     */
    public function index()
    {
        $this->metaTitle("Web Code Snippets - База скриптов для программиста");
        $this->metaKeyword("web code, веб програмирование, php snippets, функции, javascript snippets, заметки php программиста, подсказки кода, actionscript, как php, обучение php js as, готовые решение программирования");
        $this->metaDescription("Блог php программиста, обучающие статьи и каталог сниппетов по web языкам программирования (PHP, JavaScript, HTML/CSS, SQLServer,ActionScript).
        На сайте программисты раскрывают многие вопросы и задачи по разнообразным задачам веб-технологий.");

        $lastSnippets = $this->lastSnippets();
        $lastLooked = $this->lastLooked();
        $mapSnippets = $this->mapSnippets();


        $content = $this->partial('contents/half_index',[
            'columnOne' => $lastSnippets.$lastLooked,
            'columnTwo' => $mapSnippets
        ]);

        $this->render('contents/full', [
            'content'=>$content,
        ]);
    }


    public function lastSnippets()
    {
        if($this->isAuth()) $permission = $this->authData['permission'];
        else
            $permission = 0;

        $snipp = new Snippets();
        $data = $snipp->lastSnippets($permission, 20); //$sm->getAll("permission <= {$permission} AND idsubcategory > 0 ORDER BY id DESC LIMIT 20");

        if($data)
            return $this->partial('index/snippets_list',['data'=>$data]);
        else
            return null;
    }

    public function mapSnippets()
    {
        if($this->isAuth()) $permission = $this->authData['permission'];
        else
            $permission = 0;

        $category = Category::model()->getAll("permission <= ? AND enabled = ? ORDER BY ordering", [$permission, 1]);
        $subcategory = Subcategory::model()->getAll("permission <= ? AND enabled = ? ORDER BY ordering", [$permission, 1]);

        return $this->partial('index/snippets_map',[
            'category' => $category,
            'subcategory' => $subcategory,
        ]);
    }


    public function lastLooked()
    {
        if($this->isAuth()) $permission = $this->authData['permission'];
        else
            $permission = 0;

        $data = null;
        $ids = $this->lookedGet();

        if(is_array($ids) && !empty($ids))
        {
            $idsIN = ' IN('.join(',',$ids).') ';
            $sm = new Snippets();
            $data = $sm->getAll("permission <= {$permission} AND idsubcategory > 0 AND id {$idsIN}");
        }

        return $this->partial('index/snippets_looked',['data'=>$data]);

    }

    public function topSnippets()
    {

    }


    public function userSnippets()
    {

    }


    public function lastBlog()
    {

    }


    public function lastComments()
    {

    }


    /**
     * Page 404
     */
    public function notFound()
    {
        $this->view->addData('title','ERROR 404');

        $this->render('contents/full', [
            'content'=> $this->partial('index/404'),
        ]);
    }

}
