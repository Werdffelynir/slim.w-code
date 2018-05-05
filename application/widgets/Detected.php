<?php

namespace widgets;


use app\Widget;

class Detected extends Widget
{
    public $data;

    public function run()
    {
        $result = ['tip'=>'','country'=>'','region'=>'','city'=>''];

        if(is_string($this->data) && !empty($this->data)){
            try{
                $data = unserialize($this->data);
                if(is_array($data)){
                    $result = $data['geo'];
                    $result['tip'] = $this->render('detected_tip', ['data' => $data], true);
                    $result['prev'] = $data['prev'];
                }
            }
            catch(\Exception $e){}
        }
        //var_dump($result);
        return $result;
    }

}