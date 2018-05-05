<?php

namespace widgets;


use app\Widget;

class Vote extends Widget
{
    public $data;
    public $id;
    public $vote;

    public function run()
    {
        $this->render('vote', [
            'vote' => $this->vote,
            'id' => $this->id,
        ]);
    }

}