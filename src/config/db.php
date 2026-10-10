<?php
// ============================================================
// Remedy Investment — Database Connection Handler (PDO)
// Auto-detects & auto-seeds database on first run
// ============================================================

require_once __DIR__ . '/config.php';

class DB {
    private static ?PDO $instance = null;
    private static bool $checkedMigration = false;

    public static function getConnection(): ?PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // If database does not exist, try to create it if possible
                try {
                    $rootDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
                    $tempPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                    $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                    self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                } catch (Exception $inner) {
                    if (APP_DEBUG) {
                        error_log("Database connection error: " . $e->getMessage());
                    }
                    return null;
                }
            }
        }

        // Auto-run schema migration if tables don't exist yet (Zero-stress deployment!)
        if (self::$instance !== null && !self::$checkedMigration) {
            self::$checkedMigration = true;
            try {
                $stmt = self::$instance->query("SHOW TABLES LIKE 'users'");
                if ($stmt && $stmt->rowCount() === 0) {
                    $schemaFile = __DIR__ . '/../../database/schema.sql';
                    if (file_exists($schemaFile)) {
                        $sql = file_get_contents($schemaFile);
                        self::$instance->exec($sql);
                    }
                }
            } catch (Exception $e) {
                error_log("Schema auto-migration check: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Helper to run queries with parameter binding
     */
    public static function query(string $sql, array $params = []): ?PDOStatement {
        $pdo = self::getConnection();
        if (!$pdo) return null;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch single row
     */
    public static function fetch(string $sql, array $params = []): ?array {
        $stmt = self::query($sql, $params);
        if (!$stmt) return null;
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows
     */
    public static function fetchAll(string $sql, array $params = []): array {
        $stmt = self::query($sql, $params);
        if (!$stmt) return [];
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Insert/Update/Delete and return last inserted ID or affected rows
     */
    public static function execute(string $sql, array $params = []): int {
        $pdo = self::getConnection();
        if (!$pdo) return 0;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function lastInsertId(): string|false {
        $pdo = self::getConnection();
        return $pdo ? $pdo->lastInsertId() : false;
    }
}
