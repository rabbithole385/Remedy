<?php
// ============================================================
// Remedy Investment — Task Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Redirect.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Notification.php';

class TaskController {
    public function index(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $tasks = Task::getActive($user['id']);
        $submissions = Task::getUserSubmissions($user['id']);

        require_once __DIR__ . '/../../views/dashboard/tasks.php';
    }

    public function showDo(): void {
        Auth::requireAuth();
        $user = Auth::user();
        $taskId = (int)($_GET['id'] ?? 0);

        $task = Task::findById($taskId);
        if (!$task || $task['status'] !== 'active') {
            Flash::error('Task not found or currently inactive.');
            Redirect::to('tasks');
        }

        // Check if user already submitted
        $existing = DB::fetch("SELECT * FROM task_submissions WHERE user_id = ? AND task_id = ?", [$user['id'], $taskId]);
        if ($existing) {
            Flash::info('You have already submitted proof for this task. Current status: ' . ucfirst($existing['status']));
            Redirect::to('tasks');
        }

        require_once __DIR__ . '/../../views/dashboard/task-do.php';
    }

    public function submit(): void {
        Auth::requireAuth();
        $user = Auth::user();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('tasks');
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $task = Task::findById($taskId);
        if (!$task || $task['status'] !== 'active') {
            Flash::error('Invalid task.');
            Redirect::to('tasks');
        }

        $proofType = $_POST['proof_type'] ?? 'screenshot';
        $proofValue = '';
        $notes = trim($_POST['notes'] ?? '');

        if ($proofType === 'screenshot') {
            if (empty($_FILES['proof_file']) || $_FILES['proof_file']['error'] !== UPLOAD_ERR_OK) {
                Flash::error('Please upload a screenshot proof.');
                Redirect::to("tasks/do?id={$taskId}");
            }

            $file = $_FILES['proof_file'];
            if ($file['size'] > UPLOAD_MAX_SIZE) {
                Flash::error('Uploaded file exceeds maximum limit of 5MB.');
                Redirect::to("tasks/do?id={$taskId}");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, ALLOWED_IMAGES)) {
                Flash::error('Invalid image type. Please upload a JPEG, PNG, or WebP image.');
                Redirect::to("tasks/do?id={$taskId}");
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'task_' . $user['id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }

            $targetPath = UPLOAD_DIR . $filename;
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                Flash::error('Failed to save uploaded screenshot. Please try again.');
                Redirect::to("tasks/do?id={$taskId}");
            }

            $proofValue = 'uploads/' . $filename;
        } else {
            $proofValue = trim($_POST['proof_text'] ?? '');
            if (empty($proofValue)) {
                Flash::error('Please provide your verification link or proof text.');
                Redirect::to("tasks/do?id={$taskId}");
            }
        }

        $success = Task::submit($user['id'], $taskId, $proofType, $proofValue, $notes);
        if ($success) {
            Notification::create(
                $user['id'],
                'Task Submitted',
                "Your submission for '{$task['title']}' is under review by administrators.",
                'info',
                'tasks'
            );
            Flash::success("Proof submitted successfully! Your submission for '{$task['title']}' is pending review.");
            Redirect::to('tasks');
        } else {
            Flash::error('Could not submit task. You may have already submitted this task.');
            Redirect::to('tasks');
        }
    }
}
