<?php

namespace app;


class Controller {

    /**
     * @var \Slim\View|Layout
     */
    public $view;
    public $slim;

    public function __construct()
    {
        Core::addProvider('controller',$this);
        $this->view = Core::View();
        $this->slim = Core::Slim();
        $this->init();
    }

    /**
     * Инициализация
     */
    public function init(){ }


    /**
     * Вызов из параметров роутера. параметры роутера указывают дейстрия вызова
     */
    public static function call(){
        if($args = func_get_args()){
            $class = get_called_class();
            $method = array_shift($args);
            call_user_func_array([new $class(),$method],(array)$args);
        }
    }



    /**
     * Метод контолера замена для $this->view->renderLayout().
     * Отображает шаблон layout, переданые аргументы ($view, $data) помещаються в основной output
     * Отображение переданых данных происходит при вызове метода в нутри шаблона layout
     * методом Layout::output() без параметров - это указывает на основную позицию.
     *
     * @param  $view
     * @param  null $data
     * @param  bool $returned
     * @return bool|string
     */
    public function render($view, $data = null, $returned = false)
    {
        $this->view->addOutput('default_content', $this->view->render($view, $data, false));

        if($returned === true)
            return $this->view->render($this->view->layout());
        else
            $this->view->display($this->view->layout());

        return true;
    }


    /**
     * Возвращает или отображает переданый $view, без передачи глобальных данных
     *
     * @param $view
     * @param null $data
     * @param bool $returned
     * @return bool|string
     */
    public function partial($view, $data = null, $returned = true)
    {
        $partialData = $this->view->render($view, $data, false);

        if($returned)
            return $partialData;
        else
            echo $partialData;

        return true;
    }


    /**
     * Приямое отображенее вида с глобальнвми даными
     *
     * @param $view
     * @param null $data
     * @return bool
     */
    public function display($view, $data = null)
    {
        $partialData = $this->view->render($view, $data);
        echo $partialData;
        return true;
    }

    /**
     * Redirect
     *
     * @param $url
     * @param int $status
     */
    public function redirect($url, $status = 302)
    {
        Core::Slim()->redirect($url, $status);
    }

    /**
     * RedirectTo
     *
     * Redirects to a specific named route
     *
     * @param string    $route      The route name
     * @param array     $params     Associative array of URL parameters and replacement values
     * @param int $status
     */
    public function redirectTo($route, $params = array(), $status = 302)
    {
        Core::Slim()->redirectTo($route, $params, $status);
    }



    /**
     * Error 404
     */
    public function notFound()
    {
        Core::Slim()->notFound();
    }

}

