<?php

namespace app;


class Layout extends \Slim\View
{

    public $lastRenderPath = null;
    private $layout = null;
    private $expansion = null;
    private static $output = [];
    private static $values = [];

    public function __construct()
    {
        parent::__construct();

        $params = Core::param('view');

        $this->layout = $params['layout'];
        $this->expansion = $params['expansion'];

        $values = [
            'url' => Core::param('url'),
            'root' => Core::param('root'),
            'title' => $params['title'],
            'charset' => $params['charset'],
            'language' => Core::param('language'),
        ];

        # added param to layout as variables
        $this->appendData($values);

        # added param to array $values
        self::$values = $values;
    }


    /**
     * Добавляет расширение в переданой строке
     * @param $file
     * @return string
     */
    private function setExpansion($file){
        if($this->expansion == null)
            $this->expansion = 'php';
        $expansion = '.'.ltrim($this->expansion,'.');
        $path = (substr($file, -(strlen($expansion))) == $expansion) ? $file :  $file.$expansion;
        return $path;
    }


    /**
     * Переопределение, для установки расширения файла
     *
     * @param string $template
     * @param null $data
     * @param bool $extractedDataAll
     * @return string
     */
    public function render($template, $data = null, $extractedDataAll = true)
    {
        $templatePathname = $this->getTemplatePathname($template);
        $templatePathname = $this->setExpansion($templatePathname);

        if (!is_file($templatePathname))
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");

        if($extractedDataAll){
            $data = array_merge($this->data->all(), (array) $data);
        }

        extract((array) $data);
        ob_start();
        require $templatePathname;

        return ob_get_clean();
    }


    /**
     * Отображает шаблон layout, переданые аргументы ($view, $data) помещаються в основной output
     * Отображение переданых данных происходит при вызове метода в нутри шаблона layout
     * методом Layout::output() без параметров - это указывает на основную позицию.
     *
     * @param $view
     * @param null $data
     * @param bool $returned
     * @return bool|string
     */
    public function renderLayout($view, $data = null, $returned = false)
    {
        $this->addOutput('default_content', $this->render($view, $data, false));

        if($returned)
            return $this->render($this->layout);
        else
            $this->display($this->layout, $this->all());

        return true;
    }

    /**
     * Установить - порлучить layout
     *
     * @param null $view
     * @return null
     */
    public function layout($view = null) {
        if($view === null)
            return $this->layout;
        $this->layout = $view;
    }

    /**
     * Позиция вывода данных. Вывод существующих уже установленных позиций
     * Установка происходит с помощю метода setOutput() и addOutput()
     *
     * @param string $key
     * @param bool   $display
     * @param bool   $exists    Проверяет на существование ключа. По уолчанию true
     * @return mixed
     */
    public static function output($key='default_content', $display = true, $exists = true) {
        if(isset(self::$output[$key])){

            if ($display)
                echo self::$output[$key];
            else
                return self::$output[$key];
        }
        else if($exists)
            throw new \RuntimeException('Error! Output with key ['.$key.'] not exists!');
    }


    /**
     * Установить заново или обеденить позиции вывода данных в шаблоне layout
     *
     * @param array $data
     * @param bool $merge
     */
    public function setOutput(array $data, $merge = false) {
        if($merge)
            self::$output = array_merge(self::$output, $data);
        else
            self::$output = $data;
    }

    /**
     * Добавить новую позицию для вывода данных в шаблоне layout
     *
     * @param string $key
     * @param mixed $data
     */
    public function addOutput($key, $data) {
        self::$output[$key] = $data;
    }

    /**
     * Проверяет на налицие позиции вывода или есть ли содержимое
     *
     * @param $name
     * @param bool $notEmpty
     * @return bool
     */
    public function isOutput($name, $notEmpty = true){
        if($notEmpty)
            return !empty(self::$output[$name]);
        else
            return isset(self::$output[$name]);
    }

    /**
     * Медод установки / вывода ограниченых данных в layout
     *
     * @param $key
     * @param bool $value
     * @return null|string
     */
    public function addValue($key, $value = false)
    {
        if(is_array($key)){
            foreach ($key as $_key => $_value)
                self::$values[$_key] = $_value;
        }
        elseif($value===false){
            return (isset(self::$values[$key])) ? self::$values[$key] : '';
        }
        else
            self::$values[$key] = $value;
        return null;
    }


    /**
     * Вывод установленных ограниченых данных в шаблоне layout
     *
     * @param string $key
     * @param null $elseValue
     * @return mixed
     */
    public static function value($key, $elseValue = null) {
        if(isset(self::$values[$key]))
            return self::$values[$key];
        elseif($elseValue != null)
            return $elseValue;

        return false;
    }


    /**
     * Медоды установки основных переменных layout
     * Использует методу рожителя appendData()
     *
     * @param $key
     * @param $data
     */
    public function addData($key, $data) {
        $this->appendData([$key=>$data]);
    }




}