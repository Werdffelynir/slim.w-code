<?php

namespace app;


class Core {

    /** @var null string */
    public static $root = null;
    /** @var null|\Slim\Slim */
    private static $slim = null;
    /** @var null|self  */
    private static $instance = null;
    /** @var null|array  */
    private static $params = null;
    /** @var null|mixed  */
    private static $dbConnections = null;
    /** @var null|array  */
    private static $dbConnectionSettings = null;

    /** hidden constructor */
    private function __construct(){}

    /**
     * @param \Slim\Slim        $slim
     * @return null|\Slim\Slim
     */
    public static function register($slim){
        self::root();

        $slim->config((array) self::param('slim'));

        self::$slim = $slim;
        self::$dbConnectionSettings = self::param('dbConnection');
        self::param(['dbConnection'=>null]);

        self::param([
            'root'      => self::root(),
            'url'       => self::url(),
            'urlBase'   => self::urlBase(),
            'urlCurrent'=> self::urlCurrent(),
        ]);

        $slim->view(new Layout());

        return self::$slim;
    }

    /**
     * @return Core|null
     */
    public static function self() {
        self::Slim();
        if(self::$instance == null){
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @return null|\Slim\Slim
     */
    public static function Slim() {
        if(self::$slim == null) throw new \RuntimeException('Slim not register! in class \app\Core::register()');
        return self::$slim;
    }

    /**
     * @return \Slim\View|Layout
     */
    public static function View() {
        return self::$slim->view();
    }

    /**
     * @return \Slim\Http\Request
     */
    public static function Request() {
        return self::$slim->request();
    }

    /**
     * @return \Slim\Http\Response
     */
    public static function Response() {
        return self::$slim->response();
    }

    /**
     * @return \Slim\Environment
     */
    public static function Environment() {
        return self::$slim->environment();
    }

    /**
     * Get or Set config params
     * @param null $data
     * @return array|bool|null
     */
    public static function param($data = null) {
        if($data === null)
            return self::$params;
        elseif(is_string($data) && isset(self::$params[$data]))
            return self::$params[$data];
        elseif(is_array($data)){
            self::$params = array_merge((array)self::$params,$data);
            return true;
        }
        return false;
    }


    /**
     * Root app full path
     * @return null|string
     */
    public static function root(){
        if(self::$root == null)
            self::$root = dirname(__DIR__);
        return self::$root;
    }


    /**
     * Full url
     * @return string
     */
    public static function url(){
        return self::$slim->request()->getUrl();
    }

    /**
     * Base url
     * @return string
     */
    public static function urlCurrent(){
        return self::$slim->request()->getResourceUri();
    }

    public static function urlBase(){
        return self::$slim->request()->getRootUri();
    }
    public static function urlDomain(){
        return self::$slim->request()->getHost();
    }
    /**
     * @param null $name
     * @return bool|\app\db\SPDO
     */
    public static function db($name = null)
    {

        if(empty(self::$dbConnectionSettings))
            throw new \RuntimeException('Error. Cant find connection settings in file config/main.php');

        reset(self::$dbConnectionSettings);
        if($name == null)
            $name = key(self::$dbConnectionSettings);

        if(!empty(self::$dbConnections[$name])){
            $dbConnection = self::$dbConnections[$name];

        }else{

            if(isset(self::$dbConnectionSettings[$name]))
                $settings = self::$dbConnectionSettings[$name];

            if(empty($settings['dsn']))
                throw new \RuntimeException('Error. Cant find connection configure "dbConnection[ dsn=>.... ]" in file config/main.php');

            $dsn        = $settings['dsn'];
            $username   = isset($settings['username']) ? $settings['username'] : null;
            $passwd     = isset($settings['passwd']) ? $settings['passwd'] : null;
            $options    = isset($settings['options']) ? $settings['options'] : [];

            $dbConnection = new \app\db\SPDO($dsn, $username, $passwd, $options);

            if($dbConnection != null)
                self::$dbConnections[$name] = $dbConnection;
        }
        return $dbConnection;
    }


    /** @var array $providerReserved Зарезервированые ключи обекта провайдер Core::provider */
    private static $providerReserved = [];
    /** @var array $provider */
    private static $provider = [];


    /**
     * Установка базовых провайдеров. Также метод уничтожит все существующие данные в обекте Core::provider
     *
     * Методы Core::setProvider(), Core::addProvider(), Core::provider()
     * Предназначины для передачи неких данных между частями программы
     *
     * @param array $data
     * @return array
     */
    public static function setProvider(array $data){
        return self::$provider = $data;
    }

    /**
     * Подовляет данные $data в глобальный обект Core::provider по ключу $key
     *
     * Методы Core::setProvider(), Core::addProvider(), Core::provider()
     * Предназначины для передачи неких данных между частями программы
     *
     * @param string    $key    Ключ для доступа к данным
     * @param mixed     $data   Если устновлнно производин замену данных по клчю $key
     * @return null
     */
    public static function addProvider($key, $data = null)
    {
        if(is_string($key) && !in_array($key,self::$providerReserved)){
            self::$provider[$key] = $data;
            return true;
        }
        return false;
    }

    /**
     * Core::provider() Используеться для получения данных с глобального обекта Core::provider по ключу $key
     *
     * Методы Core::setProvider(), Core::addProvider(), Core::provider()
     * Предназначины для передачи неких данных между частями программы
     *
     * @param string    $key        Ключ доступа к данным
     * @return bool|mixed
     */
    public static function provider($key)
    {
        if(isset(self::$provider[(string)$key]))
            return self::$provider[(string)$key];

        return false;
    }


    /**
     * Возвращает обект текущего контролер
     * Испрозует Core::provider
     *
     * @return mixed|Controller
     */
    public static function controller() {
        return self::provider('controller');
    }


    /**
     * @param $file
     * @param array $params
     * @return string
     */
    public static function renderPHP($file, $params = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require($file);

        return ob_get_clean();
    }


    /**
     * @param $file
     * @param array $params
     */
    public static function includePHP($file, $params = [])
    {
        extract($params, EXTR_OVERWRITE);
        require($file);
    }

}