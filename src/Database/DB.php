<?php

namespace Bravo\ORM;

use PDO;
use Bravo\ORM\DatabseEnv;

/**
 * This class handles the database connection
 * 
 * @author Emilio Bravo
 */
class DB extends PDO
{

    use handlesExceptions;

    /**
     * The database driver
     * 
     * @var string
     */
    protected string $driver;

    /**
     * The current server
     * 
     * @var string
     */
    protected string $server;

    /**
     * The database connection port
     * 
     * @var int|string
     */
    protected int|string $port;

    /**
     * The database sqlite path
     * 
     * @var string
     */
    protected string $sqlitePath;

    /**
     * The database user
     * 
     * @var string
     */
    protected string $user;

    /**
     * The database password
     * 
     * @var string
     */
    protected string $password;

    /**
     * The database name
     * 
     * @var string
     */
    protected string $db;

    /**
     * The databse charset
     * 
     * @var string
     */
    protected string $charset;

    /**
     * The database error handler
     * 
     * @var int
     */
    protected int $errorMode = 0;

    /**
     * Supported drivers
     * 
     * @var array
     */
    protected array $supportedDrivers = [
        'postgresql', 'mysql', 'sqlite'
    ];

    /** 
     * Sets the database enviroment
     * 
     * @return null|PDO
     */
    public function __construct()
    {
        $this->driver = strtolower(DatabseEnv::DATABASE_DRIVER);
        $this->server = DatabseEnv::DATABASE_SERVER;
        $this->port = DatabseEnv::DATABASE_PORT;
        $this->sqlitePath = DatabseEnv::SQLITE_PATH;
        $this->user = DatabseEnv::DATABASE_USER;
        $this->password = DatabseEnv::DATABASE_PASSWORD;
        $this->db = DatabseEnv::DATABASE_NAME;
        $this->charset = DatabseEnv::DATABASE_CHARSET;

        return $this->setUp();
    }

    /**
     * Sets the database 
     * 
     * @return null|PDO
     */
    protected function setUp(): ?PDO
    {
        try {
            $this->setErrorHandling();
            return $this->setDriver();
        } catch (\PDOException $e) {
            $this->debug($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->debug($e->getMessage());
        }
    }

    /**
     * Sets the database driver
     * 
     * @return null|PDO
     * @throws \RuntimeException
     */
    public function setDriver(): ?PDO
    {
        if (!\in_array($this->driver, $this->supportedDrivers)) {

            throw new \RuntimeException(

                sprintf(
                    '(%s) is not a supported database driver, supported (%s)',
                    $this->driver,
                    implode(', ', $this->supportedDrivers)
                )
            );
        }

        $dsn = \call_user_func([$this,  "{$this->driver}DSN"]);

        return parent::__construct(
            $dsn,
            $this->user,
            $this->password,
            [PDO::ATTR_ERRMODE => $this->errorMode]
        );
    }

    /**
     * Sets the database error mode
     * 
     * @return void
     */
    public function setErrorHandling(): void
    {
        switch ($this->errorMode) {
            case 'exception':
                $this->errorMode = PDO::ERRMODE_EXCEPTION;
                break;
            case 'warning':
                $this->errorMode = PDO::ERRMODE_WARNING;
                break;
            default:
                $this->errorMode = PDO::ERRMODE_SILENT;
        }
    }

    /**
     * Returns postgresql dsn
     * 
     * @return string
     */
    protected function postgresqlDSN(): string
    {
        return "$this->driver:host=$this->server; dbname=$this->db; port=$this->port; charset=$this->charset";
    }

    /**
     * Returns mysql dsn
     * 
     * @return string
     */
    protected function mysqlDSN(): string
    {
        return "$this->driver:host=$this->server; dbname=$this->db; charset=$this->charset";
    }

    /**
     * Returns sqlite dsn
     * 
     * @return string
     */
    protected function sqliteDSN(): string
    {
        return "$this->driver:$this->sqlitePath";
    }
}
