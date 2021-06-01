<?php

namespace Bravo\ORM;

use Bravo\ORM\supportsCRUD;
use Bravo\ORM\QueryHandler;
use Bravo\ORM\inputSanitizer;
use Bravo\ORM\QueryFormater;

/**
 * This class is in charge of making the queries to the database
 * @author Emilio Bravo
 */

class Query implements supportsCRUD, logicQuerys
{

    /**
     * Handles the query data
     */
    protected $QueryHandler;
    /**
     * Used to keep the parameters to be passed in an statement
     */
    protected $paramValues = [];
    /**
     * Can be used to set the placeholders of an statement
     */
    protected $BindedParam;
    /**
     * Used to keep the SQL statment that will be used
     */
    protected $query;
    /**
     * Result of a PDO (prepare) method
     * This property is used for performing the query
     */
    protected $stmt;
    /**
     * Represents the database connection
     */
    protected $connection;
    /**
     * The table to be used
     */
    public $table;

    /**
     * Performs the databse connection and the class settings
     */

    public function __construct()
    {
        $this->connection = new DB;
        $this->QueryHandler = new QueryHandler;
    }

    /**
     * Sets the table name
     * @return this
     */

    public function __invoke($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Performs an INSERT statement into the database
     * @param array $values Values to insert
     */

    public function insert(array $values)
    {
        $this->add("INSERT INTO $this->table ");
        if ($this->is_assoc($values)) $this->setColumns($values);
        $this->setValues($values);
        return $this;
    }

    /**
     * Sets an UPDATE statement
     * @param array $column_value Needs to be an associative array in wich selected columns will bind a value Example: ['name' => 'John']
     */

    public function update(array $values)
    {
        $this->add("UPDATE $this->table SET ");
        array_map(fn ($key, $value) => $this->bindValues($key, $value), array_keys($values), $values);
        $this->query = inputSanitizer::santizeLastCharacter($this->query);
        return $this;
    }

    /**
     * Sets a DELETE statement
     * @return this
     */

    public function delete()
    {
        $this->add("DELETE FROM $this->table");
        return $this;
    }

    /**
     * Prepares and executes a query stored in the query property
     * @param array $values [optional] If provided as an array, the execute statement will pass the array in the query
     * @return object DataHandler in case of success or an Exception in case of an issue 
     */

    private function queryExec(array $values = null)
    {
        if (!$this->is_connected())  throw new noConnectionException;
        $this->stmt = $this->connection->prepare($this->query, [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]);
        $this->stmt->execute($values);
        return $this->QueryHandler->is_void($this->stmt) ? throw new statementException :  new DataHandler($this->stmt);
    }

    /**
     * Adds a WHERE statement to the current query
     * @param mixed $value Value to be compared
     * @return this
     */

    public function where(array $columns_values, $operator = '=')
    {
        $this->add(" WHERE ");
        $this->column_value_relation($columns_values, $operator);
        return $this;
    }

    /**
     * Adds a (key - value) comparison to the current query, which must have a WHERE statement beforehand
     * @param mixed $key Key to ve compared with a value
     * @param mixed $value Value to be compared
     * @return this
     */

    public function bindValues($key, $value)
    {
        $this->add(" $key = ?,");
        $this->keepValue($value);
        return $this;
    }

    /**
     * Adds or performs a SELECT statement to the current query
     * @param array $columns [optional] If provided the statment will apply to the specified columns
     * @return this
     */

    public function select(array $columns = null, array $tables = null)
    {
        $this->add("SELECT ");
        $this->add(is_array($columns) ? QueryFormater::targetize($columns) : "*");
        $this->add(" FROM ");
        $this->add(is_array($tables) ? QueryFormater::targetize($tables) : $this->table);
        return $this;
    }

    /**
     * Finds one or more register with the especified values
     * @param array $column_value ['name' => 'John', 'email' => 'john@mail.com']
     * @param string $operator [optional] The comparison operator to be used
     * @return this
     */

    public function find(array $column_value, $operator = '=')
    {
        $this->select()->where($column_value, $operator);
        return $this;
    }
    /**
     * Finds one or more register with the especified values evaluating different cases
     * @param array $column_value ['name' => 'John', 'email' => 'john@mail.com']
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function findOrFail(array $column_value, $operator = '=')
    {
        $this->select();
        $this->add(" WHERE ");
        $this->column_value_relation($column_value, $operator, 'OR');
        return $this;
    }

    /**
     * Sets the values for an INSERT statetment
     * @return this
     */

    public function setValues(array $values)
    {
        array_map(fn ($value) => $this->paramValues[] = $value, $values);
        array_map(fn () => $this->BindedParam[] = '?', $this->paramValues);
        $this->add(" VALUES" . QueryFormater::parameterize($this->BindedParam));
        return $this;
    }

    /**
     * Sets the columns to be affected in an statement
     * @return this
     */

    public function setColumns(array $values)
    {
        $this->add(QueryFormater::columnize($values));
        return $this;
    }

    /**
     * Gets all the results of the SELECT statement if executed
     * @return object
     */

    public function all()
    {
        return $this->queryExec($this->paramValues);
    }

    /**
     * Sets a limit of results in the SELECT statement
     * @return object
     */

    public function limit(int $amount)
    {
        $this->add(" LIMIT $amount");
        return $this->queryExec($this->paramValues);
    }

    /**
     * Adds an ORDER BY statement to the current query
     * @param string $order
     * @return this
     */

    public function orderBy($order)
    {
        $this->add(" ORDER BY $order");
        return $this->execute();
    }

    /**
     * Performs a query into the databse
     * @return object
     */

    public function execute()
    {
        return $this->queryExec($this->paramValues);
    }

    /**
     * Verifies if a valid database connection exists
     * @return bool
     */

    public function is_connected()
    {
        if (!$this->connection) return false;
        return true;
    }

    /**
     * Deternmines wether an array key is associative or not
     * @return bool
     */

    public function is_assoc(array $array)
    {
        return !is_int(key($array));
    }

    public function keepValue($value)
    {
        array_push($this->paramValues, $value);
    }

    public function add(string $str)
    {
        $this->query .= $str;
    }

    public function column_value_relation(array $columns_values, $operator, $comparison_context = 'AND')
    {
        foreach ($columns_values as $column => $value) {
            $this->query .= "$column $operator ? $comparison_context ";
            $this->keepValue($value);
        }
        $this->query = preg_replace("/$comparison_context\z/", '', trim($this->query));
    }
}
