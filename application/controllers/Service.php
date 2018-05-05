<?php


namespace controllers;


class Service extends Base
{

    public function init() {
        parent::init();

        //code
    }


    /**
     * 'grid|load|form-generator|html-builder'
     * @param null $link
     */
    public function index($link = null) {

        if(method_exists($this,$link)){
            $this->$link();
        }else{
            $this->notFound();
        }

    }


    public function grid() {

        $this->view->addData('title','CSS Grid Generator');

        $this->render('contents/full', [
            'title'=>'CSS Grid Generator',
            'content'=>'Grid Generator',
        ]);
    }

}