<?php

namespace models;

use app\Model;

class _className extends Model
{

    /** table name */
    public $table = 'table';

    /** table primary field name  */
    public $primaryKey = 'id';

    /** table fields names */
    public $id;

    /**
     * Не обезателен в потомках. Для IDE.
     * @param string $class
     * @return Model|self
     */
    public static function model($class = __CLASS__) {
        return parent::model($class);
    }


    protected function init(){}



}