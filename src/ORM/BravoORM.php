<?php

namespace Bravo\ORM;

use Bravo\ORM\Query;

trait BravoORM
{
    public static Query $query;
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

    public static function delete(array $keys, $operator = '=')
    {
        self::init();
        self::$query->delete()->where($keys, $operator)->execute();
    }

    public static function index($limit = 10)
    {
        self::init();
        return self::$query->select()->limit($limit)->execute()->obj();
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

    public static function all($order = 'asc', $key = 'id')
    {
        self::init();
        return self::$query->select()->orderBy("$key $order")->execute()->obj();
    }

    public static function find(array $columns_values, $operator = '=')
    {
        self::init();
        return self::$query->find($columns_values, $operator)->execute()->obj(true);
    }

    public static function findAll(array $columns_values, $operator = '=')
    {
        self::init();
        return self::$query->find($columns_values, $operator)->execute()->obj();
    }

    public static function findOrFail(array $columns_values, $operator = '=')
    {
        self::init();
        return self::$query->findOrFail($columns_values, $operator)->execute()->obj(true);
    }

    public static function findAllOrFail(array $columns_values, $operator = '=')
    {
        self::init();
        return self::$query->findOrFail($columns_values, $operator)->execute()->obj();
    }

    public static function orderBy($key = 'id', $order = 'asc', $limit = 10)
    {
        self::init();
        return self::$query->select()->orderBy("$key $order")->limit($limit)->execute()->obj();
    }

    public static function findAndOrderBy(array $columns_values, $key = 'id', $order = 'asc', $operator = '=')
    {
        self::init();
        return self::$query->find($columns_values, $operator)->orderBy("$key $order")->execute()->obj(true);
    }
}
