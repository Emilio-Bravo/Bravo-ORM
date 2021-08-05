<?php

namespace Bravo\ORM;

class QueryFormatter
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

    /**
     * Binds a columns width a value
     * 
     * @param array $data
     * @param string $operator The comparison operator to be used
     * @param string $comparison_context What to add in case of more than 1 comparison
     * @return string
     */
    public static function compare(array $data, string $operator, string $comparison_context = 'AND'): string
    {
        $str = null;

        foreach (array_keys($data) as $column) {
            $str .= "$column $operator ? $comparison_context";
        }

        return preg_replace("/$comparison_context\z/", '', trim($str));;
    }

    /**
     * Builds and orders the query values
     * 
     * @param array $query;
     * @return string
     */
    public static function build(array $query): string
    {
        $query = implode(' ', $query);

        if (self::needsOrder($query)) {
            $query = self::ensureWhereOrder($query);
        }

        return $query;
    }

    /**
     * Orders the parameters according to query
     * 
     * @param array $params
     * @param string $query
     * @return array
     */
    public static function ensureParameterOrder(array $params, string $query): array
    {
        return self::needsOrder($query) ? array_reverse($params) : $params;
    }

    /**
     * Orders the query components
     * 
     * @param string $str
     * @return string
     */
    protected static function ensureWhereOrder(string $str): string
    {
        preg_match('/(WHERE)+(\s)+(\w+\s=)+(\s\?)/', $str, $matches);

        $restingParts = explode($matches[0], $str)[1];

        $query = array_merge(
            (array) $restingParts,
            (array) $matches[0]
        );

        self::setAfterWhereComplements($query);

        return implode(' ', $query);
    }

    protected static function setAfterWhereComplements(array &$query): void
    {
        $query = implode(' ', $query);

        $excludes = self::getTerminatingComponents($query);

        $query = str_replace($excludes, [''], $query);

        $query = array_merge(explode(' ', $query), $excludes);
    }

    /**
     * Wheter the query components needs to be ordered in an especific way
     * 
     * @param string $query
     * @return bool
     */
    protected static function needsOrder(string $query): bool
    {
        $query = explode(' ', $query);

        return \in_array('WHERE', $query) && \array_search('WHERE', $query) === 0;
    }

    /**
     * Get the terminating query components
     * 
     * @param string $query
     * @return array
     */
    protected static function getTerminatingComponents(string $query): array
    {
        preg_match_all('/(ORDER BY \w+? \w+)(\sLIMIT\s\d){0,1}|(LIMIT\s\d)/', $query, $matches);

        return (array) $matches[0];
    }

    /**
     * Wheter the query has terminating components
     * 
     * @param string $query
     * @return bool
     */
    protected static function hasTerminatingComponents(string $query): bool
    {
        return
            str_contains($query, 'ORDER BY') ||
            str_contains($query, 'LIMIT');
    }

    /**
     * Wheter the query operation enters in CRUD
     * 
     * @param string $query
     * @return bool
     */
    protected static function isInCrud(string $query): bool
    {
        return
            str_contains($query, 'CREATE') || //Create
            str_contains($query, 'SELECT') || //Read
            str_contains($query, 'UPDATE') || //Update
            str_contains($query, 'DELETE');   //Delete
    }

    /**
     * Get the placeholders for the query
     * 
     * @param array $data
     * @return string
     */
    public static function getPlaceholders(array $data): string
    {
        return self::parameterize(array_fill(0, \count($data), '?'));
    }

    /**
     * Binds a column with a placeholder
     * 
     * @param array $data
     * @return string
     */
    public static function bind(array $data): string
    {
        foreach (array_keys($data) as $key) {
            $array[] = "$key = ?";
        }

        return implode(', ', $array);
    }
}
