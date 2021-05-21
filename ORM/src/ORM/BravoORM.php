<?php

namespace Bravo\ORM;

use Bravo\ORM\Query;

class BravoORM
{
    private static $query;
    public static $table;

    public static function table(string $table)
    {
        self::$table = $table;
    }

    public static function init()
    {
        self::$query = new Query;
        self::$query->__invoke(self::$table);
    }

    public static function index(int $limit = 20, array $columns = null)
    {
        self::init();
        return self::$query->select($columns)->limit($limit);
    }

    public static function select(array $columns = null, array $tables = null, $orderBy = "id DESC")
    {
        self::init();
        return self::$query->select($columns, $tables)->orderBy($orderBy)->execute();
    }

    public static function create(array $data)
    {
        self::init();
        self::$query->insert($data)->execute();
    }

    public static function update(array $values, array $where)
    {
        self::init();
        self::$query->update($values)->multipleComparisons($where)->execute();
    }

    public static function destroy(array $where, bool $strict = true)
    {
        self::init();
        self::$query->delete()->multipleComparisons($where, $strict)->execute();
    }

    public static function complexFind(array $values, array $columns = null, array $tables = null, bool $strict = true, $orderBy = "id DESC")
    {
        self::init();
        return self::$query->complexFind($values, $columns, $tables, $strict)->orderBy($orderBy)->execute();
    }

    public static function complexFindOrFail(array $values, array $columns = null, array $tables = null, bool $strict = true, $orderBy = "id DESC")
    {
        self::init();
        return self::$query->complexFindOrFail($values, $columns, $tables, $strict)->orderBy($orderBy)->execute();
    }

    public static function find(array $column_and_value, bool $strict = true)
    {
        self::init();
        return self::$query->find($column_and_value, $strict)->execute();
    }

    public static function findOrFail(array $column_and_value, bool $strict = true)
    {
        self::init();
        return self::$query->findOrFail($column_and_value, $strict)->execute();
    }

    public static function fill(array $data, int $times)
    {
        for ($i = 0; $i < $times; $i++) self::create($data);
    }
}
