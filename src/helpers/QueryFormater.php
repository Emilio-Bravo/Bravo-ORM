<?php

namespace Bravo\ORM;

class QueryFormater
{

    /**
     * Organizes the data to be passed in the statement divided by commas
     * 
     * @param array $values
     * @return string
     */
    public static function targetize(array $values): string
    {
        return implode(', ', $values);
    }

    /**
     * Takes the array's keys and divides them with commas beetween parenthesis
     * 
     * @param array $values
     * @return string
     */
    public static function columnize(array $values): string
    {
        return "(" . implode(', ', array_keys($values)) . ")";
    }

    /**
     * Divides the data with commas beetween parenthesis
     * 
     * @param array $values
     * @return string
     */
    public static function parameterize(array $values): string
    {
        return "(" . implode(', ', $values) . ")";
    }
}
