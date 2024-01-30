<?php
declare(strict_types=1);

namespace Mepatek\ToteLib;

use Dibi\Connection;
use Dibi\Driver;
use Dibi\Exception;
use Dibi\Reflection\Database;
use Dibi\Reflection\Table;
use Dibi\Result;
use Dotenv\Dotenv;
use Nette\Utils\Html;
use PDO;

/**
 * Class ToteLibFacade
 * @package Mepatek\ToteLib
 */
class ToteLibFacade
{
    /** @var Dotenv */
    private $dotenv;
    /** @var Connection */
    private $dibi = null;
    /** @var PDO */
    private $pdo = null;

    /**
     * ToteLibFacade constructor.
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        $this->dotenv = Dotenv::createUnsafeImmutable($dir);
        $this->dotenv->load();
        $this->dotenv->required(['TOTE_SERVER', 'TOTE_USERNAME', 'TOTE_PASSWORD']);
    }

    /**
     * @return PDO
     * @see https://www.php.net/manual/en/ref.pdo-sqlsrv.php
     */
    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO(
                $this->getPdoDsn(),
                $this->getSqlUserName(),
                $this->getSqlPassword()
            );
        }
        return $this->pdo;
    }

    /**
     * Get Dibi Connection object
     *
     * @return Connection
     * @throws Exception
     * @see https://dibiphp.com/cs/documentation
     */
    public function getDibi(): Connection
    {
        if ($this->dibi === null) {
            $this->dibi = new Connection(
                $this->getDibiOptions()
            );
        }
        return $this->dibi;
    }

    /**
     * Get Dibi driver
     *
     * @return Driver
     * @throws Exception
     */
    public function getDriver(): Driver
    {
        return $this->getDibi()->getDriver();
    }

    /**
     * Get Dibi result from $sql and parameters
     *
     * @param string $sql
     * @param mixed $parameter_1
     * @param mixed $parameter_2
     * @param mixed $parameter_3
     * @param mixed $parameter_n
     * @return Result
     * @throws Exception
     */
    public function query(...$args): Result
    {
        return $this->getDibi()->query($args);
    }

    /**
     * Get Dibi Database reflection object.
     *
     * @return Database
     * @throws Exception
     */
    public function getDatabaseInfo(): Database
    {
        return $this->getDibi()->getDatabaseInfo();
    }

    /**
     * Get all Dibi Tables in connected database
     *
     * @return Table[]
     * @throws Exception
     */
    public function getTables(): array
    {
        return $this->getDatabaseInfo()->getTables();
    }

    /**
     * Get all table names in connected database
     *
     * @return string[]
     * @throws Exception
     */
    public function getTableNames(): array
    {
        return $this->getDatabaseInfo()->getTableNames();
    }

    /**
     * Get Dibi Result object with all data from table $table
     *
     * @param string $table
     * @return Result
     * @throws Exception
     */
    public function getTableResult(string $table): Result
    {
        return $this->query("SELECT * FROM " . $table);
    }


    /**
     * Get table Html with all data from table
     *
     * @param string $table
     * @return Html
     * @throws Exception
     * @see https://doc.nette.org/cs/3.0/html-elements
     */
    public function getFullTableAsHtmlTable(string $table): Html
    {
        $result = $this->getTableResult($table);
        $htmlTable = Html::el("table");
        foreach ($result as $row) {
            $tr = $htmlTable->create("tr");
            foreach ($row as $key => $value) {
                $tr->create("td")
                    ->addText($value)
                    ->setAttribute("title", $key);
            }
        }
        return $htmlTable;
    }

    /**
     * Get table Html with all data from query result
     *
     * @param string $sql
     * @param mixed $parameter_1
     * @param mixed $parameter_2
     * @param mixed $parameter_3
     * @param mixed $parameter_n
     * @return Html
     * @throws Exception
     * @see https://doc.nette.org/cs/3.0/html-elements
     */
    public function getQueryResultAsHtmlTable(...$arg): Html
    {
        $result = $this->query($arg);
        $htmlTable = Html::el("table");
        foreach ($result as $row) {
            $tr = $htmlTable->create("tr");
            foreach ($row as $key => $value) {
                $tr->create("td")
                    ->addText($value)
                    ->setAttribute("title", $key);
            }
        }
        return $htmlTable;
    }

    /***********************/
    /******* PRIVATE *******/
    /***********************/

    /**
     * @return array
     */
    private function getDibiOptions(): array
    {
        $options = [
            'driver' => 'sqlsrv',
            'host' => $this->getSqlServer(),
            'username' => $this->getSqlUserName(),
            'password' => $this->getSqlPassword(),
        ];
        if (($database = $this->getSqlDatabase())) {
            $options["database"] = $database;
        }
        return $options;
    }

    /**
     * @return string
     */
    private function getPdoDsn(): string
    {
        $dsn = "sqlsrv:Server=" . $this->getSqlServer();
        if (($database = $this->getSqlDatabase())) {
            $dsn .= ";Database=" . $database;
        }
        return $dsn;
    }

    /**
     * @return string
     */
    private function getSqlServer(): string
    {
        return getenv('TOTE_SERVER');
    }

    /**
     * @return string
     */
    private function getSqlUserName(): string
    {
        return getenv('TOTE_USERNAME');
    }

    /**
     * @return string
     */
    private function getSqlPassword(): string
    {
        return getenv('TOTE_PASSWORD');
    }

    /**
     * @return string
     */
    private function getSqlDatabase(): ?string
    {
        $database = getenv('TOTE_DATABASE');
        return $database ?: null;
    }
}
