<?php
// services/AuthService.php
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/JwtService.php';

class AuthService {
    private User $userModel;
    private JwtService $jwt;

    // Adjust if you run behind a domain (leave '' for localhost)
    private string $cookieDomain = '';
    private string $cookiePath   = '/';
    // NOTE: Classic setcookie() cannot set SameSite directly on older PHP; omit for max compatibility.
    private bool   $forceSecureCookies;

    public function __construct() {
        $this->userModel = new User();
        $this->jwt = new JwtService();
        $this->forceSecureCookies = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

        // Set session cookie parameters (classic syntax for older PHP)
        if (session_status() === PHP_SESSION_NONE) {
            // lifetime=0 (session cookie), path, domain, secure, httponly
            @session_set_cookie_params(
                0,
                $this->cookiePath,
                $this->cookieDomain,
                $this->forceSecureCookies,
                true
            );
            session_start();
        }
    }

    public function register(string $name, string $email, string $password, string $role): array {
        $name  = trim($name);
        $email = strtolower(trim($email));

        if (!preg_match('/@(std\.uwu\.ac\.lk|uwu\.ac\.lk)$/', $email)) {
            return ['ok'=>false, 'message'=>'Use a valid university email'];
        }
        if (!in_array($role, ['member','manager'], true)) {
            return ['ok'=>false, 'message'=>'Invalid role'];
        }
        if (strlen($name) < 2) {
            return ['ok'=>false, 'message'=>'Name is too short'];
        }
        if (!(preg_match('/[A-Z]/', $password) && preg_match('/\d/', $password) && strlen($password) >= 8)) {
            return ['ok'=>false, 'message'=>'Weak password'];
        }

        return $this->userModel->create($name, $email, $password, $role);
    }

    public function login(string $email, string $password, ?bool $remember = false): array {
        $email = strtolower(trim($email));

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['ok'=>false, 'message'=>'Invalid credentials'];
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }

        $_SESSION['uid']   = (int)$user['id'];
        $_SESSION['role']  = $user['role'];
        $_SESSION['email'] = $user['email'];

        $tokenTtl = $remember ? 60*60*24*7 : null; // 1 week if remember, else JwtService default
        $token = $this->jwt->generate([
            'sub'   => (int)$user['id'],
            'role'  => $user['role'],
            'email' => $user['email']
        ], $tokenTtl);

        // Set JWT HttpOnly cookie (classic signature for older PHP)
        $this->setCookieClassic(
            'pms_jwt',
            $token,
            $remember ? (time() + 60*60*24*7) : 0, // 0 => session cookie
            $this->cookiePath,
            $this->cookieDomain,
            $this->forceSecureCookies,
            true
        );

        // Optional non-HttpOnly helper cookie
        $this->setCookieClassic(
            'pms_has_session',
            '1',
            time() + 60*60*24*30,
            $this->cookiePath,
            $this->cookieDomain,
            $this->forceSecureCookies,
            false
        );

        $publicUser = [
            'id'    => (int)$user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ];

        return ['ok'=>true, 'token'=>$token, 'user'=>$publicUser];
    }

    /**
     * Resolve current user in this order:
     * 1) PHP Session
     * 2) JWT Cookie (pms_jwt)
     */
    public function currentUserFromSessionOrJwt(): ?array {
        if (!empty($_SESSION['uid']) && !empty($_SESSION['role'])) {
            return [
                'id'    => (int)$_SESSION['uid'],
                'email' => $_SESSION['email'] ?? '',
                'role'  => $_SESSION['role']
            ];
        }

        $jwt = $_COOKIE['pms_jwt'] ?? '';
        if ($jwt) {
            $payload = $this->jwt->verify($jwt);
            if ($payload) {
                return [
                    'id'    => (int)$payload['sub'],
                    'email' => $payload['email'] ?? '',
                    'role'  => $payload['role'] ?? 'member'
                ];
            }
        }

        return null;
    }

    public function logout(): void {
        // Clear PHP session data
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Delete the session cookie (classic signature)
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
            session_destroy();
        }

        // Clear JWT-related cookies (classic signature)
        $this->setCookieClassic(
            'pms_jwt',
            '',
            time() - 3600,
            $this->cookiePath,
            $this->cookieDomain,
            $this->forceSecureCookies,
            true
        );
        $this->setCookieClassic(
            'pms_has_session',
            '',
            time() - 3600,
            $this->cookiePath,
            $this->cookieDomain,
            $this->forceSecureCookies,
            false
        );
    }

    // ----------------- helpers -----------------

    /**
     * Classic setcookie() wrapper that works on old PHP versions.
     * SameSite is not supported here; if you need it on modern PHP,
     * add a version check and use the array-options form.
     */
    private function setCookieClassic(
        string $name,
        string $value,
        int $expires,
        string $path,
        string $domain,
        bool $secure,
        bool $httpOnly
    ): void {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);
    }
}
