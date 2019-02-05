<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 26/07/2018
 * Time: 01:41 PM
 */

namespace DFW;

use Illuminate\Database\Connection;
use DFW\DatabaseManager;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PDOStatement;

/**
 * Class Database
 * @package DFW\Database
 */
class Database{

    /**
     * @var \Illuminate\Database\Connection
     */
    private $connection;

    /**
     * Database constructor.
     * select or create a connection to database
     * @param string|connection $connection
     */
    function __construct($connection = "default"){
        if(gettype($connection) == "string"){
            $this->connection = DatabaseManager::$CAPSULE->getConnection($connection);
        }else{
            $this->connection = $connection;
        }
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param  string  $table
     * @return \Illuminate\Database\Query\Builder
     */
    function table($table){
        return $this->connection->table($table);
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    function getConnection(){
        return $this->connection;
    }

    /**
     * @param $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true){
        return $this->getConnection()->select($query,$bindings,$useReadPdo);
    }

    /**
     * @param $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array
     */
    public function sQuery($sql, $bindings = []){
        return $this->getConnection()->select($sql,$bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function update($query, $bindings = []){
        return $this->getConnection()->update($query, $bindings);
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function insert($query, $bindings = []){
        return $this->getConnection()->insert($query, $bindings);
    }


    /**
     * Run an delete statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function delete($query, $bindings = []){
        return $this->getConnection()->delete($query, $bindings);
    }

    /**
     * @param $sql
     * @param array $option
     * @return PDOStatement|bool If the database server successfully prepares the statement,
     * <b>PDO::prepare</b> returns a
     * <b>PDOStatement</b> object.
     * If the database server cannot successfully prepare the statement,
     * <b>PDO::prepare</b> returns <b>FALSE</b> or emits
     * <b>PDOException</b> (depending on error handling).
     * </p>
     * <p>
     * Emulated prepared statements does not communicate with the database server
     * so <b>PDO::prepare</b> does not check the statement.
     */
    public function prepare($sql,$option = []){
        return $this->getConnection()->getPdo()->prepare($sql,$option);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function queryBuilder(){
        return new QueryBuilder(
            $this->getConnection(), $this->getConnection()->getQueryGrammar(), $this->getConnection()->getPostProcessor()
        );
    }

    public function getPdo(){
        return $this->getConnection()->getPdo();
    }


    /**
     * Devuelve el estado de error de la ultima sentencia SQL ejecutada o null en caso de no haber error
     */
    function getError(){
        return $this->getConnection()->getPdo()->errorInfo()[2];
    }


    /**
     * @return \Illuminate\Database\Schema\Builder
     */
    function getSchema(){
        return $this->getConnection()->getSchemaBuilder();
    }

    /**
     * Función que obtiene el valor del último id insertado en la base de datos
     * @return mixed devuelve el valor del último id insertado
     * @since 2.0.0
     */
    function getLastId(){
        return $this->getConnection()->getPdo()->lastInsertId();
    }

}