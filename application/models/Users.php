<?php

namespace models;


use app\Model;

class Users extends Model
{

    public $table = 'users';

    public $primaryKey = 'id';

    protected function init(){}

    public function updateLastVisit($id){
        return $this->db->update($this->table, ['lastvisit'=>time()], 'id=?', [$id]);
    }

}