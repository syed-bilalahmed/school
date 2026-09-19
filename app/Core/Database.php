<?php
/**
 * Database — High-Performance PDO wrapper with Read/Write Splitting & Load Balancing.
 *
 * Designed for 100,000+ Concurrent Users:
 * 1. Read/Write Splitting: Automatically balances SELECT queries across Read Replicas (DB_READ_HOSTS)
 *    while pinning INSERT, UPDATE, DELETE, and transactions to the Primary Master (DB_HOST).
 * 2. Failover Resilience: If a read replica node is unreachable, automatically falls back to Master.
 * 3. Connection Pooling / Singleton: Connections are reused per request process, avoiding socket exhaustion.
 * 4. Transaction Awareness: While inside an active transaction, all queries stay pinned to Master.
 */
class Database {

    /** @var PDO|null Master connection for writes and transactions */
    private static ?PDO $writePdo = null;

    /** @var PDO|null Read replica connection for read queries */
    private static ?PDO $readPdo = null;

    /** @var string|null The active read replica host selected for this request */
    private static ?string $activeReadHost = null;

    /** @var bool Flag indicating whether an active transaction is underway */
    private static bool $inTransaction = false;

    private $stmt;
    private ?PDO $activeStmtPdo = null;

    public function __construct() {
        if (self::$writePdo === null) {
            self::$writePdo = self::createConnection(DB_HOST);
        }
    }

    /**
     * Get or initialize the primary/write PDO instance.
     */
    public static function getWritePdo(): PDO {
        if (self::$writePdo === null) {
            self::$writePdo = self::createConnection(DB_HOST);
        }
        return self::$writePdo;
    }

    /**
     * Get or initialize the read replica PDO instance with load-balancing.
     */
    public static function getReadPdo(): PDO {
        if (self::$readPdo !== null) {
            return self::$readPdo;
        }

        // Check if read replica hosts are configured
        $replicaHosts = self::getReadReplicaHosts();
        if (empty($replicaHosts)) {
            // No replicas defined; fallback to Master
            self::$readPdo = self::getWritePdo();
            self::$activeReadHost = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
            return self::$readPdo;
        }

        // Randomly select one read replica to distribute traffic evenly (stateless load balancing)
        shuffle($replicaHosts);
        foreach ($replicaHosts as $host) {
            $host = trim($host);
            if (empty($host)) continue;
            try {
                self::$readPdo = self::createConnection($host);
                self::$activeReadHost = $host;
                return self::$readPdo;
            } catch (Throwable $e) {
                error_log("[DB LoadBalancer] Read replica {$host} failed, trying next: " . $e->getMessage());
            }
        }

        // If all replicas failed, failover gracefully to Master
        self::$readPdo = self::getWritePdo();
        self::$activeReadHost = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
        return self::$readPdo;
    }

    /**
     * Parse configured read replica hosts from constant or environment.
     */
    private static function getReadReplicaHosts(): array {
        if (defined('DB_READ_HOSTS')) {
            if (is_array(DB_READ_HOSTS)) {
                return DB_READ_HOSTS;
            }
            if (is_string(DB_READ_HOSTS) && trim(DB_READ_HOSTS) !== '') {
                return array_filter(array_map('trim', explode(',', DB_READ_HOSTS)));
            }
        }
        $env = getenv('DB_READ_HOSTS');
        if (!empty($env)) {
            return array_filter(array_map('trim', explode(',', $env)));
        }
        return [];
    }

    /**
     * Create an optimized PDO connection for a specific host.
     */
    private static function createConnection(string $host): PDO {
        $port = defined('DB_PORT') ? DB_PORT : 3306;
        $dbName = defined('DB_NAME') ? DB_NAME : '';
        $dbUser = defined('DB_USER') ? DB_USER : 'root';
        $dbPass = defined('DB_PASS') ? DB_PASS : '';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

        $options = [
            PDO::ATTR_PERSISTENT          => false,
            PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES    => true,
            PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::ATTR_TIMEOUT             => 3 // 3 second timeout to prevent thread blocking on failed nodes
        ];

        try {
            return new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            error_log("[DB] Connection failed to {$host}: " . $e->getMessage());
            // If primary host fails, send friendly 503
            if ($host === (defined('DB_HOST') ? DB_HOST : '127.0.0.1')) {
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(503);
                    echo json_encode(['success' => false, 'error' => 'Database cluster busy. Please try again shortly.']);
                } else {
                    http_response_code(503);
                    echo '<h2 style="font-family:sans-serif;color:#dc3545;text-align:center;padding:60px">Service temporarily busy. Please retry shortly.</h2>';
                }
                exit;
            }
            throw $e;
        }
    }

    /**
     * Expose the primary PDO instance (for transactions, direct queries).
     */
    public static function getPdo(): ?PDO {
        return self::getWritePdo();
    }

    /**
     * Reset singletons (used in health checks, worker reboots, unit tests).
     */
    public static function resetConnection(): void {
        self::$writePdo = null;
        self::$readPdo = null;
        self::$activeReadHost = null;
        self::$inTransaction = false;
    }

    /**
     * Returns name of currently active read replica host (useful for monitoring/telemetry).
     */
    public static function getActiveReadHost(): ?string {
        return self::$activeReadHost;
    }

    // -------------------------------------------------------------------------
    // Query API with Automatic Read/Write Splitting
    // -------------------------------------------------------------------------

    /**
     * Prepare a SQL statement on the appropriate node (Master vs Read Replica).
     */
    public function query(string $sql): void {
        $trimmed = ltrim($sql);

        // Detect if query is a read operation
        $isRead = (bool) preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i', $trimmed);

        // Locking queries must stay on Master
        $isLocking = (bool) preg_match('/\b(FOR\s+UPDATE|LOCK\s+IN\s+SHARE\s+MODE)\b/i', $trimmed);

        if ($isRead && !$isLocking && !self::$inTransaction) {
            $this->activeStmtPdo = self::getReadPdo();
        } else {
            $this->activeStmtPdo = self::getWritePdo();
        }

        $this->stmt = $this->activeStmtPdo->prepare($sql);
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

    /** Last inserted auto-increment ID from Primary DB */
    public function lastInsertId(): string|false {
        return self::getWritePdo()->lastInsertId();
    }

    // -------------------------------------------------------------------------
    // Transaction Support (Always executed on Primary Master)
    // -------------------------------------------------------------------------

    public static function beginTransaction(): bool {
        self::$inTransaction = true;
        return self::getWritePdo()->beginTransaction();
    }

    public static function commit(): bool {
        self::$inTransaction = false;
        return self::getWritePdo()->commit();
    }

    public static function rollBack(): bool {
        self::$inTransaction = false;
        return self::getWritePdo()->rollBack();
    }

    public static function inTransaction(): bool {
        return self::$inTransaction && self::getWritePdo()->inTransaction();
    }
}
