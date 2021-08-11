<?php

namespace Bravo\ORM;

use Bravo\ORM\QueryFormatter as Helper;

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
     * @var array
     */
    protected array $query = [];

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
     * Query parameter bag
     * 
     * @var \Bravo\ORM\ParameterBag
     */
    protected ParameterBag $parameterBag;

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
        $this->parameterBag = new ParameterBag;
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
     * Get the current query
     * 
     * @return string
     */
    public function __toString(): string
    {
        return Helper::build($this->query);
    }

    /**
     * Performs an INSERT statement into the database
     * 
     * @param array $values Values to insert
     * @return self
     */
    public function insert(array $values): self
    {
        $this->add("INSERT INTO $this->table");

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
        $this->add("UPDATE $this->table SET");

        $this->parameterBag->multiAppend($values);

        $this->add(Helper::bind($values));

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
     * @param array $values Values of the query
     * @return \Bravo\ORM\DataHandler|false
     * 
     * @throws \RuntimeException
     */
    protected function terminate(array $values): DataHandler|false
    {
        if (!$this->isConnected()) {
            throw new \RuntimeException('There is no database connection');
        }

        $this->prepare($values);
        
        $this->stmt = $this->connection->prepare(
            (string) $this,
            [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]
        );

        return $this->stmt->execute($values)
            ? new DataHandler($this->stmt)
            : false;
    }

    /**
     * Prepares the query values
     * 
     * @param array $values
     * @return void
     */
    protected function prepare(array &$values): void
    {
        $values = Helper::ensureParameterOrder(
            $values,
            implode(' ', $this->query)
        );
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
        $this->add('WHERE');
        $this->add(Helper::compare($data, $operator));

        $this->parameterBag->multiAppend($data);

        return $this;
    }

    /**
     * Adds or performs a SELECT statement to the current query
     * 
     * @param array|null $columns If provided the statment will apply to the specified columns
     * @param array|null $tables
     * @return self
     */
    public function select(?array $columns = null, ?array $tables = null): self
    {
        $this->add("SELECT")
            ->add(\is_array($columns) ? Helper::targetize($columns) : '*')
            ->add('FROM')
            ->add(\is_array($tables) ? Helper::targetize($tables) : $this->table);

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
        $this->add('WHERE');
        $this->add(Helper::compare($data, $operator, 'OR'));

        $this->parameterBag->multiAppend($data);

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
        $this->parameterBag->multiAppend($values);

        $this->add("VALUES")->add(Helper::getPlaceholders($values));

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
        $this->add(Helper::columnize($values));

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
        $this->add("LIMIT $amount");

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
        $this->add("ORDER BY $key $order");

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
            return $this->terminate($this->parameterBag->array_values());
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
        $this->parameterBag->append($value);

        return $this;
    }

    /**
     * Get wheter the query has a value
     * 
     * @param string $value
     * @return bool
     */
    public function has(string $value): bool
    {
        return \is_array($this->query) && \in_array($value, $this->query);
    }

    /**
     * Get the query parameter bag
     * 
     * @return \Bravo\ORM\ParameterBag
     */
    public function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }

    /**
     * Adds a string to the current query
     * 
     * @param string|array $query
     * @return self
     */
    public function add(string $query): self
    {
        $this->query = array_merge(
            $this->query,
            explode(' ', $query)
        );

        return $this;
    }
}
