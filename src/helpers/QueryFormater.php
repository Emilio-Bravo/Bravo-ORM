<?php

namespace Bravo\ORM;

class QueryFormater
{
    /**
     * Organizes the data to be passed in the statement divided by commas
     * @param array $values
     * @return string
     */
    public static function targetize(array $values)
    {
        return implode(',', $values);
    }
    public static function columnize(array $values)
    {
        return "(" . implode(',', array_keys($values)) . ")";
    }
    public static function parameterize(array $values)
    {
        return "(" . implode(',', $values) . ")";
    }
}
