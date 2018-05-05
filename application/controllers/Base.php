<?php

namespace controllers;


use app\Accessor;
use app\Controller;
use app\Core;
use models\Blocked;
use models\Category;
use models\Users;
use models\Visits;

class Base extends Controller
{
    /** @var bool|array  */
    public $authData = false;
    /** @var bool  */
    public static $auth = false;
    /** @var bool  */
    public static $visit = false;
    /** @var bool  */
    public static $blocked = false;
    /** @var null|array  */
    public static $dataCategory;


    public function init()
    {
        $this->checkVisit();
        $this->isBlocked();
        $this->isAuth();

        self::initCommonData();
        $this->initCommonOutputs();
    }


    /**
     * Загрузка в переменные общих данных
     */
    public static function initCommonData()
    {
        $self = Core::controller();

        if($self->isAuth()) $permission = $self->authData['permission'];
        else
            $permission = 0;

        if(self::$dataCategory == null)
            self::$dataCategory = Category::model()->getAll("permission <= ? AND enabled = ? ORDER BY ordering", [$permission, 1]);
    }


    /**
     * Установка позиций
     */
    public function initCommonOutputs(){

        $menu_cat = $this->partial('outputs/menu_category', [
            'data'=>self::$dataCategory
        ]);

        $this->view->addOutput('navigate',$menu_cat);
    }


    # - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -


    /**
     * @param $data
     */
    public function metaTitle($data){
        $this->view->addData('title',$data);
    }

    /**
     * @param $data
     */
    public function metaKeyword($data){
        $this->view->addData('keywords',$data);
    }

    /**
     * @param $data
     */
    public function metaDescription($data){
        $this->view->addData('description',$data);
    }


    # - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -

    /**
     * @param $data
     */
    public function lookedAdd($data){
        $looked = $this->lookedGet();

        if(!in_array($data,$looked)){
            array_push($looked,$data);
            Accessor::cookies('looked', serialize($looked), time()+3600*24*30, '/');
        }
    }


    public function lookedGet(){
        if($looked = Accessor::getCookies('looked',false) AND !empty($looked)) {
            $looked = unserialize($looked);
        } else
            $looked = [];

        if(count($looked) > 40)
            array_shift($looked);

        return $looked;
    }

    # - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -

    /**
     * Проверка авторизации
     * @return bool
     */
    public function isAuth()
    {
        if(!empty($this->authData))
            return true;

        $data = $this->slim->getCookie('auth');

        if($cookData = unserialize($data) AND is_array($cookData))
        {
            self::$auth = $cookData['id'];
            $userData = Users::model()->getOne("id=".$cookData['id']);

            if($userData == false)
                return false;

            $this->authData = $userData;
            $this->view->addData('auth', self::$auth);
            $this->view->addData('isAdmin', (int) $userData['permission'] === 3);

            return true;
        }
        return false;
    }


    # - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -  - -
    # Visits

    public function checkVisit(){

        $ses = Accessor::session('visit');

        if(!$ses && !self::$visit || Blocked::isBlockedAgent()) {

            Accessor::session('visit', 'checked');
            $modelVisits = new Visits();

            $data = [
                'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'undefined',
                'lang' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'undefined',
                'agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined',
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'undefined',
                'url' => 'http://' . Core::Request()->getHost() . Core::urlCurrent(),
                'lastvisit' => time(),
            ];

            if($visit = $modelVisits->getVisit($data['ip'])) {
                self::$blocked = (bool) $visit['blocked'];
                $modelVisits->updVisit($data);
            }
            else
                $modelVisits->insVisit($data);

            self::$visit = $modelVisits->countAdd();
        }
        else if(!self::$visit) {

            $modelVisits = new Visits();
            self::$visit = $modelVisits->count()['count'];
            self::$blocked = $modelVisits->getVisit($_SERVER['REMOTE_ADDR'])['blocked'];
        }
    }



    /**
     * @param
     */
    public function isBlocked(){
        if(self::$blocked){
            $this->partial('block', null, false);
            exit;
        }
    }

}