<?php

namespace widgets;


use app\Core;
use app\Widget;

class EditPanel extends Widget
{
    public $data;

    // IDs
    public $cat;
    public $subcat;
    public $snippet;
    public $user;

    public function run()
    {
        $authData = Core::controller()->authData;

        if($this->data){
            $this->cat = $this->data['c_id'];
            $this->subcat = $this->data['sc_id'];
            $this->snippet = $this->data['s_id'];
            $this->user = $this->data['u_id'];
        }

        if($authData && ( $authData['id'] == $this->user || $authData['permission'] >= 3 ))
        {
            $linkEdit = "/settings/{$this->cat}/{$this->subcat}/{$this->snippet}";

            $this->render('edit_panel', [
                'linkEdit'=> $linkEdit
            ]);
        }
    }

}