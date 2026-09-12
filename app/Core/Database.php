<?php
/**
 * Database — PDO wrapper with per-request singleton connection.
 *
 * Singleton pattern: the PDO connection is opened once per PHP process/request
 * and reused by ALL models. This eliminates the overhead of opening a new
 * MySQL connection for every `new Database()` call.
 *
 * ATTR_PERSISTENT = true: PHP reuses MySQL threads across requests via the
 * connection pool, significantly reducing connect latency under high load.
 */
class Database {

    /** @var PDO|null Shared PDO instance for this request */
    private static ?PDO $sharedPdo = null;

    private $stmt;

    public function __construct() {
        if (self::$sharedPdo === null) {
            self::$sharedPdo = self::createConnection();
        }
    }

    /**
     * Create the underlying PDO connection.
     * Called only once per PHP process (singleton gate in __construct).
     */
    private static function createConnection(): PDO {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );

        $options = [
            // Request-level Singleton already reuses connection; persistent=false avoids handle corruption on Windows
            PDO::ATTR_PERSISTENT          => false,
            // Always throw exceptions (never silent failures)
            PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
            // Return rows as stdClass objects by default
            PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_OBJ,
            // Initialise session charset
            PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('[DB] Connection failed: ' . $e->getMessage());
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(503);
                echo json_encode(['success' => false, 'error' => 'Database unavailable. Please try again shortly.']);
            } else {
                http_response_code(503);
                echo '<h2 style="font-family:sans-serif;color:#dc3545;text-align:center;padding:60px">Database unavailable. Please try again shortly.</h2>';
            }
            exit;
        }
    }

    /**
     * Expose the raw PDO instance (for transactions, ping checks, etc.)
     */
    public static function getPdo(): ?PDO {
        return self::$sharedPdo;
    }

    /**
     * Reset the singleton (used in health checks / tests only).
     */
    public static function resetConnection(): void {
        self::$sharedPdo = null;
    }

    // -------------------------------------------------------------------------
    // Query API (identical to original — all existing code works unchanged)
    // -------------------------------------------------------------------------

    /** Prepare a SQL statement */
    public function query(string $sql): void {
        $this->stmt = self::$sharedPdo->prepare($sql);
    }

    /** Bind a parameter value */
    public function bind($param, $value, $type = null): void {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):   $type = PDO::PARAM_INT;  break;
                case is_bool($value):  $type = PDO::PARAM_BOOL; break;
                case is_null($value):  $type = PDO::PARAM_NULL; break;
                default:               $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /** Execute the prepared statement */
    public function execute(): bool {
        return $this->stmt->execute();
    }

    /** Fetch all rows as array of objects */
    public function resultSet(): array {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /** Fetch a single row as object */
    public function single(): mixed {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    /** Affected / selected row count */
    public function rowCount(): int {
        return $this->stmt->rowCount();
    }

    /** Last inserted auto-increment ID */
    public function lastInsertId(): string|false {
        return self::$sharedPdo->lastInsertId();
    }
}
