<?php


namespace app\html;


class Src {

    private static $stackJSHead;
    private static $stackJSBody;
    private static $stackCSS;
    private static $depth = 10;
    private static $position = 'bottom';

    private static $isGenerated = false;
    private static $scriptsHead = null;
    private static $scriptsBody = null;


    public static function scriptsHead($display = true){
        self::generateHTMLRun();

        if($display == true)
            echo  self::$scriptsHead;
        else
            return  self::$scriptsHead;
    }


    public static function scriptsBody(){
        self::generateHTMLRun($display = true);

        if($display == true)
            echo  self::$scriptsBody;
        else
            return  self::$scriptsBody;
    }


    public static function generateHTMLRun(){
        if(!self::$isGenerated){
            self::$isGenerated = true;
            $generateData = self::generate();
            self::$scriptsHead = $generateData['body'];
            self::$scriptsBody = $generateData['head'];
        }
    }



    /**
     * @return array [head, body]
     */
    public function generate()
    {
        $htmlHead = '';
        $htmlBody = '';

        if(!empty(self::$stackCSS)){
            usort(self::$stackCSS, function($arg1,$arg2){
                return ($arg1['depth']>$arg2['depth'])?1:-1;
            });
            foreach(self::$stackCSS as $style){
                $htmlHead .= "<link href=\"{$style['href']}\" rel=\"{$style['rel']}\" type=\"{$style['type']}\" />\n";
            }
        }
        if(!empty(self::$stackJSHead)){
            usort(self::$stackJSHead, function($arg1,$arg2){
                return ($arg1['depth']>$arg2['depth'])?1:-1;
            });
            foreach(self::$stackJSHead as $script){
                if($script['position']=='bottom'||$script['position']==true||$script['position']=='b')
                    $htmlBody .= "<script type=\"text/javascript\" src=\"{$script['src']}\"></script>\n";
                else
                    $htmlHead .= "<script type=\"text/javascript\" src=\"{$script['src']}\"></script>\n";
            }
        }
        if(!empty(self::$stackJSBody)){
            $htmlBody .= "\n<script type=\"text/javascript\">\n".self::$stackJSBody."\n</script>\n";
        }

        return [
            'head'=>$htmlHead,
            'body'=>$htmlBody,
        ];
    }


    public static function addJavascript($dataString)
    {
        self::$stackJSBody .= $dataString;
    }

    /**
     * @param $data
     * @param $callable
     */
    private static function addTo($data, $callable)
    {
        foreach($data as $d) {
            $arg1 = (isset($d[0]))?$d[0]:null;
            $arg2 = (isset($d[1]))?$d[1]:null;
            $arg3 = (isset($d[2]))?$d[2]:self::$depth;
            $arg4 = (isset($d[3]))?$d[3]:self::$position;

            if($arg1 && $arg2)
                self::$callable($arg1,$arg2,$arg3,$arg4);
            else
                continue;
        }
    }


    /**
     * @param $data
     * @param null $src
     * @param null $depth
     * @param null $position
     */
    public static function addScript($data, $src=null, $depth=null, $position=null)
    {
        if(is_string($data))
        {
            if($depth==null) $depth = self::$depth;
            if($position==null) $position = self::$position;
            self::$stackJSHead[$data]['name'] = $data;
            self::$stackJSHead[$data]['src'] = '/public/'.trim($src,'/');
            self::$stackJSHead[$data]['depth'] = $depth;
            self::$stackJSHead[$data]['position'] = $position;
            self::$depth ++;
        }
        else if(is_array($data))
        {
            self::addTo($data, 'addScript');
        }
    }

    /**
     * @param $name
     */
    public static function delScript($name)
    {
        if(isset(self::$stackJSHead[$name])){
            unset(self::$stackJSHead[$name]);
        }
    }

    /**
     * @param $searchName
     * @param $addName
     * @param $src
     * @throws \ErrorException
     */
    public static function addBeforeScript($searchName,$addName,$src)
    {
        if(isset(self::$stackJSHead[$searchName])){
            self::addScript($addName,$src,self::$stackJSHead[$searchName]['depth']-1);
        }else
            throw new \ErrorException();
    }

    /**
     * @param $searchName
     * @param $addName
     * @param $src
     * @throws \ErrorException
     */
    public static function addAfterScript($searchName,$addName,$src)
    {
        if(isset(self::$stackJSHead[$searchName])){
            self::addScript($addName,$src,self::$stackJSHead[$searchName]['depth']+1);
        }else
            throw new \ErrorException();
    }

    /**
     * @param $data
     * @param null $src
     * @param null $depth
     */
    public static function addStyle($data, $src, $depth=null)
    {
        if(is_string($data))
        {
            $href = '';
            $rel = 'stylesheet';
            $type = 'text/css';

            if($depth==null) $depth = self::$depth;
            self::$stackCSS[$data]['name'] = $data;
            self::$stackCSS[$data]['depth'] = $depth;
            if(is_array($src)){
                $href = (isset($src['href']))?$src['href']:$href;
                $rel = (isset($src['rel']))?$src['rel']:$rel;
                $type = (isset($src['type']))?$src['type']:$type;
            }else
                $href = $src;

            self::$stackCSS[$data]['href'] = '/public/'.trim($href,'/');
            self::$stackCSS[$data]['rel'] = $rel;
            self::$stackCSS[$data]['type'] = $type;
        }
        else if(is_array($data))
        {
            self::addTo($data, 'addStyle');
        }
    }

    /**
     * @param $name
     */
    public static function delStyle($name)
    {
        if(isset(self::$stackCSS[$name])){
            unset(self::$stackCSS[$name]);
        }
    }

    /**
     * @param $searchName
     * @param $addName
     * @param $src
     * @throws \ErrorException
     */
    public static function addBeforeStyle($searchName,$addName,$src)
    {
        if(isset(self::$stackCSS[$searchName])){
            self::addStyle($addName,$src,self::$stackCSS[$searchName]['depth']-1);
        }else
            throw new \ErrorException();
    }

    /**
     * @param $searchName
     * @param $addName
     * @param $src
     * @throws \ErrorException
     */
    public static function addAfterStyle($searchName,$addName,$src)
    {
        if(isset(self::$stackCSS[$searchName])){
            self::addStyle($addName,$src,self::$stackCSS[$searchName]['depth']+1);
        }else
            throw new \ErrorException();
    }

}