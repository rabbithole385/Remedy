<?php
// ============================================================
// Remedy Investment — Task Model
// ============================================================

require_once __DIR__ . '/../config/db.php';

class Task {
    public static function findById(int $id): ?array {
        return DB::fetch("SELECT * FROM tasks WHERE id = ?", [$id]);
    }

    public static function getActive(int $userId): array {
        // Retrieve tasks that this user has not yet completed or submitted
        $sql = "SELECT t.*, 
                       ts.id as submission_id,
                       ts.status as submission_status
                FROM tasks t
                LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.user_id = ?
                WHERE t.status = 'active'
                ORDER BY t.created_at DESC";
        return DB::fetchAll($sql, [$userId]);
    }

    public static function getAllAdmin(): array {
        return DB::fetchAll("SELECT * FROM tasks ORDER BY created_at DESC");
    }

    public static function create(array $data): int|false {
        $sql = "INSERT INTO tasks (title, description, instructions, type, reward, thumbnail, target_url, max_submissions, status, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = DB::query($sql, [
            $data['title'],
            $data['description'],
            $data['instructions'] ?? '',
            $data['type'] ?? 'other',
            $data['reward'] ?? 0.00,
            $data['thumbnail'] ?? null,
            $data['target_url'] ?? null,
            !empty($data['max_submissions']) ? (int)$data['max_submissions'] : null,
            $data['status'] ?? 'active',
            $data['created_by'] ?? null,
        ]);
        return $stmt ? (int)DB::lastInsertId() : false;
    }

    public static function update(int $id, array $data): bool {
        $sql = "UPDATE tasks SET title = ?, description = ?, instructions = ?, type = ?, reward = ?, 
                       thumbnail = COALESCE(?, thumbnail), target_url = ?, max_submissions = ?, status = ?
                WHERE id = ?";
        return DB::execute($sql, [
            $data['title'],
            $data['description'],
            $data['instructions'] ?? '',
            $data['type'] ?? 'other',
            $data['reward'],
            $data['thumbnail'] ?? null,
            $data['target_url'] ?? null,
            !empty($data['max_submissions']) ? (int)$data['max_submissions'] : null,
            $data['status'] ?? 'active',
            $id
        ]) > 0;
    }

    public static function delete(int $id): bool {
        return DB::execute("DELETE FROM tasks WHERE id = ?", [$id]) > 0;
    }

    public static function submit(int $userId, int $taskId, string $proofType, string $proofValue, ?string $notes = null): bool {
        // Prevent duplicate submission
        $existing = DB::fetch("SELECT id FROM task_submissions WHERE user_id = ? AND task_id = ?", [$userId, $taskId]);
        if ($existing) return false;

        $sql = "INSERT INTO task_submissions (user_id, task_id, proof_type, proof_value, notes, status)
                VALUES (?, ?, ?, ?, ?, 'pending')";
        $res = DB::execute($sql, [$userId, $taskId, $proofType, $proofValue, $notes]);
        if ($res > 0) {
            DB::execute("UPDATE tasks SET total_submissions = total_submissions + 1 WHERE id = ?", [$taskId]);
            return true;
        }
        return false;
    }

    public static function getUserSubmissions(int $userId): array {
        $sql = "SELECT ts.*, t.title as task_title, t.reward as task_reward, t.type as task_type
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE ts.user_id = ?
                ORDER BY ts.created_at DESC";
        return DB::fetchAll($sql, [$userId]);
    }

    public static function getAllSubmissions(?string $status = null): array {
        if ($status) {
            $sql = "SELECT ts.*, t.title as task_title, t.reward as task_reward, u.username, u.email
                    FROM task_submissions ts
                    JOIN tasks t ON ts.task_id = t.id
                    JOIN users u ON ts.user_id = u.id
                    WHERE ts.status = ?
                    ORDER BY ts.created_at DESC";
            return DB::fetchAll($sql, [$status]);
        }
        $sql = "SELECT ts.*, t.title as task_title, t.reward as task_reward, u.username, u.email
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                JOIN users u ON ts.user_id = u.id
                ORDER BY ts.created_at DESC";
        return DB::fetchAll($sql);
    }

    public static function getSubmissionById(int $id): ?array {
        $sql = "SELECT ts.*, t.title as task_title, t.reward as task_reward, u.username, u.email, u.id as user_id
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                JOIN users u ON ts.user_id = u.id
                WHERE ts.id = ?";
        return DB::fetch($sql, [$id]);
    }
}
