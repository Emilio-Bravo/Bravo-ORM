<?php

namespace Bravo\ORM;

/**
 * This class is in charge of making the queries to the database
 * @author Emilio Bravo
 */

class Query implements supportsCRUD, logicQuerys
{

    use verifyiesData, handlesExceptions;

    /**
     * Handles the query data
     */
    protected QueryHandler $QueryHandler;
    /**
     * Used to keep the parameters to be passed in an statement
     */
    protected array $paramValues = [];
    /**
     * Can be used to set the placeholders of an statement
     */
    protected array $BindedParam;
    /**
     * Used to keep the SQL statment that will be used
     */
    protected ?string $query = null;
    /**
     * Result of a PDO (prepare) method
     * This property is used for performing the query
     */
    protected \PDOStatement $stmt;
    /**
     * Represents the database connection
     */
    protected DB $connection;
    /**
     * The table to be used
     */
    public string $table;

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

    public function __invoke(string $table): Query
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Performs an INSERT statement into the database
     * @param array $values Values to insert
     */

    public function insert(array $values): Query
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

    public function update(array $values): Query
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

    public function delete(): Query
    {
        $this->add("DELETE FROM $this->table");
        return $this;
    }

    /**
     * Prepares and executes a query stored in the query property
     * @param array $values [optional] If provided as an array, the execute statement will pass the array in the query
     * @throws noConnectionException In case theres no connection with the database
     * @throws statementException In case something goes wrong with the statement
     * @return object DataHandler in case of success 
     */

    private function queryExec(array $values = null): object
    {
        if (!$this->is_connected())  throw new noConnectionException;
        $this->stmt = $this->connection->prepare($this->query, [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]);
        $this->stmt->execute($values);
        return $this->QueryHandler->is_void($this->stmt) ? throw new statementException : new DataHandler($this->stmt);
    }

    /**
     * Adds a WHERE statement to the current query
     * @param mixed $value Value to be compared
     * @return this
     */

    public function where(array $columns_values, $operator = '='): Query
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

    public function bindValues($key, $value): Query
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

    public function select(array $columns = null, array $tables = null): Query
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

    public function find(array $column_value, string $operator = '='): Query
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

    public function findOrFail(array $column_value, string $operator = '='): Query
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

    public function setValues(array $values): Query
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

    public function setColumns(array $values): Query
    {
        $this->add(QueryFormater::columnize($values));
        return $this;
    }

    /**
     * Gets all the results of the SELECT statement if executed
     * @return object
     */

    public function all(): object
    {
        return $this->queryExec($this->paramValues);
    }

    /**
     * Sets a limit of results in the SELECT statement
     * @return object
     */

    public function limit(int $amount): Query
    {
        $this->add(" LIMIT $amount");
        return $this;
    }

    /**
     * Adds an ORDER BY statement to the current query
     * @param string $order
     * @return this
     */

    public function orderBy(string $order): Query
    {
        $this->add(" ORDER BY $order");
        return $this;
    }

    /**
     * Performs the final query into the databse
     * @return object
     */

    public function execute(): object
    {
        try {
            return $this->queryExec($this->paramValues);
        } catch (\Bravo\ORM\statementException $e) {
            $this->debug($e->errorMessage());
        }
    }

    /**
     * Keeps a value that will be pased while executing the statement
     * @param string $value
     */

    public function keepValue($value): void
    {
        array_push($this->paramValues, $value);
    }

    /**
     * Adds a string to the current query
     * @param string $str
     */

    public function add(string $str): void
    {
        $this->query .= $str;
    }

    /**
     * Binds a columns width a value
     * @param string $operator The comparison operator to be used
     * @param string $comparison_context What to add in case of more than 1 comparison
     */

    public function column_value_relation(array $columns_values, $operator, $comparison_context = 'AND'): void
    {
        foreach ($columns_values as $column => $value) {
            $this->add("$column $operator ? $comparison_context ");
            $this->keepValue($value);
        }
        $this->query = preg_replace("/$comparison_context\z/", '', trim($this->query));
    }
}
