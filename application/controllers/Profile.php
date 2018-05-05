<?php


namespace controllers;


use app\Core;
use models\Users;

class Profile extends Base
{


    public function init() {
        parent::init();

        //code
    }

    /**
     *
     */
    public function index(){}


    /**
     * Страница авотризации
     */
    public function login()
    {
        $message = '';

        if($login = Core::Request()->post('login'))
        {
            $password = Core::Request()->post('password');
            $remember = Core::Request()->post('remember');

            $check = $this->checkAuth($login,$password,$remember);

            if(!$check)
                $message = '<span style="color:#9A0906;">Error. Check your login or password.</span>';
            else
                $this->slim->redirect(Core::url());
        }

        $this->view->addData('title','Login');

        $this->render('profile/login', [
            'message'=>$message
        ]);
    }


    /**
     * Проверка авторизации
     * @param $login
     * @param $password
     * @param $remember
     * @return array|bool
     */
    private function checkAuth($login, $password, $remember)
    {
        $user = Users::model()->get(false, "login=? AND password=?", [$login,md5($password)]);

        if(isset($user['name']))
        {
            $time = null;
            if($remember != null) $time = time()+3600*24*30;
            $userData = [
                'id'=>$user['id'],
                'name'=>$user['name']
            ];
            $this->slim->setCookie('auth', serialize($userData), $time);

            return $userData;
        }else
            return false;
    }


    /**
     * Выход
     */
    public function logout()
    {
        $this->slim->deleteCookie('auth');
        $this->slim->redirect(Core::url());
    }


    /**
     * Профиль пользователя
     */
    public function profile(){

        $this->view->addData('title','Profile: '.$this->authData['name']);

        $this->render('contents/full', [
            'title'=>'Profile: '.$this->authData['name'],
            'content'=>'...',
        ]);
    }


    public function register()
    {
        $this->view->addData('title','Register on w-code.ru');

        $this->render('contents/full', [
            'title'=>'Profile: ',
            'content'=>'
            <div style="padding: 10px 0">На даный момент регистрация на сайте w-code.ru закрыта.</div>
            ',
        ]);
    }

}