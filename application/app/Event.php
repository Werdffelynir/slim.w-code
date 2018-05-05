<?php

namespace app;


class Event {

    private static $events = [];

    /**
     * Apply function with args in array,
     * or throw exception
     *
     * @param callable $callable callback function
     * @param array $args function arguments
     *
     * @throws
     *
     * @return mixed
     */
    public static function apply($callable, $args)
    {
        if (!is_callable($callable)) {
            throw new \RuntimeException('invalid callable');
        }
        return call_user_func_array($callable, $args);
    }


    /**
     * Added or replace event
     *
     * <pre>
     * # set closure function
     *  Event::add('name', function($arg){...});
     *
     * # set method from current class
     *  Event::add('name', [$this,'method']);
     *
     * #set method from other class
     *  Event::add('name', [new \namespace\Class,'method']);
     *
     * #set static method from other class
     *  Event::add('upper', ['\namespace\Class','method']);
     * #or
     *  Event::add('upper', '\controllers\Index::upper');
     *
     * </pre>
     *
     * @param string $name event name
     * @param callable $callable callback function
     * @param array $argsDefault default function arguments
     *
     * @throws
     */
    public static function add($name, $callable, $argsDefault = null)
    {
        if (!is_callable($callable)) {
            throw new \RuntimeException('invalid callable');
        }
        self::$events[(string) $name]['call'] = $callable;
        self::$events[(string) $name]['args'] = $argsDefault;
    }


    /**
     * Call event by name with args
     *
     * @param $name
     * @param mixed... Аргумент функции массив
     * @return mixed|bool
     */
    public static function call($name)
    {
        if (isset(self::$events[(string) $name]))
        {
            $args = func_get_args();
            array_shift($args);

            if(empty($args))
                $args = self::$events[(string) $name]['args'];

            return self::apply(self::$events[(string) $name]['call'], $args);
        }
        else
            return false;
    }


    /**
     * Return event if exists
     *
     * @param null $name
     * @return array|null
     */
    public static function get($name = null)
    {
        if (!is_null($name)) {
            return isset(self::$events[(string) $name]) ? self::$events[(string) $name] : null;
        } else {
            return self::$events;
        }
    }


    /**
     * Clear event or all events
     *
     * @param null $name
     * @return bool
     */
    public static function clear($name = null)
    {
        if($name == null){
            self::$events = [];
            return true;
        } else if(isset(self::$events[(string) $name])) {
            unset(self::$events[(string) $name]);
            return true;
        }
        return false;
    }


    /**
     * Set or get session message.
     * One param - return message and delete from session
     * Two params - set message
     * Three params and last is true - keep message
     *
     * <pre># register
     * Event::flash('ok','Update success!');
     *
     * # call after register
     * Event::flash('ok');
     *
     * # keep session to next called
     *  Event::flash('ok',true,true);
     * </pre>
     *
     * @param string $key
     * @param string $value
     * @param bool $keep
     *
     * @return mixed
     */
    public static function flash($key = null, $value = null, $keep = false)
    {
        $flash = null;
        if (!isset($_SESSION)) session_start();

        if(func_num_args() == 1 || func_num_args() == 3){
            $flash = isset($_SESSION['flash_data'][$key]) ? $_SESSION['flash_data'][$key] : null;
            goto keeper;
        }
        elseif(func_num_args() == 2){
            $_SESSION['flash_data'][$key] = $value;
            goto end;
        }

        keeper:
        if(!$keep)
            unset($_SESSION['flash_data'][$key]);

        return $flash;

        end:
        return true;
    }

}