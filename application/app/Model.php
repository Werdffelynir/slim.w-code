<?php

namespace app;


class Model
{
    /** @var \app\db\SPDO  */
    public $db = null;

    /** @var null|string Current table name */
    public $table = null;

    /** @var null|string Primary key this table */
    public $primaryKey = null;

    /** @var array  */
    private static $models = [];

    /**
     * @param null $table
     * @param null $primaryKey
     * @param null $dbName
     */
    public function __construct($table = null, $primaryKey = null, $dbName = null)
    {
        if($table != null)
            $this->table = $table;
        if($table != null)
            $this->primaryKey = $primaryKey;

        $this->db = $this->db($dbName);
        $this->init();
    }

    /**
     *
     */
    protected function init(){}


    /**
     * Return all columns of table with default values
     * @return bool|array [column => value, column => value, ...] or false
     */
    public function columns(){
        if($this->table) {
            $tableCatInfo = $this->db->tableInfo($this->table);
            if($tableCatInfo) {
                $defaultCatData = [];
                foreach ($tableCatInfo as $ci) {
                    $ciTitle = $ci['name'];
                    $ciValue = $ci['dflt_value'];
                    if($ciValue == 'CURRENT_TIMESTAMP') $ciValue = date('d.m.Y H:i:s');
                    $defaultCatData[$ciTitle] = $ciValue;
                }
                return $defaultCatData;
            }
        }

        return false;
    }


    /**
     * Return all columns of table where keys in values
     * @return bool|array [0 => column, 1 => column, ...] or false
     */
    public function columnsKeys(){
        if($this->table) {
            $tableCatInfo = $this->db->tableInfo($this->table);
            if($tableCatInfo) {
                $defaultCatData = [];
                foreach ($tableCatInfo as $ci) {
                    $defaultCatData[] = $ci['name'];
                }
                return $defaultCatData;
            }
        }
        return false;
    }


    /**
     * Class for dynamic connect with table.
     * If you want nice? overwrite to children class:
     *
     * <pre>
     * param string $class
     * return \app\Model|self
     * public static function model($class = __CLASS__) {
     *    return parent::model($class);
     * }
     * </pre>
     *
     * @param string $className
     * @return Model|self
     */
    public static function model($className = null) {
        if($className === null)
            $className = get_called_class();
        if (isset(self::$models[$className])) {
            return self::$models[$className];
        } else {
            /** @var Model $model */
            $model = self::$models[$className] = new $className();
            return $model;
        }
    }


    /**
     * Return class SPDO with connection by name (from configure 'dbConnection'),
     * if name not specified, default name is first in configure.
     *
     * @param null $name
     * @return \app\db\SPDO|bool|null
     */
    public function db($name = null) {
        if($name == null && $this->db != null)
            return $this->db;

        return Core::db($name);
    }




    /**
     * Query data from current table
     * If in class child model not inset table name - return false.
     *
     * @param bool $all     all records or one
     * @param null $where   part SQL query after WHERE ...
     * @param null $bind    bind params
     * @return array|mixed
     */
    public function get($all = true, $where = null, $bind = null)
    {
        if($this->table){
            $where = (empty($where)) ? "" : " WHERE $where";
            $sql = "SELECT * FROM ".$this->table.$where;

            $pdoStat = $this->db()->prepare($sql);
            $pdoStat->execute((array)$bind);

            if($all)
                return $pdoStat->fetchAll();
            else
                return $pdoStat->fetch();
        }
        return false;
    }

    /**
     * @param null $where
     * @param null $bind
     * @return array|mixed
     */
    public function getAll($where = null, $bind = null) {
        return $this->get(true, $where, $bind);
    }

    /**
     * @param $attr
     * @param $value
     * @return array|mixed
     */
    public function getAllByAttr($attr, $value) {
        return $this->get(true, "$attr=:$attr", [":$attr"=>$value]);
    }

    /**
     * @param null $where
     * @param null $bind
     * @return array|mixed
     */
    public function getOne($where = null, $bind = null) {
        return $this->get(false, $where, $bind);
    }

    /**
     * @param $attr
     * @param $value
     * @return array|mixed
     */
    public function getOneByAttr($attr, $value) {
        return $this->get(false, "$attr=:$attr", [":$attr"=>$value]);
    }

    /**
     * @param $value
     * @return array|mixed
     */
    public function getOneById($value) {
        return $this->get(false, $this->primaryKey."=?", [$value]);
    }


    public function lastId() {
        return $this->get(false, 'id NOT NULL ORDER BY id DESC')[$this->primaryKey];
    }

}