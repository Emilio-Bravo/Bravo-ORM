<?php

namespace Bravo\ORM;

use PDO;
use Bravo\ORM\ENV\DatabseEnv;

/**
 * This class handles the database connection
 * @author Emilio Bravo
 */

class DB extends PDO
{

    private $driver;
    private $server;
    private $port;
    private $sqlitePath;
    private $user;
    private $password;
    private $db;
    private $charset;
    private $errorMode;

    /** 
     * Sets the database enviroment
     * @return void
     */

    public function __construct()
    {
        $this->driver = DatabseEnv::DATABASE_DRIVER;
        $this->server = DatabseEnv::DATABASE_SERVER;
        $this->port = DatabseEnv::DATABASE_PORT;
        $this->sqlitePath = DatabseEnv::SQLITE_PATH;
        $this->user = DatabseEnv::DATABASE_USER;
        $this->password = DatabseEnv::DATABASE_PASSWORD;
        $this->db = DatabseEnv::DATABASE_NAME;
        $this->charset = DatabseEnv::DATABASE_CHARSET;
        $this->errorMode = DatabseEnv::DATABASE_ERROR_HANDLING;
        $this->setUp();
    }

    /**
     * Sets the database 
     * @return void
     */

    private function setUp(): void
    {
        try {
            $this->setErrMode();
            $this->setDriver();
        } catch (\PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Sets the database driver
     */

    public function setDriver(): ?\PDO
    {
        switch ($this->driver) {
            case 'psql' || 'postgres':
                return parent::__construct($this->postgreSqlConnection(), $this->user, $this->password, $this->errorMode);
            case 'mysql':
                return parent::__construct($this->mysqlConnection(), $this->user, $this->password, $this->errorMode);
            case 'sqlite':
                return parent::__construct($this->sqliteConnection());
        }
    }
    /**
     * Sets the database error mode
     * @return void
     */

    public function setErrMode(): void
    {
        switch ($this->errorMode) {
            case 'exception':
                $this->errorMode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
                break;
            case 'warning':
                $this->errorMode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING];
                break;
            case 'silent' || 'none':
                $this->errorMode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
                break;
        }
    }

    /**
     * Returns an appropiate postgresql connection instruction
     * @return mixed
     */

    protected function postgreSqlConnection(): string
    {
        return "$this->driver:host=$this->server;dbname=$this->db;port=$this->port;charset=$this->charset";
    }

    /**
     * Returns an appropiate mysql connection instruction
     * @return mixed
     */

    protected function mysqlConnection(): string
    {
        return "$this->driver:host=$this->server;dbname=$this->db;charset=$this->charset";
    }

    /**
     * Returns an appropiate sqlite connection instruction
     * @return mixed
     */

    protected function sqliteConnection(): string
    {
        return "$this->driver:$this->sqlitePath";
    }
}
