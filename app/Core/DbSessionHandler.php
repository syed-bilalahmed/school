<?php
/**
 * DbSessionHandler — Distributed Database Session Driver for Multi-Server Load Balancing.
 *
 * Implements native PHP SessionHandlerInterface to store sessions in a centralized MySQL database.
 * When traffic is distributed across multiple load-balanced web servers (Web1, Web2, Web3...),
 * all servers read/write sessions to the shared database, preventing users from being logged out.
 */
class DbSessionHandler implements SessionHandlerInterface {

    private ?PDO $pdo = null;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? Database::getWritePdo();
    }

    public function open(string $path, string $name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $id): string|false {
        try {
            $stmt = $this->pdo->prepare("SELECT payload FROM sessions WHERE id = :id AND last_activity >= :expiry LIMIT 1");
            $lifetime = (int) ini_get('session.gc_maxlifetime') ?: 86400;
            $stmt->execute([
                ':id' => $id,
                ':expiry' => time() - $lifetime
            ]);
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            return $row ? (string)$row->payload : '';
        } catch (Throwable $e) {
            error_log('[Session DB Read] ' . $e->getMessage());
            return '';
        }
    }

    public function write(string $id, string $data): bool {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
            $now = time();

            $sql = "INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity)
                    VALUES (:id, :user_id, :ip, :ua, :data, :act)
                    ON DUPLICATE KEY UPDATE
                        user_id = VALUES(user_id),
                        ip_address = VALUES(ip_address),
                        user_agent = VALUES(user_agent),
                        payload = VALUES(payload),
                        last_activity = VALUES(last_activity)";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':user_id' => $userId,
                ':ip' => $ipAddress,
                ':ua' => $userAgent,
                ':data' => $data,
                ':act' => $now
            ]);
        } catch (Throwable $e) {
            error_log('[Session DB Write] ' . $e->getMessage());
            return false;
        }
    }

    public function destroy(string $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Throwable $e) {
            error_log('[Session DB Destroy] ' . $e->getMessage());
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE last_activity < :threshold");
            $stmt->execute([':threshold' => time() - $max_lifetime]);
            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log('[Session DB GC] ' . $e->getMessage());
            return 0;
        }
    }
}
