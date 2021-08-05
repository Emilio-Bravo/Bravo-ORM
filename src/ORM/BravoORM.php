<?php

namespace Bravo\ORM;

/**
 * @method static \Bravo\ORM\ORM update(array $values)
 * @method static \Bravo\ORM\ORM where(array $columns_values, string $operator = '=')
 * @method static \Bravo\ORM\ORM update(array $values) 
 * @method static \Bravo\ORM\ORM delete(array $keys, string $operator = '=')
 * @method static \Bravo\ORM\ORM orderBy(string $key = 'id', string $order = 'asc')
 * @method static \Bravo\ORM\ORM get()
 * @method static \Bravo\ORM\ORM insert(array $values)
 * @method static \Bravo\ORM\ORM setTable(string $table)
 * @method static \Bravo\ORM\Query getQuery()
 * @method static object find(array $columns_values, string $operator = '=')
 * @method static object findOrFail(array $columns_values, string $operator = '=')
 * @method static bool getHasPendingWhere()
 * @method static string getTable()
 * @method static array index(int $limit = 10)
 * @method static array all(string $order = 'asc', string $key = 'id')
 * @method static array findAll(array $columns_values, string $operator = '=')
 * @method static array findAllOrFail(array $columns_values, string $operator = '=')
 * 
 * @see \Bravo\ORM\ORM
 */
trait BravoORM
{
    /**
     * Dinamicly calls the ORM methods
     * 
     * @param string $method
     * @param mixed $arguments
     * @return mixed
     * 
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $method, $arguments)
    {
        $orm = self::setORM();

        if (!\method_exists($orm, $method)) {

            throw new \BadMethodCallException(
                sprintf('ORM::%s does not exists', $method)
            );
        }

        return $orm->$method(...$arguments);
    }

    /**
     * Sets the model table
     * 
     * @return \Bravo\ORM\ORM
     * 
     * @throws \RuntimeException
     */
    protected static function setORM(): ORM
    {
        $self = new self;

        if (!\property_exists($self, 'table')) {

            throw new \RuntimeException(
                sprintf('Property "table" does not exists in %s', $self::class)
            );
        }

        $orm = new ORM($self->table);

        return $orm;
    }
}
