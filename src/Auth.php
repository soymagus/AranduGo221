<?php
declare(strict_types=1);

namespace AranduGo;

final class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        session_name('arandugo_session');
        session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']), 'samesite' => 'Lax', 'path' => '/']);
        session_start();
    }

    public static function attempt(string $login, string $password): bool
    {
        $pdo = Database::connection();
        $table = Database::table('users');
        $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . Support::config()['app']['key']);
        $cutoff = date('Y-m-d H:i:s', time() - 900);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE last_ip_hash = ? AND failed_at >= ? AND failed_attempts >= 5");
        $stmt->execute([$ipHash, $cutoff]);
        if ((int) $stmt->fetchColumn() > 0) return false;

        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE (username = ? OR email = ?) AND active = 1 LIMIT 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            if ($user) {
                $pdo->prepare("UPDATE {$table} SET failed_attempts=failed_attempts+1, failed_at=NOW(), last_ip_hash=? WHERE id=?")->execute([$ipHash, $user['id']]);
            }
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id']; $_SESSION['role'] = $user['role']; $_SESSION['name'] = $user['name'];
        $pdo->prepare("UPDATE {$table} SET failed_attempts=0, failed_at=NULL, last_login_at=NOW(), last_ip_hash=? WHERE id=?")->execute([$ipHash, $user['id']]);
        return true;
    }

    public static function check(): bool { return !empty($_SESSION['user_id']); }
    public static function requireLogin(): void { if (!self::check()) Support::redirect('dashboardcliente/login.php'); }
    public static function isAdmin(): bool { return ($_SESSION['role'] ?? '') === 'admin'; }
    public static function isCollaborator(): bool { return ($_SESSION['role'] ?? '') === 'collaborator'; }
    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) Support::json(['ok'=>false,'error'=>'Solo un administrador puede realizar esta acción.'],403);
    }
    public static function logout(): void { $_SESSION = []; if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']); } session_destroy(); }
}
