<?php

namespace models;


use app\Model;

class Visits extends Model
{

    public $table = 'visits';

    public $primaryKey = 'id';

    protected function init(){}


    public function getVisit($ip){
        return $this->db->select('*',$this->table, "ip = ? ", $ip, false);
    }

    public function insVisit($data){
        return $this->db->insert($this->table, $data);
    }

    public function updVisit($data){
        return $this->db->update($this->table, $data, 'ip=?', $data['ip']);
    }


    /* Счетчик посищений*/
    public function count(){
        return $this->db->select('*','visits_count', null, null, false);
    }
    public function countAdd(){
        $data = [
            'count' => (int) $this->count()['count'] + 1,
            'last'  => time()
        ];
        $this->db->update('visits_count', $data, 'id=1');
        return $data['count'];
    }

}