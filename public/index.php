<?php
// ============================================================
// Remedy Investment — Central Dispatcher / Router
// ============================================================

// If running in PHP built-in server and file exists, serve directly
if (php_sapi_name() === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
        return false;
    }
}

require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/helpers/Auth.php';
require_once __DIR__ . '/../src/helpers/Csrf.php';
require_once __DIR__ . '/../src/helpers/Flash.php';
require_once __DIR__ . '/../src/helpers/Redirect.php';
require_once __DIR__ . '/../src/helpers/Crypto.php';

// Check DB connectivity
$pdo = DB::getConnection();
if ($pdo === null) {
    // Show database installation / setup guidance screen
    require_once __DIR__ . '/setup_db.php';
    exit;
}

// Controllers
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/DashboardController.php';
require_once __DIR__ . '/../src/controllers/TaskController.php';
require_once __DIR__ . '/../src/controllers/FinanceController.php';
require_once __DIR__ . '/../src/controllers/ReferralController.php';
require_once __DIR__ . '/../src/controllers/ProfileController.php';
require_once __DIR__ . '/../src/controllers/AdminController.php';

// Determine URI / Route
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip script directory path if hosted in a subdirectory (e.g., /Remedy/public)
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir !== '/' && str_starts_with($requestUri, $scriptDir)) {
    $route = substr($requestUri, strlen($scriptDir));
} else {
    $route = $requestUri;
}

$route = trim($route, '/');
// Also allow query parameter override: ?route=dashboard
if (isset($_GET['route'])) {
    $route = trim($_GET['route'], '/');
}

$method = $_SERVER['REQUEST_METHOD'];

// Route Dispatch
switch ($route) {
    case '':
        if (Auth::check()) {
            Redirect::to('dashboard');
        } else {
            (new AuthController())->showLogin();
        }
        break;

    // ── Auth Routes ──
    case 'login':
        $c = new AuthController();
        $method === 'POST' ? $c->login() : $c->showLogin();
        break;

    case 'register':
        $c = new AuthController();
        $method === 'POST' ? $c->register() : $c->showRegister();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'forgot-password':
        $c = new AuthController();
        $method === 'POST' ? $c->forgotPassword() : $c->showForgotPassword();
        break;

    case 'reset-password':
        $c = new AuthController();
        $method === 'POST' ? $c->resetPassword() : $c->showResetPassword();
        break;

    // ── Dashboard Routes ──
    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'tasks':
        (new TaskController())->index();
        break;

    case 'tasks/do':
        (new TaskController())->showDo();
        break;

    case 'tasks/submit':
        (new TaskController())->submit();
        break;

    case 'wallet':
        (new FinanceController())->wallet();
        break;

    case 'deposit':
        $c = new FinanceController();
        $method === 'POST' ? $c->submitDeposit() : $c->showDeposit();
        break;

    case 'withdraw':
        $c = new FinanceController();
        $method === 'POST' ? $c->submitWithdraw() : $c->showWithdraw();
        break;

    case 'transactions':
        (new FinanceController())->transactions();
        break;

    case 'referral':
        (new ReferralController())->index();
        break;

    case 'profile':
        (new ProfileController())->index();
        break;

    case 'profile/update':
        (new ProfileController())->updateProfile();
        break;

    case 'profile/password':
        (new ProfileController())->updatePassword();
        break;

    case 'notifications':
        (new ProfileController())->notifications();
        break;

    // ── Admin Routes ──
    case 'admin':
        (new AdminController())->index();
        break;

    case 'admin/users':
        (new AdminController())->users();
        break;

    case 'admin/users/toggle-status':
        (new AdminController())->toggleUserStatus();
        break;

    case 'admin/users/balance':
        (new AdminController())->adjustUserBalance();
        break;

    case 'admin/tasks':
        (new AdminController())->tasks();
        break;

    case 'admin/tasks/save':
        (new AdminController())->saveTask();
        break;

    case 'admin/tasks/delete':
        (new AdminController())->deleteTask();
        break;

    case 'admin/submissions':
        (new AdminController())->submissions();
        break;

    case 'admin/submissions/process':
        (new AdminController())->processSubmission();
        break;

    case 'admin/deposits':
        (new AdminController())->deposits();
        break;

    case 'admin/deposits/process':
        (new AdminController())->processDeposit();
        break;

    case 'admin/withdrawals':
        (new AdminController())->withdrawals();
        break;

    case 'admin/withdrawals/process':
        (new AdminController())->processWithdrawal();
        break;

    case 'admin/transactions':
        (new AdminController())->transactions();
        break;

    case 'admin/settings':
        (new AdminController())->settings();
        break;

    case 'admin/settings/save':
        (new AdminController())->saveSettings();
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/404.php';
        break;
}
