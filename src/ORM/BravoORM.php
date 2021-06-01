<?php

namespace Bravo\ORM;

use Bravo\ORM\Query;

trait BravoORM
{
    public static $query;
    private static $pendingWhere = false;

    public static function init()
    {
        self::$query = new Query;
        self::$query->__invoke(self::class::$table);
    }

    public static function update(array $values)
    {
        self::init();
        self::$pendingWhere = true;
        self::$query->update($values);
        return new static;
    }

    public function where(array $columns_values, $operator = '=')
    {
        self::$query->where($columns_values, $operator);
        if (self::$pendingWhere) return self::$query->execute();
        return new static;
    }

    public static function destroy(array $keys, $operator = '=')
    {
        self::init();
        self::$query->delete()->where($keys, $operator)->execute();
    }

    public static function index($limit = 0)
    {
        self::init();
        return self::$query->select()->limit($limit)->obj();
    }

    public static function get()
    {
        self::init();
        self::$query->select();
        return new static;
    }

    public static function insert(array $values)
    {
        self::init();
        self::$query->insert($values)->execute();
    }
    public function all($output_type = 'obj')
    {
        return self::$query->execute()->$output_type();
    }

    public static function find(array $columns_values, $operator = '=')
    {
        self::init();
        return self::$query->find($columns_values, $operator)->execute()->obj();
    }
}
