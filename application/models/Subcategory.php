<?php
/**
 * Created by PhpStorm.
 * User: ProStation
 * Date: 06.04.2015
 * Time: 2:16
 */

namespace models;


use app\Model;

class Subcategory extends Model
{
    /** table name */
    public $table = 'subcategory';

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

    public function getSubcategoryList($permission, $link)
    {
        $binds = [ 1, $permission, $link ];

        $sql = "SELECT

                  c.id as c_id,
                  c.link as c_link,
                  c.title as c_title,
                  c.ordering as c_ordering,
                  c.permission as c_permission,

                  sc.id as sc_id,
                  sc.link as sc_link,
                  sc.title as sc_title,
                  sc.ordering as sc_ordering,
                  sc.permission as sc_permission

                FROM subcategory sc
                LEFT JOIN category c ON (c.id = sc.idcategory)
                WHERE sc.enabled = ? AND sc.permission <= ? AND c.link = ? ORDER BY sc.ordering";

        return $this->db->executeAll($sql, $binds);
    }


}