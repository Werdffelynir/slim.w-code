<?php

namespace models;


use app\Model;

class Category extends Model
{

    public $table = 'category';

    public $primaryKey = 'id';

    /**
     * @param string $class
     * @return Model|self
     */
    public static function model($class = __CLASS__) {
        return parent::model($class);
    }

    protected function init(){}



}