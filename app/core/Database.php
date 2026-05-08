<?php

namespace App\Core;
use \PDO;
use \PDOStatement;

// Database class: handles connection + query execution
class Database {

    // PDO connection instance
    private PDO $connection;

    // Stores the last executed statement
    private PDOStatement $statement;

    /**
     * Constructor: establishes database connection
     *
     * @param array $dbConfig  Example:
     * [
     *   'host' => '127.0.0.1',
     *   'port' => 3306,
     *   'dbname' => 'auth',
     *   'charset' => 'utf8mb4'
     * ]
     *
     * @param ?string $username  DB username (optional)
     * @param ?string $password  DB password (optional)
     */
    public function __construct(array $dbConfig, ?string $username = null, ?string $password = null) {

        // If username is null, try to get from environment, otherwise use empty string
        $username ??= $_ENV['DB_USERNAME'] ?? '';

        // Same logic for password
        $password ??= $_ENV['DB_PASSWORD'] ?? '';

        if (isset($dbConfig['database'])) {
            $dbConfig = $dbConfig['database'];
        }

        // Build DSN string dynamically from config array
        // Result example: mysql:host=127.0.0.1;port=3306;dbname=auth;charset=utf8mb4
        $dsn = 'mysql:' . http_build_query($dbConfig, '', ';');

        // Create PDO connection
        $this->connection = new PDO($dsn, $username, $password, [
            // Throw exceptions on error
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Fetch results as associative array by default
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    /**
     * Prepares and executes a SQL query
     *
     * @param string $query   SQL query with placeholders
     * @param array $params   Values to bind with prepared query
     *
     * @return self           Allows method chaining
     */
    public function query(string $query, array $params = []): self {

        // Prepare SQL statement (prevents SQL injection)
        $statement = $this->connection->prepare($query);

        // Execute query with parameters
        $statement->execute($params);

        // Store statement for later fetch operations
        $this->statement = $statement;

        return $this; // Enables chaining like ->query()->get()
    }

    /**
     * Fetch all records from last executed query
     *
     * @return array
     */
    public function get() {
        return $this->statement->fetchAll();
    }

    /**
     * Fetch a single record from last executed query
     *
     * @return array|false
     */
    public function find() {
        return $this->statement->fetch();
    }
}
