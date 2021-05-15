<?php

namespace Bravo\ORM;

use Bravo\ORM\QueryInterface;
use Bravo\ORM\QueryHandler;

class Query implements QueryInterface
{
    protected $QueryHandler;
    protected $DataHandler;
    protected $query;
    protected $params;
    protected $connection;
    protected $smtp;
    public $table;
    public $attributes = null;

    /**
     * Performs the databse connection
     * @return \Src\Database\DB
     */
    public function connect()
    {
        $this->connection = new DB;
    }
    public function initQueryHanlder()
    {
        $this->QueryHandler = new QueryHandler;
    }
    public function __construct()
    {
        $this->connection = new DB;
        $this->QueryHandler = new QueryHandler;
        $this->DataHandler = new DataHandler;
    }
    public function table()
    {
    }
    public function selectAll()
    {
    }
    public function insert(array $values)
    {
        $this->query = "INSERT INTO $this->table VALUES(?)";
    }
    public function update()
    {
    }
    public function query($query, array $values = null)
    {
        if (!$this->connection) return;
        $this->smtp = $this->connection->prepare($query);
        $this->smtp->execute($values);
        return $this->QueryHandler->is_void($this->smtp) ? new DataNotFoundException :  new DataHandler($this->smtp);
    }
    public function where($value)
    {
        $this->query .= " WHERE $value";
        return $this;
    }
    public function like($value)
    {
        $this->query .= " LIKE ?";
        $this->params[] = $value;
        return $this;
    }
    public function equal($value)
    {
        $this->query .= " = ?";
        $this->params[] = $value;
        return $this;
    }
    public function and($column)
    {
        $this->query .= " AND $column";
        return $this;
    }
    public function select(array $columns = null)
    {
        $this->attributes = $columns;
        $this->query = "SELECT ";
        $this->query .= is_array($this->attributes) ? implode(', ', $this->attributes) : "* ";
        $this->query .= " FROM $this->table";
        return $this;
    }
    public function all()
    {
        return $this->query($this->query, $this->params ?? null);
    }
    public function is_connected()
    {
        return $this->DB;
    }
}
