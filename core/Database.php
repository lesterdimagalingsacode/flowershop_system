<?php
// ─────────────────────────────────────────────
//  core/Database.php — PDO Singleton
//  • All queries use prepared statements (SQL injection prevention)
//  • Atomic operations via beginTransaction / commit / rollback
// ─────────────────────────────────────────────

declare(strict_types=1);

class Database {

    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST, DB_PORT, DB_NAME
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,   // real prepared statements
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci; SET time_zone = '+08:00'",
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->pdo->exec("SET time_zone = '+08:00'"); // ← add this
        } catch (PDOException $e) {
            // Never expose credentials in production
            $msg = APP_DEBUG
                ? 'Database connection failed: ' . $e->getMessage()
                : 'Database connection failed. Please try again later.';
            throw new RuntimeException($msg);
        }
    }

    // ── Singleton accessor ────────────────────
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }

    // ── Query helpers ─────────────────────────

    /**
     * Run a SELECT query and return all rows.
     */
    public function query(string $sql, array $params = []): array {
        $stmt = $this->prepare($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Run a SELECT query and return a single row.
     */
    public function queryOne(string $sql, array $params = []): array|false {
        $stmt = $this->prepare($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Run an INSERT / UPDATE / DELETE and return affected row count.
     */
    public function execute(string $sql, array $params = []): int {
        $stmt = $this->prepare($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Run an INSERT and return the new row's ID.
     */
    public function insert(string $sql, array $params = []): string {
        $this->prepare($sql, $params);
        return $this->pdo->lastInsertId();
    }

    // ── Atomic / Transaction helpers ──────────

    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    public function commit(): void {
        $this->pdo->commit();
    }

    public function rollback(): void {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    /**
     * Execute a callable inside a transaction.
     * Auto-commits on success, auto-rolls-back on exception.
     */
    public function transaction(callable $callback): mixed {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    // ── Internal ──────────────────────────────

    private function prepare(string $sql, array $params): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Prevent cloning and unserialization (singleton safety)
    private function __clone() {}
    public function __wakeup() {
        throw new RuntimeException('Cannot unserialize a singleton.');
    }
}
