<?php

namespace Bravo\ORM;

use Bravo\ORM\Query;

class BravoORM
{
    private static $query;
    public static $table = 'users';

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

    public static function find(array $values, bool $strict = true)
    {
        self::init();
        return self::$query->find($values, $strict)->execute();
    }

    public static function fill(array $data, int $times)
    {
        for ($i = 0; $i < $times; $i++) self::create($data);
    }
}
