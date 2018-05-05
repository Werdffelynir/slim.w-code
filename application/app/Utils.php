<?php

namespace app;


class Utils {

//    private static $url;
//
//    public static function setUrl($url){
//        self::$url = $url;
//    }





    /**
     * @param null $url
     * @param int $code
     * @param int $delay
     * @return bool
     */
    public static function redirect($url = null, $code = 302, $delay = 0)
    {
        $url = ($url==null) ? '/' : '/' . trim($url,'/');

        if ($delay) {
            header('Refresh: ' . $delay . '; url=' . $url, true);
        } else {
            header('Location: ' . $url, true, $code);
        }
        return true;
    }


    /**
     * prints out no-cache headers before dumping passed content
     *
     * @param null $content
     * @return bool
     */
    public static function nocache($content = null) {

        $stamp = gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']).' GMT';

        # dump no-cache headers
        header('Expires: Tue, 13 Mar 1979 18:00:00 GMT');
        header('Last-Modified: '.$stamp);
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        # if you have content, dump it
        return $content && strlen($content) && (print $content);
    }

    /**
     * maps directly to json_encode, but renders JSON headers as well
     *
     * @return int
     */
    public static function json() {

        $json = call_user_func_array('json_encode', func_get_args());
        $err = json_last_error();

        # trigger a user error for failed encodings
        if ($err !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON encoding failed [{$err}].", 500);
        }

        header('Content-type: application/json');
        return print $json;
    }

    /**
     * shortcut for http_response_code()
     *
     * @param $code
     * @return int
     */
    public static function status($code) {
        return http_response_code($code);
    }


    /**
     * Check if request is an ajax request
     * @since  3.3.0
     * @return bool true if ajax
     */
    public static function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax']);
    }




}