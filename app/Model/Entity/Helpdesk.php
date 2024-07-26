<?php

namespace App\Model\Entity;
use WilliamCosta\DatabaseManager\Database;

class Helpdesk {
        
    /**
     * getHelpdesks
     *
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getHelpdesks($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('helpdesk'))->select($where, $order, $limit, $fields);
    }
}