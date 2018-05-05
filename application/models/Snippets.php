<?php

namespace models;


use app\Model;

class Snippets extends Model{

    /** table name */
    public $table = 'snippets';

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

    /**
     * Возвращает снипеты про принципу:
     *      категории
     *      категории / субкатегории
     *      категории / субкатегории / синпет
     * В последенем случае будет сформирован многомерный массив но с одной записю
     *
     * @param null $permission
     * @param null $linkCat
     * @param null $linkSubcat
     * @param null $linkSnip
     * @return mixed
     */
    public function getSnippetsList($permission, $linkCat = null, $linkSubcat = null, $linkSnip = null)
    {
        $where = " s.enabled = ? AND sc.permission <= ? ";
        $binds = [1, (int)$permission];

        if($linkCat){
            $where .= " AND c.link = ? ";
            array_push($binds, (string)$linkCat);
        }

        if($linkSubcat){
            $where .= " AND sc.link = ? ";
            array_push($binds, (string)$linkSubcat);
        }

        if($linkSnip){
            $where .= " AND s.link = ? ";
            array_push($binds, (string)$linkSnip);
        }

        $sql = "SELECT
                  s.id as s_id,
                  s.link as s_link,
                  s.tags as s_tags,
                  s.vote as s_vote,
                  s.title as s_title,
                  s.content as s_content,
                  s.description as s_description,
                  s.ordering as s_ordering,
                  s.created as s_created,

                  c.id as c_id,
                  c.link as c_link,
                  c.title as c_title,
                  c.ordering as c_ordering,
                  c.permission as c_permission,

                  sc.id as sc_id,
                  sc.link as sc_link,
                  sc.title as sc_title,
                  sc.ordering as sc_ordering,
                  sc.permission as sc_permission,

                  u.id as u_id,
                  u.permission as u_permission,
                  u.name as u_name

                FROM snippets s
                LEFT JOIN subcategory sc ON (sc.id = s.idsubcategory)
                LEFT JOIN category c ON (c.id = sc.idcategory)
                LEFT JOIN users u ON (u.id = s.iduser)
              WHERE $where ORDER BY s.id DESC";

        return $this->db->executeAll($sql, $binds);
    }


    public function lastSnippets($permission=0, $limit=20)
    {
        return $this->db->executeAll("SELECT
            s.id, s.title, s.link, s.description,
            sc.id as scid, sc.title as sctitle, sc.link as sclink,
            c.id as cid, c.title as ctitle, c.link as clink
            FROM snippets s
            LEFT JOIN subcategory sc ON(sc.id = s.idsubcategory)
            LEFT JOIN category c ON(c.id = sc.idcategory)
            WHERE s.enabled = 1 AND s.permission <= $permission
            ORDER BY s.id desc
            LIMIT $limit");
    }


    public function searchQuery($words, $permission=0, $limit=20)
    {
        $res = [];

        $sqlFirst = "SELECT
            s.id, s.title, s.link, s.description,
            sc.id as scid, sc.title as sctitle, sc.link as sclink,
            c.id as cid, c.title as ctitle, c.link as clink
            FROM snippets s
            LEFT JOIN subcategory sc ON(sc.id = s.idsubcategory)
            LEFT JOIN category c ON(c.id = sc.idcategory)
            WHERE s.permission <= ? AND s.enabled = ? AND (s.tags LIKE ? OR s.title LIKE ?) LIMIT ?";
        $resFirst = $this->db->executeAll($sqlFirst, [
            $permission, 1, "%$words%", "%$words%", $limit
        ]);

        if(is_array($resFirst)) $res = $resFirst;
/*
        $sqlLast = "SELECT
            s.id, s.title, s.link,
            sc.id as scid, sc.title as sctitle, sc.link as sclink,
            c.id as cid, c.title as ctitle, c.link as clink
            FROM snippets s
            LEFT JOIN subcategory sc ON(sc.id = s.idsubcategory)
            LEFT JOIN category c ON(c.id = sc.idcategory)
            WHERE s.permission <= ? AND s.enabled = ? AND s.content LIKE ?  LIMIT ?";

        $resLast = $this->db->executeAll($sqlLast, [
            $permission, 1, "%$words%", $limit
        ]);

        if(is_array($resLast)){
            foreach($resLast as $rl){
                array_push($res, $rl);
            }
        }*/

        return $res;
    }



}