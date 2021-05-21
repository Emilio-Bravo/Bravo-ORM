<?php

namespace Bravo\ORM;

use Bravo\ORM\QueryInterface;
use Bravo\ORM\QueryHandler;
use Bravo\ORM\inputSanitizer;

/**
 * This class is in charge of making the queries to the database
 * @author Emilio Bravo
 */

class Query implements QueryInterface
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
     * Determines wether hasPendingQuery method can be used or not
     */
    protected $pendingQuery = false;
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
        $this->query = "INSERT INTO $this->table ";
        if ($this->is_assoc($values)) $this->setColumns($values);
        $this->setValues($values);
        return $this;
    }

    /**
     * Sets an UPDATE statement and activates the pendingQuery property
     * @param array $column_value Needs to be an associative array in wich selected columns will bind a value Example: ['name' => 'John']
     */

    public function update(array $values)
    {
        $this->query = "UPDATE $this->table SET ";
        array_map(fn ($key, $value) => $this->bindValues($key, $value), array_keys($values), $values);
        $this->query = inputSanitizer::santizeLastCharacter($this->query);
        return $this;
    }

    /**
     * Sets a DELETE statement and activates the pendingQuery property
     * @return this
     */

    public function delete()
    {
        $this->query = "DELETE FROM $this->table";
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
        $this->stmt = $this->connection->prepare($this->query);
        $this->stmt->execute($values);
        return $this->QueryHandler->is_void($this->stmt) ? new statementException :  new DataHandler($this->stmt);
    }

    /**
     * Adds a WHERE statement to the current query
     * @param mixed $value Value to be compared
     * @return this
     */

    public function where($value)
    {
        $this->query .= " WHERE $value";
        $this->pendingQuery = true;
        return $this;
    }

    /**
     * Adds a LIKE statement to the current query, which must have a WHERE statement beforehand
     * In case pendientQuery is active, hasPendingQuery method would be called
     * @param mixed $value Value to be compared
     * @return this
     */

    public function like($value)
    {
        $this->query .= " LIKE ?";
        if ($this->pendingQuery)  array_push($this->paramValues, $value);
        return $this;
    }

    /**
     * Adds a LIKE statement to the current query and also adds lower level of stricness in the query, which must have a WHERE statement beforehand
     * @param mixed $value Value to be compared
     * @return this
     */

    public function beLike($value)
    {
        $this->query .= " LIKE ?";
        if ($this->pendingQuery)  array_push($this->paramValues, "%$value%");
        return $this;
    }

    /**
     * Adds a value comparison to the current query, which must have a WHERE statement beforehand
     * In case pendientQuery is active, hasPendingQuery method would be called
     * @param mixed $value Value to be compared
     * @return this
     */

    public function equal($value)
    {
        $this->query .= " = ?";
        if ($this->pendingQuery)  array_push($this->paramValues, $value);
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
        $this->query .= " $key = ?,";
        array_push($this->paramValues, $value);
        return $this;
    }

    /**
     * Allows to have mutliple comparisons to in current query if necesary
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function multipleComparisons(array $keys_and_values, bool $strict = true)
    {
        $this->lessThanExpected($keys_and_values, $strict);
        array_map(fn ($key, $value) => $strict ? $this->and($key)->equal($value) : $this->and($key)->beLike($value), array_keys($keys_and_values), $keys_and_values);
        return $this;
    }

    /**
     * Allows to have mutliple cases comparisons to in current query if necesary
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function multipleCases(array $keys_and_values, bool $strict = true)
    {
        $this->lessThanExpected($keys_and_values, $strict);
        array_map(fn ($key, $value) => $strict ? $this->or($key)->equal($value) : $this->or($key)->beLike($value), array_keys($keys_and_values), $keys_and_values);
        return $this;
    }

    /**
     * Adds an AND statement to the current query, which must have a WHERE statement beforehand
     * this method will allow to have two or more comparisons depending on how many times it is used in the current query
     * @param mixed $value Value to be compared
     * @return this 
     */

    public function and($value)
    {
        $this->query .= " AND $value";
        return $this;
    }

    /**
     * Adds an OR statement to the current query, which must have a WHERE statement beforehand
     * this method will allow to have two or more different comparisons depending on how many times it is used in the current query
     * @param mixed $value Value to be compared
     * @return this 
     */

    public function or($value)
    {
        $this->query .= " OR $value";
        return $this;
    }
    /**
     * Adds or performs a SELECT statement to the current query
     * @param array $columns [optional] If provided the statment will apply to the specified columns
     * @return this
     */

    public function select(array $columns = null, array $tables = null)
    {
        $this->query = "SELECT ";
        $this->query .= is_array($columns) ? $this->toTarget($columns) : "*";
        $this->query .= " FROM ";
        $this->query .= is_array($tables) ? $this->toTarget($tables) : $this->table;
        return $this;
    }

    /**
     * Finds one or more register with the especified values
     * @param array $column_value ['name' => 'John', 'email' => 'john@mail.com']
     * @param array $columns [optional] columns to select
     * @param array $tables [optional] tables to select
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function complexFind(array $column_value, array $columns = null, array $tables = null, $strict = true)
    {
        $this->select($columns, $tables)->multipleComparisons($column_value, $strict);
        return $this;
    }

    /**
     * Finds one or more register with the especified values
     * @param array $column_value ['name' => 'John', 'email' => 'john@mail.com']
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function find(array $column_value, $strict = true)
    {
        $this->select()->multipleComparisons($column_value, $strict);
        return $this;
    }

    /**
     * Finds one or more register with the especified values evaluating different cases
     * @param array $column_value ['name' => 'John', 'email' => 'john@mail.com']
     * @param array $columns [optional] The columns be to selected
     * @param array $tables [optional] The tables to be selected
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function complexFindOrFail(array $column_value, array $columns = null, array $tables = null, bool $strict = true)
    {
        $this->select($columns, $tables)->multipleCases($column_value, $strict);
        return $this;
    }

    /**
     * Finds one or more register with the especified values evaluating different cases
     * @param array $column_value ['name' => 'John', 'email' => 'john@mail.com']
     * @param bool $strict [optional] Wheter the query is strict or not while searching results
     * @return this
     */

    public function findOrFail(array $column_value, $strict = true)
    {
        $this->select()->multipleCases($column_value, $strict);
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
        $this->query .= " VALUES" . "(" . $this->toTarget($this->BindedParam) . ")";
        return $this;
    }

    /**
     * Sets the columns to be affected in an statement
     * @return this
     */

    public function setColumns(array $values)
    {
        $this->query .= "(" . $this->toTarget(array_keys($values)) . ")";
        return $this;
    }
    /**
     * In case that pendingQuery has been activated (equals to true) this method will execute
     */

    public function hasPendingQuery()
    {
        return $this->queryExec(empty($this->paramValues) ? null : $this->paramValues);
    }

    /**
     * Gets all the results of the SELECT statement if executed
     * @return object
     */

    public function all()
    {
        if ($this->pendingQuery) return $this->hasPendingQuery();
        return $this->queryExec($this->paramValues ?? null);
    }

    /**
     * Sets a limit of results in the SELECT statement
     * @return object
     */

    public function limit(int $amount)
    {
        $this->query .= " LIMIT $amount";
        if ($this->pendingQuery) return $this->hasPendingQuery();
        return $this->queryExec($this->paramValues ?? null);
    }

    /**
     * Adds an ORDER BY statement to the current query
     * @param string $order
     * @return this
     */

    public function orderBy($order)
    {
        $this->query .= " ORDER BY $order";
        return $this;
    }

    /**
     * Sets the appropiate query in case that the array lenght is less tha expected (2)
     * @param array $array
     * @param bool $strict
     */

    public function lessThanExpected(array &$array, bool $strict)
    {
        $this->where(key($array));
        $strict ? $this->equal(array_values($array)[0]) : $this->beLike(array_values($array)[0]);
        array_shift($array);
    }
    
    /**
     * Performs a query into the databse
     * @return object
     */

    public function execute()
    {
        return $this->queryExec(empty($this->paramValues) ? null : $this->paramValues);
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

    /**
     * Organizes the data to be passed in the statement divided by commas
     * @param array $values
     * @return string
     */

    public function toTarget(array $values)
    {
        return implode(',', $values);
    }
}
