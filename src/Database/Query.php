<?php

namespace Bravo\ORM;

/**
 * This class is in charge of making the queries to the database
 * 
 * @author Emilio Bravo
 */
class Query implements supportsCRUD, logicQuerys
{

    use verifyiesData, handlesExceptions;

    /**
     * Used to keep the parameters to be passed in an statement
     * 
     * @var array
     */
    protected array $paramValues = [];
    /**
     * Can be used to set the placeholders of an statement
     * 
     * @var array
     */
    protected array $BindedParam;

    /**
     * Used to keep the SQL statment that will be used
     * 
     * @var string|null
     */
    protected ?string $query = null;

    /**
     * Result of a PDO (prepare) method
     * This property is used for performing the query
     * 
     * @var \PDOStatement
     */
    protected \PDOStatement $stmt;

    /**
     * Represents the database connection
     * 
     * @var \Bravo\ORM\DB
     */
    protected DB $connection;

    /**
     * The table to be used
     * 
     * @var string
     */
    public string $table;

    /**
     * Create a new Query
     * 
     * @param string $table
     * @return void
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->connection = new DB;
    }

    /**
     * Sets the table name in an invokable way
     * 
     * @param string $table
     * @return self
     */
    public function __invoke(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Performs an INSERT statement into the database
     * 
     * @param array $values Values to insert
     * @return self
     */
    public function insert(array $values): self
    {
        $this->add("INSERT INTO $this->table ");

        if ($this->isAssoc($values)) {
            $this->setColumns($values);
        }

        $this->setValues($values);

        return $this;
    }

    /**
     * Sets an UPDATE statement
     * 
     * @param array $values Needs to be an associative array in wich selected columns will bind a value Example: ['name' => 'example']
     * @return self
     */
    public function update(array $values): self
    {
        $this->add("UPDATE $this->table SET ");
        array_map(fn ($key, $value) => $this->bindValues($key, $value), array_keys($values), $values);
        $this->query = rtrim($this->query, ',');

        return $this;
    }

    /**
     * Sets a DELETE statement
     * 
     * @return self
     */
    public function delete(): self
    {
        $this->add("DELETE FROM $this->table");

        return $this;
    }

    /**
     * Prepares and executes a query stored in the query property
     * 
     * @param array|null $values If provided as an array, the execute statement will pass the array in the query
     * @return \Bravo\ORM\DataHandler|false
     * 
     * @throws \RuntimeException
     */
    protected function terminate(?array $values = null): DataHandler|false
    {
        if (!$this->isConnected()) {
            throw new \RuntimeException('There is no database connection');
        }

        $this->stmt = $this->connection->prepare($this->query, [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]);

        return $this->stmt->execute($values)
            ? new DataHandler($this->stmt)
            : false;
    }

    /**
     * Adds a WHERE statement to the current query
     * 
     * @param array $data
     * @param string $operator The comparison operator to be used
     * @return self
     */
    public function where(array $data, string $operator = '='): self
    {
        $this->add(" WHERE ");
        $this->columnLink($data, $operator);

        return $this;
    }

    /**
     * Adds a (key - value) comparison to the current query, which must have a WHERE statement beforehand
     * 
     * @param string $key Key to ve compared with a value
     * @param string $value Value to be compared
     * @return self
     */
    protected function bindValues(string $key, string $value): self
    {
        $this->add(" $key = ?,");
        $this->keepValue($value);

        return $this;
    }

    /**
     * Adds or performs a SELECT statement to the current query
     * 
     * @param array|null $columns If provided the statment will apply to the specified columns
     * @param array|null $tables
     * @return self
     */
    public function select(?array $columns = null, array $tables = null): self
    {
        $this->add("SELECT ")
            ->add(\is_array($columns) ? QueryFormater::targetize($columns) : "*")
            ->add(" FROM ")
            ->add(\is_array($tables) ? QueryFormater::targetize($tables) : $this->table);

        return $this;
    }

    /**
     * Finds one or more rows with the specified values
     * 
     * @param array $data ['name' => 'John', 'email' => 'john@mail.com']
     * @param string $operator The comparison operator to be used
     * @return self
     */
    public function find(array $data, string $operator = '='): self
    {
        $this->select()->where($data, $operator);

        return $this;
    }
    /**
     * Finds one or more rows with the specified values evaluating different cases
     * 
     * @param array $data ['name' => 'John', 'email' => 'john@mail.com']
     * @param string $operator The comparison operator to be used
     * @return self
     */
    public function findOrFail(array $data, string $operator = '='): self
    {
        $this->select();
        $this->add(" WHERE ");
        $this->columnLink($data, $operator, 'OR');

        return $this;
    }

    /**
     * Sets the values for an INSERT statetment
     * 
     * @param array $values
     * @return self
     */
    protected function setValues(array $values): self
    {
        array_map(fn ($value) => $this->paramValues[] = $value, $values);
        array_map(fn () => $this->BindedParam[] = '?', $this->paramValues);
        $this->add(" VALUES" . QueryFormater::parameterize($this->BindedParam));

        return $this;
    }

    /**
     * Sets the columns to be affected in an statement
     * 
     * @param array $values
     * @return self
     */
    protected function setColumns(array $values): self
    {
        $this->add(QueryFormater::columnize($values));

        return $this;
    }

    /**
     * Gets all the results of the SELECT statement if executed
     * 
     * @return DataHandler|false
     */
    public function all(): DataHandler|false
    {
        return $this->terminate($this->paramValues);
    }

    /**
     * Sets a limit of results in the SELECT statement
     * 
     * @param int $amount
     * @return self
     */
    public function limit(int $amount): self
    {
        $this->add(" LIMIT $amount");

        return $this;
    }

    /**
     * Adds an ORDER BY statement to the current query
     * 
     * @param string $key
     * @param string $order
     * @return self
     */
    public function orderBy(string $key, string $order): self
    {
        $this->add(" ORDER BY $key $order");

        return $this;
    }

    /**
     * Performs the final query into the databse
     * 
     * @return DataHandler|false
     */
    public function execute(): DataHandler|false
    {
        try {
            return $this->terminate($this->paramValues);
        } catch (\RuntimeException $e) {
            $this->debug($e->getMessage());
        }
    }

    /**
     * Keeps a value that will be pased while executing the statement
     * 
     * @param string $value
     * @return self
     */
    protected function keepValue(string $value): self
    {
        array_push($this->paramValues, $value);

        return $this;
    }

    /**
     * Adds a string to the current query
     * 
     * @param string $str
     * @return self
     */
    public function add(string $str): self
    {
        $this->query .= $str;

        return $this;
    }

    /**
     * Binds a columns width a value
     * 
     * @param array $data
     * @param string $operator The comparison operator to be used
     * @param string $comparison_context What to add in case of more than 1 comparison
     * @return void
     */
    protected function columnLink(array $data, string $operator, string $comparison_context = 'AND'): void
    {
        foreach ($data as $column => $value) {
            $this->add("$column $operator ? $comparison_context ");
            $this->keepValue($value);
        }

        $this->query = preg_replace("/$comparison_context\z/", '', trim($this->query));
    }
}
