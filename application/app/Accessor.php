<?php

namespace app;


class Accessor {

    /**
     * accessor for $_COOKIE when fetching values, or maps directly
     * to setcookie() when setting values.
     * bool setcookie (
     *      string $name
     *      [, string $value
     *      [, int $expire = 0
     *      [, string $path
     *      [, string $domain
     *      [, bool $secure = false
     *      [, bool $httponly = false
     * ]]]]]] )
     *
     * @param $name
     * @param null $value
     * @return mixed|null
     */
    public static function cookies($name, $value=null)
    {
        $argsNum = func_num_args();
        $argsValues = func_get_args();

        if ($argsNum == 1)
            return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;

        return call_user_func_array('setcookie', $argsValues);
    }

    /**
     * @param null $name
     * @param null $default
     * @return mixed
     */
    public static function getCookies($name=null, $default = null) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    /**
     * accessor for $_SESSION
     *
     * @param $name
     * @param null $value
     * @return null
     */
    public static function session($name, $value = null) {

        if(!isset($_SESSION)) {
            // ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
            session_save_path($_SERVER['DOCUMENT_ROOT'] . '/session');
            session_start();
        }

        # session var set
        if (func_num_args() == 2)
            return ($_SESSION[$name] = $value);

        # session var get
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * @param null $name
     * @param null $default
     * @return mixed
     */
    public static function getSession($name=null, $default = null) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    /**
     * @param null $name
     * @param mixed $default Default return value when key does not exist
     * @return mixed
     */
    public static function post($name=null, $default = null) {
        if($name==null)
            return (empty($_POST)) ? ($default===null)?false:$default : $_POST;
        else
            return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    /**
     * @param null $name
     * @param mixed $default Default return value when key does not exist
     * @return mixed
     */
    public static function get($name=null, $default = null) {
        if($name==null)
            return (empty($_GET)) ? ($default===null)?false:$default : $_GET;
        else
            return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

}