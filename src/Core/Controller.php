<?php

declare(strict_types=1);

namespace Nicastore\Core;

use Nicastore\Config\AppConfig;

abstract class Controller
{
    protected array $data = [];
    protected string $layout = 'main';
    
    public function __construct()
    {
        $this->data['app_name'] = AppConfig::get('APP_NAME', 'Nicastore');
        $this->data['csrf_token'] = $this->generateCsrfToken();
        $this->data['flash'] = Session::getFlash('flash');
        $this->data['user'] = Session::getUser();
    }
    
    protected function view(string $view, array $data = [], string $layout = null): void
    {
        $this->data = array_merge($this->data, $data);
        $layout = $layout ?? $this->layout;
        
        extract($this->data);
        
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        $layoutPath = __DIR__ . "/../Views/layouts/{$layout}.php";
        
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$viewPath}");
        }
        
        if ($layout && file_exists($layoutPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean();
            require $layoutPath;
        } else {
            require $viewPath;
        }
    }
    
    protected function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }
    
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? AppConfig::url('/');
        $this->redirect($referer);
    }
    
    protected function validateCsrf(): bool
    {
        $tokenName = AppConfig::csrfTokenName();
        $token = $_POST[$tokenName] ?? $_GET[$tokenName] ?? '';
        $sessionToken = Session::get('csrf_token');
        
        return !empty($token) && hash_equals($sessionToken, $token);
    }
    
    private function generateCsrfToken(): string
    {
        $tokenName = AppConfig::csrfTokenName();
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }
    
    protected function json(mixed $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}