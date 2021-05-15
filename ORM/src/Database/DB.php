<?php

namespace Bravo\ORM;

use PDO;
use PDOException;
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
     * Set's the database enviroment
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
     * Set's the database 
     * @return void
     */

    private function setUp()
    {
        try {
            $this->setErrMode();
            $this->setDriver();
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Set's the database driver
     */

    public function setDriver()
    {
        switch ($this->driver) {
            case 'psql' || 'postgres':
                return parent::__construct($this->postgreSqlConnection());
                break;
            case 'mysql':
                return parent::__construct($this->mysqlConnection(), $this->user, $this->password, $this->errorMode);
                break;
            case 'sqlite':
                return parent::__construct($this->sqliteConnection(), $this->errorMode);
                break;
        }
    }
    /**
     * Set's the database error mode
     * @return void
     */

    public function setErrMode()
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

    protected function postgreSqlConnection()
    {
        return "$this->driver:host=$this->server;dbname=$this->db;port=$this->port;charset=$this->charset;user=$this->user;password=$this->password";
    }

    /**
     * Returns an appropiate mysql connection instruction
     * @return mixed
     */

    protected function mysqlConnection()
    {
        return "$this->driver:host=$this->server;dbname=$this->db;charset=$this->charset";
    }

    /**
     * Returns an appropiate sqlite connection instruction
     * @return mixed
     */

    protected function sqliteConnection()
    {
        return "$this->driver:$this->sqlitePath";
    }
}
