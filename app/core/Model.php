<?php

namespace App\Core;
use \PDO;
abstract class Model {
    protected static $table;
    protected static $primaryKey = "id";
    protected static $wheres = [];
    protected static $orderBys = [];
    protected static $groupBy = [];
    protected static $limit = [];
    protected static $offset= [];
    
    protected static $db;
    public static function getDB() {
        return Database::getInstance();
    }
    
    public static function all() {
        $sql = "SELECT * FROM " . static::$table;
        return Database::query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function find($id) {
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        return Database::query($sql,['id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    public static function paginate($limit = 10, $page = 1) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM " . static::$table . " LIMIT {$limit} OFFSET {$offset}";
        $countQuery = "SELECT COUNT(*) as total FROM " . static::$table;

        $result = Database::query($query)->fetchAll(PDO::FETCH_ASSOC);
        $total = Database::query($countQuery)->fetch(PDO::FETCH_ASSOC)['total'];

        $lastPage = ceil($total/$limit);

        return[
            'data' => $result,
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => $lastPage
        ];
    }

    public function first() {
        $query = "SELECT * FROM " . static::$table . " WHERE ";
        $conditions = [];
        $params = [];
        foreach (static::$wheres as $where) {
            $conditions[] = "{$where['column']} {$where['operator']} :{$where['column']}";
            $params[":{$where['column']}"] = $where['value'];
        }
        $query .= implode(' AND ', $conditions) . " LIMIT 1";
        $stmt = Database::query($query, $params);
        return $stmt->fetchObject(static::class);
    }   

    public function where ($column, $operator, $value) {
        static::$wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function orderBy ($column, $direction) {
        static::$orderBys[] = [
            'column' => $column,
            'direction' => $direction
        ];
        return $this;
    }

    public function groupBy($column) {
        static::$wheres[] = $column;
        return $this;
    }

    public function limit ($limit) {
        static::$limit[] = $limit;
        return $this;
    }

    public function offset ($offset) {
        static::$offset[] = $offset;
        return $this;
    }
    public function get () {
        $query = "SELECT * FROM " . static::$table;

        if(!empty(static::$wheres)) {
            $query .= " WHERE ";
            foreach(static::$wheres as $index => $where) {
                if($index != 0) {
                    $query .= " AND ";
                }
                $query .= $where['column'] . " " . $where['operator'] . ' :' . $where['column'];
            }
        }

        if(!empty(static::$orderBys)) {
            $query .= " ORDER BY ";
            foreach(static::$orderBys as $index => $orderBy) {
                if($index != 0) {
                    $query .= ", ";
                }
                $query .= $orderBy['column'] . " " . $orderBy['direction'];
            }
        }

        if(!empty(static::$groupBy)) {
            $query .= " GROUP BY ";
            foreach(static::$groupBy as $index => $groupBy) {
                if($index != 0) {
                    $query .= ", ";
                }
                $query .= $groupBy;
            }
        }

        if(!empty(static::$limit)) {
            $query .= " LIMIT " . static::$limit[0];
            print_r($query);

        }

        if(!empty(static::$offset)) {
            $query .= " OFFSET " . static::$offset[0];
        }

        $stmt = Database::query($query, $this->getWhereParameters());
        var_dump($stmt);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     
    protected function getWhereParameters() {
        $parameters = [];

        foreach (static::$wheres as $where) {
            $parameters[$where['column']] = $where['value'];
        }
        return $parameters;
    }
}
